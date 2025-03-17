<?php

namespace App\Services;

use App\Models\StandardRevision;
use App\Models\StandardSection;
use App\Models\Question;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Title;

/**
 * Example DocxParserService that extracts questions (including text formatting)
 * from a DOCX file. It searches for "æquestion_section_startæ" and "æquestion_section_endæ"
 * placeholders, parses the table in between, and saves the results.
 */
class DocxParserService
{
  protected $document;
  protected $standardRevision;

  // We'll store all standard sections & questions in these arrays, then save to DB.
  protected $standardSections = [];
  protected $questions = [];

  protected $currentStandardSection = null;
  protected $standardSectionOrder = 0;
  protected $debugMode = true; // Set to true to enable detailed logging

  // State flags for question block
  protected $inQuestionSectionBlock = false;
  protected $accumulatedQuestionTable = null;

  /**
   * Parse a DOCX file and extract the question table
   *
   * @param string $filePath Path to the DOCX file
   * @param StandardRevision $standardRevision The standard revision to associate with
   * @return bool Success status
   */
  public function parseDocx($filePath, StandardRevision $standardRevision)
  {
    try {
      if (!file_exists($filePath)) {
        $this->logError("DOCX parsing error: File does not exist at path: {$filePath}");
        return false;
      }

      if (!is_readable($filePath)) {
        $this->logError("DOCX parsing error: File is not readable at path: {$filePath}");
        return false;
      }

      $this->standardRevision = $standardRevision;
      $this->logInfo("Attempting to load DOCX file: {$filePath}");
      $this->logInfo("File size: " . filesize($filePath) . " bytes");

      $this->document = IOFactory::load($filePath);
      $this->logInfo("DOCX file loaded successfully");

      // Process the document to find question sections
      $this->processDocument();

      $this->logInfo("Extraction complete. Found " . count($this->standardSections) . " standard sections");
      $this->logInfo("Found " . count($this->questions) . " questions");

      // Save extracted data to database
      DB::beginTransaction();
      try {
        $this->saveExtractedData();
        DB::commit();
        $this->logInfo("Data saved to database successfully");
      } catch (Exception $e) {
        DB::rollBack();
        $this->logError("Database error: " . $e->getMessage());
        $this->logError($e->getTraceAsString());
        return false;
      }

      return true;
    } catch (Exception $e) {
      $this->logError('DOCX parsing error: ' . $e->getMessage());
      $this->logError('File path: ' . $filePath);
      $this->logError('Exception type: ' . get_class($e));
      $this->logError($e->getTraceAsString());
      session()->flash('error', 'Failed to parse the DOCX file: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Process the entire document's sections
   */
  protected function processDocument()
  {
    $sections = $this->document->getSections();
    $this->logInfo("Document has " . count($sections) . " sections");

    foreach ($sections as $index => $section) {
      $this->logInfo("Processing document section $index");
      $elements = $section->getElements();
      $this->logInfo("Section has " . count($elements) . " elements");
      $this->processElements($elements);
    }
  }

  /**
   * Look for placeholders "æquestion_section_startæ" and "æquestion_section_endæ"
   * in the text. Inside that block, parse the table that holds the questions.
   */
  protected function processElements($elements)
  {
    foreach ($elements as $index => $element) {
      $elementType = get_class($element);
      $text = $this->getElementText($element);

      $this->logInfo("Processing element $index of type: $elementType");

      // Check if we've hit the start of the question section
      if (Str::contains($text, 'æquestion_section_startæ')) {
        $this->inQuestionSectionBlock = true;
        $this->accumulatedQuestionTable = null;
        $this->logInfo("Detected æquestion_section_startæ");
        continue;
      }

      // If we're inside a question section, look for a table or the end
      if ($this->inQuestionSectionBlock) {
        // If element is a table, we accumulate it
        if ($element instanceof Table) {
          $this->accumulatedQuestionTable = $element;
          $this->logInfo("Accumulated question table detected");
        }

        // If we see the end placeholder, parse the table
        if (Str::contains($text, 'æquestion_section_endæ')) {
          $this->inQuestionSectionBlock = false;
          $this->logInfo("Detected æquestion_section_endæ");
          if ($this->accumulatedQuestionTable !== null) {
            $this->processQuestionTable($this->accumulatedQuestionTable);
          }
          continue;
        }
        continue;
      }
    }
  }

  /**
   * Process a single table containing the questions.
   * We'll skip the first row(s) if they're headers, then treat each row as:
   *   [0] => item_number
   *   [1] => question_text
   *   [2] => notes (optional)
   */
  protected function processQuestionTable(Table $table)
  {
    $rows = $table->getRows();
    $this->logInfo("Question table has " . count($rows) . " rows");

    if (count($rows) == 0) {
      $this->logInfo("No rows in question table, skipping");
      return;
    }

    // For example, let's assume row 0 is a big header, row 1 might be subheader, etc.
    // We'll create one standard section for the entire table. You can refine this logic if needed.
    $startRow = 2; // skip first 2 rows as "headers"
    $headerRow = $rows[0];
    $headerCells = $headerRow->getCells();
    $defaultClauseNumber = "Q";
    $defaultClauseTitle = "Question Section";
    if (count($headerCells) > 0) {
      // We'll store the text of the first cell as the section title
      $defaultClauseTitle = $this->getCellText($headerCells[0]);
    }

    // Create a single StandardSection for the entire table
    $this->createStandardSection($defaultClauseNumber, $defaultClauseTitle);

    // Now parse each row from startRow onward
    for ($i = $startRow; $i < count($rows); $i++) {
      $row = $rows[$i];
      $cells = $row->getCells();

      if (count($cells) < 2) {
        $this->logInfo("Row $i has fewer than 2 cells, skipping");
        continue;
      }

      // Extract plain text
      $itemNumber = $this->getCellText($cells[0]);
      $questionText = $this->getCellText($cells[1]);
      $notes = (count($cells) > 2) ? $this->getCellText($cells[2]) : '';

      // Extract detailed formatting
      echo $itemNumberFormatted   = $this->parseTableCell($cells[0]); // JSON of styled segments
      $questionTextFormatted = $this->parseTableCell($cells[1]);
      $notesFormatted        = (count($cells) > 2) ? $this->parseTableCell($cells[2]) : null;
      $this->logInfo("Item Number Formatted: " . $itemNumberFormatted);
      $this->logInfo("Question Text Formatted: " . $questionTextFormatted);
      $this->logInfo("Notes Formatted: " . $notesFormatted);
      $this->logInfo("Adding question from row $i: " . substr($questionText, 0, 30) . "...");

      // Add to $this->questions array
      $this->questions[] = [
        'standard_section_id'      => $this->currentStandardSection,
        'item_number'              => $itemNumber,
        'item_number_formatted'    => $itemNumberFormatted,    // JSON string
        'question_text'            => $questionText,
        'question_text_formatted'  => $questionTextFormatted,  // JSON string
        'question_type'            => 'text',
        'options'                  => null,
        'is_required'              => true,
        'notes'                    => $notes,
        'notes_formatted'          => $notesFormatted,         // JSON string
        'display_order'            => $this->getQuestionCount($this->currentStandardSection)
      ];
    }
  }

  /**
   * Create a new standard section array item.
   */
  protected function createStandardSection($clauseNumber, $clauseTitle)
  {
    $this->standardSections[] = [
      'standard_revision_id' => $this->standardRevision->id,
      'clause_number'        => $clauseNumber,
      'clause_title'         => $clauseTitle,
      'description'          => '',
      'display_order'        => $this->standardSectionOrder++,
      'is_mandatory'         => true
    ];
    $this->currentStandardSection = count($this->standardSections) - 1;
    $this->logInfo("Created standard section: $clauseNumber $clauseTitle (index: $this->currentStandardSection)");
  }

  /**
   * Parse all styled segments from a table cell into a JSON string.
   */
  protected function parseTableCell($cell)
  {
    $segments = $this->getCellFormattedContents($cell);
    return json_encode($segments);
  }

  /**
   * Recursively collect all styled segments in a cell.
   */
  protected function getCellFormattedContents($cell)
  {
    $result = [];

    foreach ($cell->getElements() as $element) {
      if ($element instanceof TextRun) {
        // a run can contain multiple Text elements
        $result = array_merge($result, $this->extractTextRunSegments($element));
      }
      elseif ($element instanceof Text) {
        $result[] = $this->getFormattedSegment($element);
      }
      elseif ($element instanceof Table) {
        // handle nested table or skip
        $plainText = $this->getTableText($element);
        if (!empty($plainText)) {
          $result[] = [
            'text'   => $plainText,
            'format' => []
          ];
        }
      }
      else {
        // fallback: store plain text or skip
        $plainText = $this->getElementText($element);
        if (!empty($plainText)) {
          $result[] = [
            'text'   => $plainText,
            'format' => []
          ];
        }
      }
    }

    return $result;
  }

  /**
   * Extract styled segments from a TextRun.
   */
  protected function extractTextRunSegments(TextRun $textRun)
  {
    $segments = [];
    foreach ($textRun->getElements() as $textElement) {
      if ($textElement instanceof Text) {
        $segments[] = $this->getFormattedSegment($textElement);
      }
      // If there are other element types in the run, handle similarly
    }
    return $segments;
  }

  /**
   * Convert a single Text element into an array with 'text' and 'format'.
   */
  protected function getFormattedSegment(Text $textElement)
  {
    $text  = $textElement->getText();
    $style = $textElement->getFontStyle();

    return [
      'text'   => $text,
      'format' => $this->extractTextFormatting($style)
    ];
  }

  /**
   * Extract bold, italic, underline, color, font size, etc. from a FontStyle.
   */
  protected function extractTextFormatting($style)
  {
    if (!$style) {
      return [];
    }

    $format = [];
    if ($style->getName())       $format['fontName']   = $style->getName();
    if ($style->getSize())       $format['fontSize']   = $style->getSize();
    if ($style->getColor())      $format['color']      = $style->getColor();
    if ($style->isBold())        $format['bold']       = true;
    if ($style->isItalic())      $format['italic']     = true;
    if ($style->getUnderline())  $format['underline']  = true;

    return $format;
  }

  /**
   * Return plain text from any element, ignoring style.
   */
  protected function getElementText($element)
  {
    if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
      return $element->getText();
    } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
      $runText = '';
      foreach ($element->getElements() as $child) {
        if ($child instanceof \PhpOffice\PhpWord\Element\Text) {
          $runText .= $child->getText();
        }
      }
      return $runText;
    }
    // fallback
    return '';
  }


  /**
   * Get text from a table cell.
   */
  protected function getCellText($cell)
  {
    $text = '';
    foreach ($cell->getElements() as $element) {
      $elementText = $this->getElementText($element);
      $text .= $elementText . " ";
    }
    return trim($text);
  }

  /**
   * Convert an entire table to plain text (used as fallback).
   */
  protected function getTableText(Table $table)
  {
    $text = '';
    foreach ($table->getRows() as $row) {
      $text .= $this->getRowText($row) . "\n";
    }
    return $text;
  }

  /**
   * Convert a single row to plain text, tab-separated cells.
   */
  protected function getRowText($row)
  {
    $rowText = '';
    foreach ($row->getCells() as $cell) {
      $cellText = $this->getElementText($cell);
      $rowText .= $cellText . "\t";
    }
    return rtrim($rowText, "\t");
  }

  /**
   * Count how many questions are in a section so far, for display_order.
   */
  protected function getQuestionCount($sectionIndex)
  {
    $count = 0;
    foreach ($this->questions as $q) {
      if ($q['standard_section_id'] === $sectionIndex) {
        $count++;
      }
    }
    return $count;
  }

  /**
   * Save the extracted data (sections + questions) to the database.
   */
  protected function saveExtractedData()
  {
    $this->logInfo("Saving extracted data to database");

    foreach ($this->standardSections as $index => $sectionData) {
      $this->logInfo("Saving standard section: " . $sectionData['clause_number'] . " " . $sectionData['clause_title']);
      $section = StandardSection::create($sectionData);

      $sectionQuestions = 0;
      foreach ($this->questions as $questionData) {
        if ($questionData['standard_section_id'] === $index) {
          // Link to the actual StandardSection ID
          $questionData['standard_section_id'] = $section->id;

          // If 'notes' column doesn't exist, append notes to question_text
          $hasNotesField = \Schema::hasColumn('questions', 'notes');
          if (!$hasNotesField && isset($questionData['notes'])) {
            $notes = $questionData['notes'];
            unset($questionData['notes']);
            if (!empty($notes)) {
              $questionData['question_text'] .= "\n\n" . $notes;
            }
          }

          // JSON verisini kontrol edin
          if (is_null($questionData['item_number_formatted'])) {
            $this->logError("Item number formatted is null for question: " . $questionData['item_number']);
          }
          if (is_null($questionData['question_text_formatted'])) {
            $this->logError("Question text formatted is null for question: " . $questionData['item_number']);
          }
          if (is_null($questionData['notes_formatted'])) {
            $this->logError("Notes formatted is null for question: " . $questionData['item_number']);
          }

          $this->logInfo("Saving question for section " . $sectionData['clause_number'] . ": " . substr($questionData['question_text'], 0, 30) . "...");
          Question::create($questionData);
          $sectionQuestions++;
        }
      }
      $this->logInfo("Saved $sectionQuestions questions for section " . $sectionData['clause_number']);
    }

    $this->logInfo("All data saved successfully");
  }

  /**
   * Logging helpers
   */
  protected function logInfo($message)
  {
    $this->logToFile($message);
    if ($this->debugMode) {
      Log::info('[DocxParser] ' . $message);
    }
  }

  protected function logError($message)
  {
    $this->logToFile($message, true);
    Log::error('[DocxParser] ' . $message);
  }

  protected function logToFile($message, $isError = false)
  {
    $logFile = storage_path('logs/docx_parser.log');
    $prefix = $isError ? '[ERROR] ' : '[INFO] ';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $prefix$message\n", FILE_APPEND);
  }
}
