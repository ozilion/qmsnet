<?php

namespace App\Services;

use App\Models\Audit;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ChecklistExportService
{
  /**
   * Export the audit checklist to a DOCX file using a template.
   *
   * @param Audit $audit
   * @param string|null $asama The audit stage (asama1, asama2, gozetim1, gozetim2, ybtar, ozeltar)
   * @return string Path to the generated file
   * @throws \Exception
   */
  public function exportToDocx(Audit $audit, $asama = null)
  {
    // If asama is not provided, determine it based on audit_type
    if ($asama === null) {
      $asama = $this->determineAuditStage($audit);
    }

    $pno = $audit->plan_no;

    // If no plan number is set, return an error
    if (empty($pno)) {
      Log::error("No plan number set for audit ID: " . $audit->id);
      throw new \Exception("No plan number specified for this audit.");
    }

    // Set up directory paths
    $dizin = public_path();
    $pati = $dizin . '/setler/' . str_pad($pno, 4, '0', STR_PAD_LEFT);

    $asamaArr = [
      'asama1' => 'I. ASAMA',
      'asama2' => 'II. ASAMA',
      'gozetim1' => 'I. GOZETIM',
      'gozetim2' => 'II. GOZETIM',
      'ybtar' => 'YenidenBelgelendirme',
      'ozeltar' => 'OZEL TETKIK',
    ];

    // Validate if asama is valid
    if (!array_key_exists($asama, $asamaArr)) {
      Log::error("Invalid audit stage: $asama");
      throw new \Exception("Invalid audit stage specified: $asama");
    }

    $patia1 = $pati . '/' . $asamaArr[$asama];

    // Ensure directory exists
    if (!file_exists($patia1)) {
      if (!mkdir($patia1, 0755, true)) {
        Log::error("Failed to create directory: $patia1");
        throw new \Exception("Failed to create directory for export: $patia1");
      }
    }

    // Template and output file names
    $templateFile = $patia1 . '/AFR.09DenetimRaporu-R8_temp.docx';  //required don't touch
    $outputFile = $patia1 . '/AFR.09 Denetim Raporu R0.docx';  //required don't touch

    // Check if template exists
    if (!file_exists($templateFile)) {
      Log::error("Template file not found: $templateFile");
      throw new \Exception("Template file not found: $templateFile");
    }

    try {
      // Create a temporary file with the table using native PhpWord
      $tempTableFile = $this->createTableDocx($audit);

      // Now use the template processor to insert this table into the main document
      $templateProcessor = new TemplateProcessor($templateFile);
      $templateProcessor->setMacroClosingChars("æ");  //required don't touch
      $templateProcessor->setMacroOpeningChars("æ"); //required don't touch

      // Extract the table content from the temporary file
      $tableContent = $this->extractTableFromDocx($tempTableFile);

      // Replace the placeholder with the table content
      $templateProcessor->setValue('STANDARD_TABLE', $tableContent);

      // Save the document
      $templateProcessor->saveAs($outputFile);

      // Delete the temporary file
      if (file_exists($tempTableFile)) {
        unlink($tempTableFile);
      }

      return $outputFile;
    } catch (\Exception $e) {
      Log::error('Error generating DOCX: ' . $e->getMessage());
      throw new \Exception("Failed to generate DOCX: " . $e->getMessage());
    }
  }

  /**
   * Create a temporary DOCX file containing just the table
   *
   * @param Audit $audit
   * @return string Path to the temporary file
   */
  private function createTableDocx(Audit $audit)
  {
    // Create a new Word document
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();

    // Add audit information
    $standardName = $audit->standardRevision->standard->code . ":" . $audit->standardRevision->standard->version;
    $tableWidthTwips = 17.75 * 567;

    // Create table
    $table = $section->addTable([
      'borderSize' => 1,
      'borderColor' => '000000',
      'width' => $tableWidthTwips,
      'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP,
      'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
    ]);

    $textStyleHeader = [
      'name' => 'Arial',
      'size' => 8,
      'bold' => true,
    ];

    $textStyle = [
      'name' => 'Arial',
      'size' => 8,
      'bold' => false,
    ];

    // Row heights
    $rowHeight = 567 * 0.72; // 0.72 cm in twips

    // Column widths in cm converted to twips
    $colWidths = [
      567 * 1.32,  // 1.32 cm - Madde no
      567 * 8.09,  // 8.09 cm - Gereklilikler
      567 * 7.75   // 7.75 cm - Bulgular
    ];

    // Row 1: Standard name header
    $table->addRow($rowHeight);
    $cell = $table->addCell(null, ['gridSpan' => 3, 'valign' => 'center', 'bgColor' => 'F2F2F2']);
    $cell->addText($standardName, $textStyleHeader, ['alignment' => 'center']);

    // Row 2: Column headers
    $table->addRow($rowHeight);
    $table->addCell($colWidths[1], ['gridSpan' => 2, 'valign' => 'center', 'bgColor' => 'F2F2F2'])->addText('Gereklilikler', $textStyleHeader, ['alignment' => 'center']);
    $table->addCell($colWidths[2], ['valign' => 'center', 'bgColor' => 'F2F2F2'])->addText('Bulgular', $textStyleHeader, ['alignment' => 'center']);

    // Get responses keyed by question id for lookup
    $responses = $audit->responses->keyBy('question_id');

    // Add rows for each section and its questions
    foreach ($audit->standardRevision->standardSections as $section) {
      foreach ($section->questions as $question) {
        $table->addRow();

        // First cell: Item number
        $itemNumberCell = $table->addCell($colWidths[0]);
        if (!empty($question->item_number_formatted)) {
          $this->applyFormatting($itemNumberCell, $question->item_number, $question->item_number_formatted);
        } else {
          $itemNumberCell->addText($question->item_number ?? $section->clause_number, $textStyle);
        }

        // Second cell: Question text
        $questionTextCell = $table->addCell($colWidths[1]);
        if (!empty($question->question_text_formatted)) {
          $this->applyFormatting($questionTextCell, $question->question_text, $question->question_text_formatted);
        } else {
          $questionTextCell->addText($question->question_text, $textStyle);
        }

        // Third cell: Response and evidence
        $responseCell = $table->addCell($colWidths[2]);
        $response = $responses->get($question->id);

        if ($response) {
          if (!empty($response->response_text)) {
            $responseCell->addText($response->response_text, $textStyle);
          }

          if (!empty($response->evidence)) {
            if (!empty($response->response_text)) {
              $responseCell->addTextBreak();
            }
            $responseCell->addText('Evidence: ' . $response->evidence, $textStyle);
          }
        } else if (!empty($question->notes_formatted)) {
          $this->applyFormatting($responseCell, $question->notes, $question->notes_formatted);
        } else if (!empty($question->notes)) {
          $responseCell->addText($question->notes);
        } else {
          $responseCell->addText('');
        }
      }
    }

    // Save to temporary file
    $tempFile = storage_path('app/temp/audit_table_' . $audit->id . '_' . time() . '.docx');

    // Ensure temp directory exists
    if (!file_exists(storage_path('app/temp'))) {
      mkdir(storage_path('app/temp'), 0755, true);
    }

    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($tempFile);

    return $tempFile;
  }
  /**
   * Apply formatting to a cell based on stored JSON formatting
   */
  private function applyFormatting($cell, $text, $formattedData = null)
  {
    if (empty($formattedData)) {
      $cell->addText($text); // Default formatting
      return;
    }

    try {
      $formattedText = json_decode($formattedData, true);

      if (is_array($formattedText) && isset($formattedText['text'])) {
        // Single formatted text element
        $format = $formattedText['format'] ?? [];
        $this->addFormattedText($cell, $formattedText['text'], $format);
      }
      else if (is_array($formattedText) && is_array(reset($formattedText))) {
        // Multiple formatted text elements
        foreach ($formattedText as $part) {
          if (isset($part['text'])) {
            $format = $part['format'] ?? [];
            $this->addFormattedText($cell, $part['text'], $format);
          }
        }
      }
      else {
        // Fallback
        $cell->addText($text);
      }
    } catch (\Exception $e) {
      // Log error and fallback to plain text
      Log::error("Error applying formatting: " . $e->getMessage());
      $cell->addText($text);
    }
  }

  /**
   * Add text with specific formatting to a cell
   */
  private function addFormattedText($cell, $text, $format = [])
  {
    $fontStyle = [];
    $paragraphStyle = [];

    // Apply font formatting
    if (!empty($format['fontName'])) $fontStyle['name'] = $format['fontName'];
    if (!empty($format['fontSize'])) $fontStyle['size'] = $format['fontSize'];
    if (!empty($format['color'])) $fontStyle['color'] = $format['color'];
    if (!empty($format['bold'])) $fontStyle['bold'] = true;
    if (!empty($format['italic'])) $fontStyle['italic'] = true;
    if (!empty($format['underline'])) $fontStyle['underline'] = true;

    // Apply default font if none specified
    if (empty($fontStyle['name'])) $fontStyle['name'] = 'Arial';

    $cell->addText($text, $fontStyle, $paragraphStyle);
  }

  /**
   * Extract table content from a DOCX file to insert into the template
   *
   * @param string $docxPath
   * @return string XML content of the table
   */
  private function extractTableFromDocx($docxPath)
  {
    // Create a temporary directory
    $tempDir = storage_path('app/temp/extract_' . time());
    if (!file_exists($tempDir)) {
      mkdir($tempDir, 0755, true);
    }

    // Extract the DOCX (which is a ZIP file)
    $zip = new \ZipArchive();
    if ($zip->open($docxPath) === TRUE) {
      $zip->extractTo($tempDir);
      $zip->close();

      // Get the document.xml file containing the table
      $documentXmlPath = $tempDir . '/word/document.xml';
      if (file_exists($documentXmlPath)) {
        $xml = file_get_contents($documentXmlPath);

        // Extract just the table XML
        if (preg_match('/<w:tbl>.*<\/w:tbl>/s', $xml, $matches)) {
          // Clean up the temp directory
          $this->removeDirectory($tempDir);

          return $matches[0];
        }
      }

      // Clean up the temp directory
      $this->removeDirectory($tempDir);
    }

    throw new \Exception("Failed to extract table from temporary DOCX file");
  }

  /**
   * Remove a directory and all its contents
   *
   * @param string $dir
   */
  private function removeDirectory($dir)
  {
    if (!is_dir($dir)) {
      return;
    }

    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object == "." || $object == "..") continue;

      if (is_dir($dir . "/" . $object)) {
        $this->removeDirectory($dir . "/" . $object);
      } else {
        unlink($dir . "/" . $object);
      }
    }

    rmdir($dir);
  }

  /**
   * Determine the audit stage based on the audit type
   *
   * @param Audit $audit
   * @return string The stage identifier (asama1, asama2, etc.)
   */
  private function determineAuditStage(Audit $audit)
  {
    // Map audit types to stages
    switch ($audit->audit_type) {
      case 'Initial':
        return 'asama1';
      case 'Surveillance':
        // Check if this is the first or second surveillance
        // For now we'll default to first surveillance
        return 'gozetim1';
      case 'Recertification':
        return 'ybtar';
      case 'Special':
        return 'ozeltar';
      default:
        return 'asama1'; // Default to first stage
    }
  }
}
