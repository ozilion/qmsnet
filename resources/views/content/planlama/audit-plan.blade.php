@extends('layouts/layoutMaster')

@section('title', '[' . $pno . '] Denetim Planı | ' . $asama . " | " . $plan->firmaadi)

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}"/>
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{asset('assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
@endsection

<?php
$pot = $plan;
$kyssistemler = \App\Helpers\Helpers::getSistemler($pot);
$oicsistemler = \App\Helpers\Helpers::getOicSistemler($pot);
if ($kyssistemler !== "" && $oicsistemler !== "") {
  $belgelendirileceksistemler = $kyssistemler . ", " . $oicsistemler;
}
if ($kyssistemler === "" && $oicsistemler !== "") {
  $belgelendirileceksistemler = $oicsistemler;
}
if ($kyssistemler !== "" && $oicsistemler === "") {
  $belgelendirileceksistemler = $kyssistemler;
}
$columns = [
  'asama1' => ['cols' => ['bd1', 'd1', 'tu1', 'g1', 'ad1', 'sid1'], 'date' => 'asama1'],
  'asama2' => ['cols' => ['bd2', 'd2', 'tu2', 'g2', 'ad2', 'sid2'], 'date' => 'asama2'],
  'gozetim1' => ['cols' => ['gbd1', 'gd1', 'gtu1', 'gg1', 'adg1', 'sidg1'], 'date' => 'gozetim1'],
  'gozetim2' => ['cols' => ['gbd2', 'gd2', 'gtu2', 'gg2', 'adg2', 'sidg2'], 'date' => 'gozetim2'],
  'ybtar' => ['cols' => ['ybbd', 'ybd', 'ybtu', 'ybg', 'adyb', 'sidyb'], 'date' => 'ybtar'],
  'ozeltar' => ['cols' => ['otbd', 'otd', 'ottu', 'otg', 'adot', 'sidot'], 'date' => 'ozeltar'],
];
$column = $columns[$asama] ?? null;
// Standart isimlerini virgül ile ayrılmış olarak alıp, boşlukları temizleyelim:
$standards = array_map('trim', explode(',', $belgelendirileceksistemler));

// Denetim ekibi için; BD hariç diğer kolonlardaki isimleri virgülle ayrıp, ayrı seçenek olarak ekleyelim:
$teams = [];
if (!empty($pot->{$column['cols'][0]})) {
  $teams[] = $pot->{$column['cols'][0]} . ' (BD)';
}
if (!empty($pot->{$column['cols'][1]})) {
  $names = explode(',', $pot->{$column['cols'][1]});
  foreach ($names as $name) {
    $trimmed = trim($name);
    if ($trimmed) {
      $teams[] = $trimmed . ' (D)';
    }
  }
}
if (!empty($pot->{$column['cols'][2]})) {
  $names = explode(',', $pot->{$column['cols'][2]});
  foreach ($names as $name) {
    $trimmed = trim($name);
    if ($trimmed) {
      $teams[] = $trimmed . ' (TU)';
    }
  }
}
if (!empty($pot->{$column['cols'][3]})) {
  $names = explode(',', $pot->{$column['cols'][3]});
  foreach ($names as $name) {
    $trimmed = trim($name);
    if ($trimmed) {
      $teams[] = $trimmed . ' (G)';
    }
  }
}
if (!empty($pot->{$column['cols'][4]})) {
  $names = explode(',', $pot->{$column['cols'][4]});
  foreach ($names as $name) {
    $trimmed = trim($name);
    if ($trimmed) {
      $teams[] = $trimmed . ' (AD)';
    }
  }
}
if (!empty($pot->{$column['cols'][5]})) {
  $names = explode(',', $pot->{$column['cols'][5]});
  foreach ($names as $name) {
    $trimmed = trim($name);
    if ($trimmed) {
      $teams[] = $trimmed . ' (Değ.)';
    }
  }
}
?>

  <!-- Define global variables for external JS file -->
<script>
  window.appData = {
    standards: @json($standards),
    teams: @json($teams),
    audit: @json($audit ?? null),
    asama: '{{ $asama }}'
  };
</script>

@section('page-script')
  <script src="{{ asset('assets/js/audit-plan.js') }}"></script>
@endsection

@section('content')
  <style>
    .table-active {
      background-color: rgba(0, 0, 0, 0.075);
    }
  </style>

  @php
    use Illuminate\Support\Facades\DB;
    $columnsMapping = [
        'plan_9001'  => ['iso9001a1sure', 'iso9001a2sure', 'iso9001gsure', 'iso9001ybsure'],
        'plan_14001' => ['iso14001a1sure', 'iso14001a2sure', 'iso14001gsure', 'iso14001ybsure'],
        'plan_45001' => ['iso45001a1sure', 'iso45001a2sure', 'iso45001gsure', 'iso45001ybsure'],
        'plan_22000' => ['iso22000a1sure', 'iso22000a2sure', 'iso22000gsure', 'iso22000ybsure'],
        'plan_50001' => ['iso50001a1sure', 'iso50001a2sure', 'iso50001gsure', 'iso50001ybsure'],
        'plan_27001' => ['iso27001a1sure', 'iso27001a2sure', 'iso27001gsure', 'iso27001ybsure'],
        'plan_smiic1' => ['oicsmiica1sure', 'oicsmiica2sure', 'oicsmiicgsure', 'oicsmiicybsure'],
    ];
    $stageMapping = [
        'asama1'   => 0,
        'asama2'   => 1,
        'gozetim1' => 2,
        'gozetim2' => 2,
        'ybtar'    => 3,
        'ozeltar'  => 2,
    ];
    $stageIndex = isset($stageMapping[$asama]) ? $stageMapping[$asama] : null;
    $totalDuration = 0;
    if($stageIndex !== null) {
        foreach($columnsMapping as $tableName => $colArray) {
            $identifier = str_replace('plan_', '', $tableName);
            if(strpos($belgelendirileceksistemler, $identifier) !== false) {
                $record = DB::table($tableName)->where('planno', $pot->planno)->first();
                if($record) {
                    $col = $colArray[$stageIndex];
                    $totalDuration += (float)($record->{$col} ?? 0);
                }
            }
        }
    }
    $roundedDuration = round($totalDuration * 2) / 2;
  @endphp

  @if($column)
    <div class="card mb-4">
      <div
        class="card-header sticky-element bg-info d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
        <h5 class="card-title mb-sm-0 me-2">Aşama: {{ strtoupper($asama) }}</h5>
        @include('_partials/planlama-menu', ['pno' => $pno])
      </div>
      <div class="card-body">
        <p class="card-text">
          <strong>Belgelendirilecek Sistemler:</strong> {{ $belgelendirileceksistemler }}
        </p>
        <p class="card-text">
          <strong>Tarih:</strong> {{ $pot->{$column['date']} ?? 'Tarih Belirtilmemiş' }}
        </p>
        <p class="card-text">
          <strong>Denetim Ekibi:</strong>
          @php $cols = $column['cols']; @endphp
          {{ $pot->{$cols[0]} ? $pot->{$cols[0]}.' (BD)' : '' }}
          {{ $pot->{$cols[1]} ? $pot->{$cols[1]}.' (D)' : '' }}
          {{ $pot->{$cols[2]} ? $pot->{$cols[2]}.' (TU)' : '' }}
          {{ $pot->{$cols[3]} ? $pot->{$cols[3]}.' (G)' : '' }}
          {{ $pot->{$cols[4]} ? $pot->{$cols[4]}.' (AD)' : '' }}
          {{ $pot->{$cols[5]} ? $pot->{$cols[5]}.' (Değ.)' : '' }}
          <br><br>
          <strong>Denetim Süresi:</strong> {{ $roundedDuration }} d/g
        </p>
      </div>
    </div>

    <div class="card card-action mb-4">
      <div class="card-header">
        <div class="card-action-title text-center">Saha Adresleri</div>
        <div class="card-action-element">
          <ul class="list-inline mb-0">
            <li class="list-inline-item">
              <a href="javascript:void(0);" class="card-collapsible"><i class="tf-icons mdi mdi-chevron-up"></i></a>
            </li>
          </ul>
        </div>
      </div>
      <div class="collapse show">
        <table class="table table-bordered">
          <thead>
          <tr>
            <th colspan="2">Saha Adresleri</th>
            <th style="width:10%;">Denetim Zamanı</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td style="width:2%;">1.</td>
            <td>{{ (!empty($basvuru->subeadresia) && intval($basvuru->subevardaa) > 0) ? $basvuru->subeadresia : '-' }}</td>
            <td></td>
          </tr>
          <tr>
            <td>2.</td>
            <td>{{ (!empty($basvuru->subeadresib) && intval($basvuru->subevardba) > 0) ? $basvuru->subeadresib : '-' }}</td>
            <td></td>
          </tr>
          <tr>
            <td>3.</td>
            <td>{{ (!empty($basvuru->subeadresic) && intval($basvuru->subevardca) > 0) ? $basvuru->subeadresic : '-' }}</td>
            <td></td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  @endif

  <div class="row">
    <div class="col-12 text-danger">
      <h5>[{{ $pno }}] {{ $pot->firmaadi }}</h5>
      {{ $pot->belgelendirmekapsami }}
    </div>
  </div>

  <div class="row gy-4 mb-4">
    <div class="col-xl-12">
      <div id="formMessages" class="mb-4" style="display: none;"></div>
      <form id="formAuditPlan" method="POST" action="{{route('audit-plan-kaydet')}}">
        @csrf
        <input type="hidden" name="formAuditPlanRoute" value="{{ route('audit-plan-kaydet') }}" class="form-control">
        <input type="hidden" name="pno" value="{{$pno}}" class="form-control">
        <input type="hidden" name="asama" value="{{$asama}}" class="form-control">
        <!-- Tablo -->
        <table class="table table-bordered" id="scheduleTable">
          <thead>
          <tr>
            <th style="width:5%;" class="text-center align-middle">Seç</th>
            <th style="width:7.5%;" class="text-center align-middle">Saat Başlangıç</th>
            <th style="width:7.5%;" class="text-center align-middle">Saat Bitiş</th>
            <th style="width:30%;" class="text-center align-middle">Departman/ Proses/Saha</th>
            <th style="width:15%;" class="text-center align-middle">Denetim Ekibi</th>
            <th style="width:15%;" class="text-center align-middle">Standard</th>
            <th style="width:15%;" class="text-center align-middle">Madde No</th>
            <th style="width:5%;" class="text-center align-middle">İşlem</th>
          </tr>
          </thead>
          <tbody>
          <!-- 1. Satır: Açılış Toplantısı – Saatler ayrı, geri kalan hücreler merge (colspan=5) -->
          <tr class="default-row" data-locked="true">
            <td></td>
            <td>
              <input type="time" name="rows[0][start]" value="09:00" class="form-control" readonly>
            </td>
            <td>
              <input type="time" name="rows[0][end]" value="09:30" class="form-control" readonly>
            </td>
            <td colspan="5" class="text-center">Açılış Toplantısı</td>
          </tr>
          <!-- 2. Satır: Kısa Tur – Saatler ayrı, geri kalan hücreler merge (colspan=5) -->
          <tr class="default-row" data-locked="true">
            <td></td>
            <td>
              <input type="time" name="rows[1][start]" value="09:30" class="form-control" readonly>
            </td>
            <td>
              <input type="time" name="rows[1][end]" value="10:00" class="form-control" readonly>
            </td>
            <td colspan="5" class="text-center">Kısa Tur</td>
          </tr>
          <!-- 3. Satır: Varsayılan Satır – tüm sütunlar ayrı; dinamik işlemler 3. satırdan itibaren geçerli -->
          <tr class="default-row" data-locked="false">
            <td><input type="checkbox" class="row-select"></td>
            <td><input type="time" name="rows[2][start]" value="10:00" class="form-control"></td>
            <td><input type="time" name="rows[2][end]" value="11:00" class="form-control"></td>
            <td><input type="text" name="rows[2][department]" placeholder="Departman/ Proses/Saha" class="form-control"
                       value=""></td>
            <td>
              <select name="rows[2][team]" class="form-control">
                <option value="">Lütfen Seçiniz</option>
                @foreach($teams as $team)
                  <option value="{{ $team }}" @if(count($teams) === 1) selected @endif>{{ $team }}</option>
                @endforeach
              </select>
            </td>
            <td>
              <select name="rows[2][standard]" class="form-control">
                <option value="">Lütfen Seçiniz</option>
                @foreach($standards as $std)
                  <option value="{{ $std }}" @if(count($standards) === 1) selected @endif>{{ $std }}</option>
                @endforeach
              </select>
            </td>
            <td>
              <select name="rows[2][madde_no][]" class="select2 form-select" multiple>
                <option value="">Lütfen Seçiniz</option>
                <!-- Bu selectin seçenekleri, seçilen standard ve asamaya göre AJAX ile doldurulacak -->
              </select>
            </td>
            <td>
              <button type="button" class="btn btn-danger remove-row">Sil</button>
            </td>
          </tr>
          </tbody>
        </table>
        <br><br>
        <!-- Buton grupları -->
        <div class="d-flex flex-wrap gap-2">
          <div class="btn-group" role="group" aria-label="Denetim Planı İşlemleri">
            <button type="button" class="btn btn-primary" id="addRow">Satır Ekle</button>
            <button type="button" class="btn btn-warning" id="mergeRows">Seçili Satırları Birleştir</button>
            <button type="button" class="btn btn-info" id="splitRow">Birleştirilmiş Satırı Ayır</button>
          </div>
          <br><br>
          <div class="btn-group" role="group" aria-label="Özel Satır Ekle">
            <button type="button" class="btn btn-instagram" id="addDegerlendirme">Değerlendirme Ekle</button>
            <button type="button" class="btn btn-facebook" id="addKapanis">Kapanış Toplantısı Ekle</button>
            <button type="button" class="btn btn-danger" id="addOglen">Öğle Arası Ekle</button>
          </div>
        </div>
        <br><br>
        <button type="submit" class="btn btn-success">Gönder</button>
        <!-- Add this dropdown button to your audit-plan.blade.php file -->
        <div class="btn-group" role="group" aria-label="Özel Satır Ekle">
          <a class="btn btn-info" href="{{ route('audit-plan-export', ['pno' => $pno, 'asama' => $asama, 'format' => 'docx']) }}">
            <i class="tf-icons mdi mdi-file-word"></i> Word Belgesi (.docx)
          </a>
          <a class="btn btn-primary"
             href="{{ route('audit-plan-export', ['pno' => $pno, 'asama' => $asama, 'format' => 'pdf']) }}">
            <i class="tf-icons mdi mdi-file-pdf"></i> PDF Belgesi (.pdf)
          </a>
{{--          <a class="btn btn-info"--}}
{{--             href="{{ route('audit-plan-export', ['pno' => $pno, 'asama' => $asama, 'include_participants' => true]) }}">--}}
{{--            <i class="tf-icons mdi mdi-file-word"></i> Word Belgesi (Katılımcı Listesi ile)--}}
{{--          </a>--}}
{{--          <a class="btn btn-primary"--}}
{{--             href="{{ route('audit-plan-export', ['pno' => $pno, 'asama' => $asama, 'format' => 'pdf', 'include_participants' => true]) }}">--}}
{{--            <i class="tf-icons mdi mdi-file-pdf"></i> PDF Belgesi (Katılımcı Listesi ile)--}}
{{--          </a>--}}
        </div>
      </form>

      <!-- Modaller (başarı ve hata mesajları) -->
      <div class="modal modal-top fade" id="myModalSucces" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <form class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalTopTitle">Sistem Belgelendirme Sertifika</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col mb-4 mt-2 text-wrap">
                  <div id="formkaydetsonucsuccess"></div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
          </form>
        </div>
      </div>
      <div class="modal modal-top fade" id="myModalError" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <form class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalTopTitle">Sistem Belgelendirme Sertifika</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col mb-4 mt-2 text-wrap">
                  <div id="formkaydetsonucerror"></div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
