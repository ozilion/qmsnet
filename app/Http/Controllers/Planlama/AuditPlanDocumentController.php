<?php

namespace App\Http\Controllers\Planlama;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Cell;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Exception;

class AuditPlanDocumentController extends Controller
{
  /**
   * Generate and download the audit plan as a Word document
   *
   * @param string $pno
   * @param string $asama
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function generateAuditPlanDocument($pno, $asama, Request $request)
  {
    // Get the format from the request
    $format = $request->input('format', 'docx');
    $includeParticipants = $request->input('include_participants', false);

    // Get the audit plan data
    $auditPlan = DB::table('audit_plan')
      ->where('planno', $pno)
      ->where('asama', $asama)
      ->first();

    if (!$auditPlan || !$auditPlan->rows) {
      return response()->json(['error' => 'Denetim planı bulunamadı.'], 404);
    }

    // Get other necessary data
    $plan = DB::table('planlar')
      ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
      ->select('basvuru.*', 'planlar.*')
      ->where('planlar.planno', $pno)
      ->first();

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

    $patia1 = $pati . '/' . $asamaArr[$asama];

    // Ensure directory exists
    if (!file_exists($patia1)) {
      mkdir($patia1, 0755, true);
    }

    // Template and output file names
    $templateFile = $patia1 . '/AFR.07DenetimPlani-R8_temp.docx';

    // Generate unique output filename with timestamp
    $timestamp = date('YmdHis');
    $outputFile = $patia1 . '/AFR.07 Denetim Plani-R8.docx';

    // Check if template exists
    if (!file_exists($templateFile)) {
      return response()->json(['error' => 'Şablon dosyası bulunamadı: ' . $templateFile], 404);
    }

    // Parse the rows data
    $rows = json_decode($auditPlan->rows, true);

    // Preprocess rows to identify departments with multiple standards
    $processedRows = $this->preprocessRows($rows);

    try {
      // Load the template
      $template = new TemplateProcessor($templateFile);

      // Set macro characters if needed
      if (method_exists($template, 'setMacroOpeningChars')) {
        $template->setMacroOpeningChars("æ");
        $template->setMacroClosingChars("æ");
      }

      // Get the date from the plan
      $columns = [
        'asama1'   => ['date' => 'asama1'],
        'asama2'   => ['date' => 'asama2'],
        'gozetim1' => ['date' => 'gozetim1'],
        'gozetim2' => ['date' => 'gozetim2'],
        'ybtar'    => ['date' => 'ybtar'],
        'ozeltar'  => ['date' => 'ozeltar'],
      ];
      $column = $columns[$asama] ?? null;
      $date = $plan->{$column['date']} ?? date('d.m.Y');

      // Create a temporary file to store the table
      $tempFile = tempnam(sys_get_temp_dir(), 'tbl');

      // Generate the table in a new PHPWord document
      $phpWord = new PhpWord();
      $section = $phpWord->addSection();

      // Create the table
      $this->createAuditPlanTable($section, $processedRows, $date);

      // Save the temporary document
      $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
      $objWriter->save($tempFile);

      // Read the generated file
      $tempDocx = new \ZipArchive();
      if ($tempDocx->open($tempFile) === true) {
        $tempXml = $tempDocx->getFromName('word/document.xml');
        $tempDocx->close();

        // Extract table XML
        preg_match('/<w:tbl>.*?<\/w:tbl>/s', $tempXml, $matches);
        $tableXml = $matches[0] ?? '';

        if (empty($tableXml)) {
          throw new Exception('Denetim plan tablosu oluşturulamadı.');
        }

        // Replace placeholder with table XML
        $template->setValue('denetimplani', $tableXml);

        // Save the document
        $template->saveAs($outputFile);

        // Clean up temporary file
        unlink($tempFile);

        // Determine the content type based on format
        $contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

        // If PDF format was requested, convert the DOCX to PDF using PHPWord's functionality
        if ($format === 'pdf') {
          $pdfOutputFile = str_replace('.docx', '.pdf', $outputFile);

          // Load the generated DOCX
          $phpWord = \PhpOffice\PhpWord\IOFactory::load($outputFile);

          // Save it as PDF
          $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
          $pdfWriter->save($pdfOutputFile);

          // Update content type and output file for the response
          $contentType = 'application/pdf';
          $outputFile = $pdfOutputFile;
        }

        // Return the document for download
        return response()->download($outputFile, basename($outputFile), [
          'Content-Type' => $contentType,
          'Content-Disposition' => 'attachment; filename="' . basename($outputFile) . '"'
        ])->deleteFileAfterSend(true);
      } else {
        throw new Exception('Geçici dosya açılamadı.');
      }
    } catch (Exception $e) {
      // Clean up temporary file if it exists
      if (isset($tempFile) && file_exists($tempFile)) {
        unlink($tempFile);
      }

      return response()->json([
        'error' => 'Döküman oluşturulurken bir hata oluştu: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get participants for the audit
   *
   * @param string $pno
   * @return string
   */
  private function getParticipants($pno) {
    // This is a placeholder - you should implement your logic to fetch participants
    // from your database based on the plan number

    // For now, we'll return some example text
    return "Firma Temsilcileri:\n- Genel Müdür\n- Kalite Sistem Sorumlusu\n\nDenetim Ekibi:\n- Baş Denetçi\n- Denetçi";
  }

  /**
   * Create the audit plan table in the document
   *
   * @param \PhpOffice\PhpWord\Element\Section $section
   * @param array $rows
   * @param string $date
   * @return void
   */
  private function createAuditPlanTable($section, $rows, $date)
  {
    // Convert 17.75 cm to twips (1 cm = 567 twips)
    $tableWidthTwips = 17.75 * 567;

    // Define table style
    $tableStyle = [
      'borderSize' => 6,
      'borderColor' => '000000',
      'cellMargin' => 80,
      'width' => $tableWidthTwips,
      'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP,
      'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
    ];

    // Define text style with Arial font - normal weight for all text
    $textStyle = [
      'name' => 'Arial',
      'size' => 10,
      'bold' => false,
    ];

    // Define cell style
    $cellStyle = ['valign' => 'center'];
    $cellHeaderStyle = ['valign' => 'center', 'bgColor' => 'F2F2F2'];

    // Create the table
    $table = $section->addTable($tableStyle);

    // Calculate column widths based on the total table width
    $columnWidths = [
      'time' => $tableWidthTwips * 0.08,     // 8%
      'department' => $tableWidthTwips * 0.20,  // 20%
      'team' => $tableWidthTwips * 0.27,     // 27%
      'standard' => $tableWidthTwips * 0.20,  // 20%
      'maddeNo' => $tableWidthTwips * 0.25   // 25%
    ];

    // Add date header row
    $table->addRow();
    $table->addCell($columnWidths['time'], $cellHeaderStyle)->addText('Tarih', $textStyle, ['alignment' => 'center']);
    $cell = $table->addCell(null, array_merge($cellHeaderStyle, ['gridSpan' => 4]));
    $cell->addText($date, $textStyle, ['alignment' => 'center']);

    // Add column headers
    $table->addRow();
    $table->addCell($columnWidths['time'], $cellHeaderStyle)->addText('Saat', $textStyle, ['alignment' => 'center']);
    $table->addCell($columnWidths['department'], $cellHeaderStyle)->addText('Departman/ Proses/Saha', $textStyle, ['alignment' => 'center']);
    $table->addCell($columnWidths['team'], $cellHeaderStyle)->addText('Denetim Ekibi', $textStyle, ['alignment' => 'center']);
    $table->addCell($columnWidths['standard'], $cellHeaderStyle)->addText('Standard', $textStyle, ['alignment' => 'center']);
    $table->addCell($columnWidths['maddeNo'], $cellHeaderStyle)->addText('Standard Madde No', $textStyle, ['alignment' => 'center']);

    // Process the rows in the correct order

    // First, add fixed rows: opening meeting and short tour
    if (isset($rows['0'])) {
      $this->addFixedRowToTable($table, $rows['0'], 'Açılış Toplantısı', $cellStyle, $textStyle, $columnWidths);
    }

    if (isset($rows['1'])) {
      $this->addFixedRowToTable($table, $rows['1'], 'Kısa Tur', $cellStyle, $textStyle, $columnWidths);
    }

    // Then add numeric rows in order
    $numericRows = [];
    foreach ($rows as $key => $row) {
      if (is_numeric($key) && $key >= 2) {
        $numericRows[$key] = $row;
      }
    }

    // Sort by numeric key
    ksort($numericRows);

    foreach ($numericRows as $key => $row) {
      $this->addContentRowToTable($table, $row, $cellStyle, $textStyle, $columnWidths);
    }

    // Add merged rows if any
    if (isset($rows['merged'])) {
      $this->addContentRowToTable($table, $rows['merged'], $cellStyle, $textStyle, $columnWidths);
    }

    // Add lunch break if any
    if (isset($rows['ogle'])) {
      $this->addFixedRowToTable($table, $rows['ogle'], 'Öğle Arası', $cellStyle, $textStyle, $columnWidths);
    }

    // Add evaluation if any
    if (isset($rows['deg'])) {
      $this->addFixedRowToTable($table, $rows['deg'], 'Değerlendirme', $cellStyle, $textStyle, $columnWidths);
    }

    // Add closing meeting if any
    if (isset($rows['kap'])) {
      $this->addFixedRowToTable($table, $rows['kap'], 'Kapanış Toplantısı', $cellStyle, $textStyle, $columnWidths);
    }
  }

  /**
   * Add a fixed row to the table (opening, closing, lunch, etc.)
   *
   * @param \PhpOffice\PhpWord\Element\Table $table
   * @param array $rowData
   * @param string $title
   * @param array $cellStyle
   * @param array $textStyle
   * @param array $columnWidths
   * @return void
   */
  private function addFixedRowToTable($table, $rowData, $title, $cellStyle, $textStyle, $columnWidths)
  {
    $table->addRow();

    // Add the time cell
    $timeCell = $table->addCell($columnWidths['time'], $cellStyle);
    $timeCell->addText($this->formatTime($rowData['start'] ?? ''), $textStyle, ['alignment' => 'center']);
    $timeCell->addText($this->formatTime($rowData['end'] ?? ''), $textStyle, ['alignment' => 'center']);

    // Add the title cell spanning 4 columns
    $combinedWidth = $columnWidths['department'] + $columnWidths['team'] +
      $columnWidths['standard'] + $columnWidths['maddeNo'];

    $titleCell = $table->addCell($combinedWidth, array_merge($cellStyle, ['gridSpan' => 4]));
    $titleCell->addText($title, $textStyle, ['alignment' => 'center']);
  }

  /**
   * Add a content row to the table
   *
   * @param \PhpOffice\PhpWord\Element\Table $table
   * @param array $rowData
   * @param array $cellStyle
   * @param array $textStyle
   * @param array $columnWidths
   * @return void
   */
  private function addContentRowToTable($table, $rowData, $cellStyle, $textStyle, $columnWidths)
  {
    $table->addRow();

    // Add the time cell
    $timeCell = $table->addCell($columnWidths['time'], $cellStyle);
    $timeCell->addText($this->formatTime($rowData['start'] ?? ''), $textStyle, ['alignment' => 'center']);
    $timeCell->addText($this->formatTime($rowData['end'] ?? ''), $textStyle, ['alignment' => 'center']);

    // Add the department cell
    $table->addCell($columnWidths['department'], $cellStyle)->addText($rowData['department'] ?? '', $textStyle, ['alignment' => 'left']);

    // Add the team cell
    $table->addCell($columnWidths['team'], $cellStyle)->addText($rowData['team'] ?? '', $textStyle, ['alignment' => 'center']);

    // Add the standard cell
    $table->addCell($columnWidths['standard'], $cellStyle)->addText($rowData['standard'] ?? '', $textStyle, ['alignment' => 'center']);

    // Add the madde_no cell
    $maddeNoText = $this->formatMaddeNo($rowData['madde_no'] ?? '');
    $table->addCell($columnWidths['maddeNo'], $cellStyle)->addText($maddeNoText, $textStyle, ['alignment' => 'left']);

    // Process additional standards for this department (if they exist)
    $additionalStandards = $rowData['additional_standards'] ?? [];

    foreach ($additionalStandards as $addStandard) {
      $standardName = $addStandard['standard'] ?? '';
      $standardMaddeNoText = $this->formatMaddeNo($addStandard['madde_no'] ?? '');

      $table->addRow();

      // Empty time cell
      $table->addCell($columnWidths['time'], $cellStyle)->addText('', $textStyle, ['alignment' => 'center']);

      // Empty department cell
      $table->addCell($columnWidths['department'], $cellStyle)->addText('', $textStyle, ['alignment' => 'left']);

      // Empty team cell
      $table->addCell($columnWidths['team'], $cellStyle)->addText('', $textStyle, ['alignment' => 'center']);

      // Standard cell
      $table->addCell($columnWidths['standard'], $cellStyle)->addText($standardName, $textStyle, ['alignment' => 'center']);

      // Madde no cell
      $table->addCell($columnWidths['maddeNo'], $cellStyle)->addText($standardMaddeNoText, $textStyle, ['alignment' => 'left']);
    }
  }

  /**
   * Pre-process the rows to identify and handle multiple standards for the same department
   *
   * @param array $rows
   * @return array
   */
  private function preprocessRows($rows)
  {
    $processedRows = [];
    $departmentGroups = [];

    // First, keep special rows as they are
    foreach ($rows as $key => $row) {
      if (!is_numeric($key) || $key < 2) {
        $processedRows[$key] = $row;
      }
    }

    // Then organize rows by department, start time, and end time
    foreach ($rows as $key => $row) {
      if (!is_numeric($key) || $key < 2) {
        // Already processed above
        continue;
      }

      $department = $row['department'] ?? '';
      $start = $row['start'] ?? '';
      $end = $row['end'] ?? '';
      $team = $row['team'] ?? '';

      // Skip empty rows
      if (empty($department) && empty($start) && empty($end)) {
        continue;
      }

      // If this row already has additional_standards, keep as is
      if (isset($row['additional_standards']) && is_array($row['additional_standards']) && !empty($row['additional_standards'])) {
        $processedRows[$key] = $row;
        continue;
      }

      // Create a unique key for this department and time slot
      $groupKey = md5($department . '-' . $start . '-' . $end . '-' . $team);

      if (!isset($departmentGroups[$groupKey])) {
        $departmentGroups[$groupKey] = [
          'key' => $key,
          'department' => $department,
          'start' => $start,
          'end' => $end,
          'team' => $team,
          'standards' => []
        ];
      }

      // Add the standard and madde_no to this group
      $departmentGroups[$groupKey]['standards'][] = [
        'standard' => $row['standard'] ?? '',
        'madde_no' => $row['madde_no'] ?? ''
      ];
    }

    // Now convert the grouped data back to rows format
    foreach ($departmentGroups as $group) {
      if (empty($group['standards'])) {
        continue;
      }

      $key = $group['key'];
      $firstStandard = array_shift($group['standards']); // Remove the first standard

      $processedRows[$key] = [
        'department' => $group['department'],
        'start' => $group['start'],
        'end' => $group['end'],
        'team' => $group['team'],
        'standard' => $firstStandard['standard'],
        'madde_no' => $firstStandard['madde_no'],
        'additional_standards' => $group['standards'] // Store remaining standards
      ];
    }

    return $processedRows;
  }

  /**
   * Format madde_no value
   *
   * @param mixed $maddeNo
   * @return string
   */
  private function formatMaddeNo($maddeNo)
  {
    if (empty($maddeNo)) {
      return '';
    }

    if (is_array($maddeNo)) {
      return implode(', ', $maddeNo);
    }

    // Try to decode if it's a JSON string
    try {
      $maddeNoArray = json_decode($maddeNo, true);
      if (is_array($maddeNoArray)) {
        return implode(', ', $maddeNoArray);
      }
    } catch (Exception $e) {
      // Not JSON, continue
    }

    return $maddeNo;
  }

  /**
   * Format time value
   *
   * @param string $time
   * @return string
   */
  private function formatTime($time)
  {
    if (empty($time)) {
      return '';
    }

    // Handle '--:--' placeholder
    if ($time === '--:--') {
      return '--:--';
    }

    // Already in HH:MM format
    if (preg_match('/^\d{2}:\d{2}$/', $time)) {
      return $time;
    }

    // Try to convert to time format
    try {
      return date('H:i', strtotime($time));
    } catch (Exception $e) {
      return $time;
    }
  }

  /**
   * Get standard madde numbers for a given standard and stage
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */

  public function auditPlanMaddeNos(Request $request)
  {
    // İstekten 'standard' ve 'asama' parametrelerini alıyoruz.
    $standard = $request->input('standard');
    $asama = $request->input('asama');

    // Veritabanındaki 'denetim_programi' tablosundan standart ve asama eşleşen kayıtların madde_no kolonunu alıyoruz.
    // Örneğin: standard sütunu "ISO 9001:2015", asama sütunu "asama1" olan kayıtların madde_no değeri
    $maddeNos = DB::table('denetim_programi')
      ->where('standart', $standard)
      ->where('asama', $asama)
      ->pluck('madde_no');

    // Eğer madde_no verileri virgülle ayrılmış ise (örn. "4.1,4.2,4.3"), bunları parçalayıp düz bir dizi haline getirebiliriz:
    $result = [];
    foreach ($maddeNos as $madde) {
      // Virgülle ayrılmış madde numaralarını parçalayalım
      $numbers = array_map('trim', explode(',', $madde));
      $result = array_merge($result, $numbers);
    }
    // Benzersiz değerler döndürelim
    $result = array_values(array_unique($result));

    return response()->json($result);
  }

  /**
   * Display the audit plan form
   *
   * @param Request $request
   * @return \Illuminate\Contracts\View\View
   */

  public function auditPlan(Request $request)
  {
    $kid = Auth::user()->kurulusid;
    if (intval($kid) < 0) {
      return view('content.planlama.dashboards-plan', ['kiderror' => 'Seçili kuruluşa ait bilgiler alınamadı.']);
    }

    $audit = DB::table('audit_plan')
      ->where('asama', '=', $request->asama, 'and')
      ->where('planno', '=', $request->pno)
      ->first();

    $basvuru = DB::table('basvuru')
      ->where('kid', '=', $kid, 'and')
      ->where('planno', '=', $request->pno)
      ->first();

    $plan = DB::table('planlar')
      ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
      ->select('basvuru.*', 'planlar.*')
      ->where('basvuru.planno', '=', $request->pno, 'and')
      ->where('basvuru.kid', '=', $kid, 'and')
      ->where('planlar.planno', '=', $request->pno, 'and')
      ->where('planlar.kid', '=', $kid)
      ->first();

    $smiictablo = DB::select('SELECT * FROM clssmiic ORDER BY id ASC');

    return view('content.planlama.audit-plan', [
      'basvuru' => $basvuru,
      'plan' => $plan,
      'audit' => $audit,
      'asama' => $request->asama,
      'pno' => $request->pno,
    ]);
  }

  /**
   * Save audit plan data with support for grouped standards
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function saveAuditPlan(Request $request)
  {
    try {
      // Validate input
      $validator = Validator::make($request->all(), [
        'pno' => 'required',
        'asama' => 'required',
        'rows' => 'required|array',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'Validasyon hatası',
          'errors' => $validator->errors()
        ], 422);
      }

      // Get the data
      $pno = $request->input('pno');
      $asama = $request->input('asama');
      $rows = $request->input('rows');

      // Check if we have grouped data
      $groupedData = $request->input('groupedData');
      if ($groupedData) {
        $groupedData = json_decode($groupedData, true);

        // Process the grouped data to update rows
        if (is_array($groupedData)) {
          $this->processGroupedData($rows, $groupedData);
        }
      }

      // Begin transaction
      DB::beginTransaction();

      // Check if there's an existing record
      $existingRecord = DB::table('audit_plan')
        ->where('planno', $pno)
        ->where('asama', $asama)
        ->first();

      if ($existingRecord) {
        // Update existing record
        DB::table('audit_plan')
          ->where('planno', $pno)
          ->where('asama', $asama)
          ->update([
            'rows' => json_encode($rows),
            'updated_at' => now()
          ]);
      } else {
        // Insert new record
        DB::table('audit_plan')->insert([
          'planno' => $pno,
          'asama' => $asama,
          'rows' => json_encode($rows),
          'created_at' => now(),
          'updated_at' => now()
        ]);
      }

      // Commit transaction
      DB::commit();

      // Return success response
      return response()->json([
        'success' => true,
        'message' => 'Denetim planı başarıyla kaydedildi.'
      ]);

    } catch (Exception $e) {
      // Rollback transaction on error
      DB::rollBack();

      // Return error response
      return response()->json([
        'success' => false,
        'message' => 'Denetim planı kaydedilirken bir hata oluştu: ' . $e->getMessage()
      ], 500);
    }
  }
}
