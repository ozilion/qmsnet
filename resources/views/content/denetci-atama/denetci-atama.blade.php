@extends('layouts/layoutMaster')

@section('title', 'Denetçi Atama')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
@endsection

@php
  // Tüm EA kodlarını ve NACE kodlarını eanacekodlari tablosundan çekelim
  $eaOptions = DB::table('eanacekodlari')
              ->select('ea')
              ->distinct()
              ->orderBy('ea')
              ->pluck('ea')
              ->toArray();

  // NACE kodları için bir array hazırlayalım - tüm NACE kodlarını getir, filtreleme yapma
  $naceByEa = [];
  $allNaceOptions = DB::table('eanacekodlari')->select('ea', 'nace')->orderBy('ea')->orderBy('nace')->get();
  foreach ($allNaceOptions as $option) {
      if (!isset($naceByEa[$option->ea])) {
          $naceByEa[$option->ea] = [];
      }
      $naceByEa[$option->ea][] = $option->nace;
  }
  // JavaScript için JSON formatına çevirelim
  $naceByEaJson = json_encode($naceByEa);
@endphp
@if(isset($record->ea) && !empty($record->ea) && isset($naceByEa[$record->ea]))
  @foreach($naceByEa[$record->ea] as $nace)
    @php
      // Mevcut seçili değerler
      $naceArray = isset($record->nace) ? explode(',', $record->nace) : [];
      $selected = in_array($nace, $naceArray);

      // xx.xx formatını kontrol et (5 karakter, örn. 10.01)
      $isValidFormat = preg_match('/^\d{2}\.\d{2}$/', $nace);
    @endphp
    <option value="{{ $nace }}" {{ $selected ? 'selected' : '' }} {{ !$isValidFormat ? 'disabled' : '' }}>{{ $nace }}</option>
  @endforeach
@endif

@section('page-script')
  <script src="{{asset('assets/js/auditor-denetci-dosyasi.js')}}"></script>
  <script src="{{asset('assets/js/cards-actions.js')}}"></script>
@endsection

@section('styles')

  <!-- Sayfanın başına veya head bölümüne eklenecek CSS stilleri -->
  <style>
    /* Gün-Yıl inputları için stil tanımlamaları */
    .danismanlik-input-container {
      display: flex;
      width: 100%;
      gap: 4px;
    }

    .danismanlik-gun-input {
      width: 40%;
      border-radius: 4px 0 0 4px;
    }

    .danismanlik-yil-input {
      width: 60%;
      background-color: #f8f8f8;
      border-radius: 0 4px 4px 0;
    }

    /* Placeholder stilleri */
    .danismanlik-gun-input::placeholder {
      color: #aaa;
      font-size: 0.85em;
    }

    .danismanlik-yil-input::placeholder {
      color: #aaa;
      font-size: 0.85em;
    }

    /* Focus (odaklanma) durumunda stil */
    .danismanlik-gun-input:focus {
      z-index: 1;
      position: relative;
    }
  </style>

@endsection

@section('content')
  <h4 class="py-3 mb-4">
    {{--  <span class="text-muted fw-light">Forms /</span> File upload--}}
  </h4>

  <div class="row">
    <!-- Multi  -->
    <div class="col-12">
      <div class="card">
        <h5 class="card-header">Denetçi Atama / {{$user->name}}</h5>
        <div class="card-body">
          <!-- Hata mesajları -->
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if(session('success'))
            <div class="alert alert-success">
              {{ session('success') }}
            </div>
          @endif

          <!-- Form başlangıcı -->
          <form id="denetciAtamaEkleForm" action="{{route("denetci.ata")}}">
            @csrf
            <input type="hidden" name="uid" id="uid" class="form-control"
                   placeholder=""
                   value="{{$uid ? $uid : ""}}"/>

            <!-- Form Kaydet Butonu -->
            <div class="text-end mt-1 mb-2">
              <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
            <!-- Atama standartları -->
            <div class="col-sm-12">
              <div class="card card-action mb-4">
                <div class="card-header">
                  <div class="card-action-title text-center"></div>
                  <div class="card-action-element">
                    <ul class="list-inline mb-0">
                      <li class="list-inline-item">
                        <a href="javascript:void(0);" class="card-collapsible"><i
                            class="tf-icons mdi mdi-chevron-up"></i></a>
                      </li>
                    </ul>
                  </div>
                </div>

                <div class="collapse show">
                  <div class="card-body">
{{--{{print($auditor)}}--}}
                    @php
                      // Controller'dan gelen $auditor dizisi, her kaydın "standard" alanını içeriyor.
                      // Tüm standart değerlerini lowercase olarak alıyoruz.
                      $auditorStandards = isset($auditor)
                          ? collect($auditor)->pluck('standard')->map(function($item) {
                                return strtolower($item);
                            })->toArray()
                          : [];
                    @endphp

                    <div class="row g-4">
                      <!-- ISO 9001 -->
                      <div class="col-sm-3">
                        <label class="form-label">ISO 9001 Başdenetçi Eğitimi</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="iso9001" id="iso9001_var" value="var" {{ in_array('iso9001', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso9001_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="iso9001" id="iso9001_yok" value="yok" {{ !in_array('iso9001', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso9001_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- ISO 14001 -->
                      <div class="col-sm-3">
                        <label class="form-label">ISO 14001 Başdenetçi Eğitimi</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="iso14001" id="iso14001_var" value="var" {{ in_array('iso14001', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso14001_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="iso14001" id="iso14001_yok" value="yok" {{ !in_array('iso14001', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso14001_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- ISO 45001 -->
                      <div class="col-sm-3">
                        <label class="form-label">ISO 45001 Başdenetçi Eğitimi</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="iso45001" id="iso45001_var" value="var" {{ in_array('iso45001', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso45001_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="iso45001" id="iso45001_yok" value="yok" {{ !in_array('iso45001', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso45001_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- ISO 22000 -->
                      <div class="col-sm-3">
                        <label class="form-label">ISO 22000 Başdenetçi Eğitimi</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="iso22000" id="iso22000_var" value="var" {{ in_array('iso22000', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso22000_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="iso22000" id="iso22000_yok" value="yok" {{ !in_array('iso22000', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso22000_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- ISO 50001 -->
                      <div class="col-sm-3">
                        <label class="form-label">ISO 50001 Başdenetçi Eğitimi</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="iso50001" id="iso50001_var" value="var" {{ in_array('iso50001', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso50001_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="iso50001" id="iso50001_yok" value="yok" {{ !in_array('iso50001', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso50001_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- ISO 27001 -->
                      <div class="col-sm-3">
                        <label class="form-label">ISO 27001 Başdenetçi Eğitimi</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="iso27001" id="iso27001_var" value="var" {{ in_array('iso27001', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso27001_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="iso27001" id="iso27001_yok" value="yok" {{ !in_array('iso27001', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="iso27001_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- OIC/SMIIC 1 -->
                      <div class="col-sm-3">
                        <label class="form-label">OIC/SMIIC 1</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="oicsmiic1" id="oicsmiic1_var" value="var" {{ in_array('oicsmiic1', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic1_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="oicsmiic1" id="oicsmiic1_yok" value="yok" {{ !in_array('oicsmiic1', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic1_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- OIC/SMIIC 6 -->
                      <div class="col-sm-3">
                        <label class="form-label">OIC/SMIIC 6</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="oicsmiic6" id="oicsmiic6_var" value="var" {{ in_array('oicsmiic6', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic6_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="oicsmiic6" id="oicsmiic6_yok" value="yok" {{ !in_array('oicsmiic6', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic6_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- OIC/SMIIC 9 -->
                      <div class="col-sm-3">
                        <label class="form-label">OIC/SMIIC 9</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="oicsmiic9" id="oicsmiic9_var" value="var" {{ in_array('oicsmiic9', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic9_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="oicsmiic9" id="oicsmiic9_yok" value="yok" {{ !in_array('oicsmiic9', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic9_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- OIC/SMIIC 17-1 -->
                      <div class="col-sm-3">
                        <label class="form-label">OIC/SMIIC 17-1</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="oicsmiic171" id="oicsmiic171_var" value="var" {{ in_array('oicsmiic171', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic17-1_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="oicsmiic171" id="oicsmiic171_yok" value="yok" {{ !in_array('oicsmiic171', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic171_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- OIC/SMIIC 23 -->
                      <div class="col-sm-3">
                        <label class="form-label">OIC/SMIIC 23</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="oicsmiic23" id="oicsmiic23_var" value="var" {{ in_array('oicsmiic23', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic23_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="oicsmiic23" id="oicsmiic23_yok" value="yok" {{ !in_array('oicsmiic23', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic23_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                      <!-- OIC/SMIIC 24 -->
                      <div class="col-sm-3">
                        <label class="form-label">OIC/SMIIC 24</label>
                        <div class="d-flex">
                          <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="oicsmiic24" id="oicsmiic24_var" value="var" {{ in_array('oicsmiic24', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic24_var">Var</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="oicsmiic24" id="oicsmiic24_yok" value="yok" {{ !in_array('oicsmiic24', $auditorStandards) ? 'checked' : '' }}>
                            <label class="form-check-label" for="oicsmiic24_yok">Yok</label>
                          </div>
                        </div>
                      </div>

                    </div>

                  </div>
                </div>
              </div>
            </div>

            <!-- Atama tabloları -->
            <div class="col-sm-12">
              <div class="card card-action mb-4">
                <div class="card-header">
                  <div class="card-action-title text-center"></div>
                  <div class="card-action-element">
                    <ul class="list-inline mb-0">
                      <li class="list-inline-item">
                        <a href="javascript:void(0);" class="card-collapsible"><i
                            class="tf-icons mdi mdi-chevron-up"></i></a>
                      </li>
                    </ul>
                  </div>
                </div>

                <div class="collapse show">
                  <div class="card-body">

                    <!-- ISO 9001 TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-2">
                        <button type="button" class="btn btn-success btn-sm mt-2 add-row-btn" data-standard="iso9001">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="iso9001Table">
                        <thead class="table-light">
                        <tr>
                          <th colspan="7">ISO 9001 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">EA Kodu</th>
                          <th style="width: 25%">NACE Kodu</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          // $auditor dizisini koleksiyona çevirip "iso9001" kayıtlarını filtreliyoruz.
                          $iso9001Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'iso9001';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($iso9001Records)) {
                              $iso9001Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($iso9001Records as $index => $record)
                          <tr>
                            <td class="row-index">{{ $index + 1 }}</td>
                            <td>
                              <select name="iso9001_ea{{ $index + 1 }}" class="form-select ea-select" data-standard="iso9001" data-row="{{ $index + 1 }}">
                                <option value="">EA Kodu Seçin</option>
                                @foreach($eaOptions as $ea)
                                  <option value="{{ $ea }}" {{ isset($record->ea) && $record->ea == $ea ? 'selected' : '' }}>{{ $ea }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td>
                              <select name="iso9001_nace{{ $index + 1 }}" class="form-select nace-select selectpicker" data-standard="iso9001" data-row="{{ $index + 1 }}" multiple data-live-search="true">
                                @if(isset($record->ea) && !empty($record->ea) && isset($naceByEa[$record->ea]))
                                  @foreach($naceByEa[$record->ea] as $nace)
                                    @php
                                      $selected = isset($record->nace) && in_array($nace, explode(',', $record->nace));
                                      // xx.xx formatını kontrol et (5 karakter, örn. 10.01)
                                      $isValidFormat = preg_match('/^\d{2}\.\d{2}$/', $nace);
                                    @endphp
                                    <option value="{{ $nace }}" {{ $selected ? 'selected' : '' }} {{ !$isValidFormat ? 'disabled' : '' }}>{{ $nace }}</option>
                                  @endforeach
                                @endif
                              </select>
                            </td>
                            <td>
                              <input type="text" name="iso9001_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}">
                            </td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="iso9001_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ $record->danismanlikTecrubesiGun ?? '' }}">
                                <input type="text" name="iso9001_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td>
                              <input type="text" name="iso9001_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}">
                            </td>
                            <td>
                              <button type="button" class="btn btn-danger btn-sm delete-row-btn" data-standard="iso9001" {{$index === 0 ? "disabled" : ""}}>Sil</button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- ISO 14001 TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2 add-row-btn" data-standard="iso14001">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="iso14001Table">
                        <thead class="table-light">
                        <tr>
                          <th colspan="7">ISO 14001 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">EA Kodu</th>
                          <th style="width: 25%">NACE Kodu</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $iso14001Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'iso14001';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($iso14001Records)) {
                              $iso14001Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($iso14001Records as $index => $record)
                          <tr>
                            <td class="row-index">{{ $index + 1 }}</td>
                            <td>
                              <select name="iso14001_ea{{ $index + 1 }}" class="form-select ea-select" data-standard="iso14001" data-row="{{ $index + 1 }}">
                                <option value="">EA Kodu Seçin</option>
                                @foreach($eaOptions as $ea)
                                  <option value="{{ $ea }}" {{ isset($record->ea) && $record->ea == $ea ? 'selected' : '' }}>{{ $ea }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td>
                              <select name="iso14001_nace{{ $index + 1 }}" class="form-select nace-select selectpicker" data-standard="iso14001" data-row="{{ $index + 1 }}" multiple data-live-search="true">
                                @if(isset($record->ea) && !empty($record->ea) && isset($naceByEa[$record->ea]))
                                  @foreach($naceByEa[$record->ea] as $nace)
                                    @php
                                      $selected = isset($record->nace) && in_array($nace, explode(',', $record->nace));
                                    @endphp
                                    <option value="{{ $nace }}" {{ $selected ? 'selected' : '' }}>{{ $nace }}</option>
                                  @endforeach
                                @endif
                              </select>
                            </td>
                            <td>
                              <input type="text" name="iso14001_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}">
                            </td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="iso14001_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ $record->danismanlikTecrubesiGun ?? '' }}">
                                <input type="text" name="iso14001_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td>
                              <input type="text" name="iso14001_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}">
                            </td>
                            <td>
                              <button type="button" class="btn btn-danger btn-sm delete-row-btn" data-standard="iso14001" {{$index === 0 ? "disabled" : ""}}>Sil</button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- ISO 45001 TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2 add-row-btn" data-standard="iso45001">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="iso45001Table">
                        <thead class="table-light">
                        <tr>
                          <th colspan="7">ISO 45001 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">EA Kodu</th>
                          <th style="width: 25%">NACE Kodu</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $iso45001Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'iso45001';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($iso45001Records)) {
                              $iso45001Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($iso45001Records as $index => $record)
                          <tr>
                            <td class="row-index">{{ $index + 1 }}</td>
                            <td>
                              <select name="iso45001_ea{{ $index + 1 }}" class="form-select ea-select" data-standard="iso45001" data-row="{{ $index + 1 }}">
                                <option value="">EA Kodu Seçin</option>
                                @foreach($eaOptions as $ea)
                                  <option value="{{ $ea }}" {{ isset($record->ea) && $record->ea == $ea ? 'selected' : '' }}>{{ $ea }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td>
                              <select name="iso45001_nace{{ $index + 1 }}" class="form-select nace-select selectpicker" data-standard="iso45001" data-row="{{ $index + 1 }}" multiple data-live-search="true">
                                @if(isset($record->ea) && !empty($record->ea) && isset($naceByEa[$record->ea]))
                                  @foreach($naceByEa[$record->ea] as $nace)
                                    @php
                                      $selected = isset($record->nace) && in_array($nace, explode(',', $record->nace));
                                    @endphp
                                    <option value="{{ $nace }}" {{ $selected ? 'selected' : '' }}>{{ $nace }}</option>
                                  @endforeach
                                @endif
                              </select>
                            </td>
                            <td>
                              <input type="text" name="iso45001_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}">
                            </td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="iso45001_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ $record->danismanlikTecrubesiGun ?? '' }}">
                                <input type="text" name="iso45001_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td>
                              <input type="text" name="iso45001_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}">
                            </td>
                            <td>
                              <button type="button" class="btn btn-danger btn-sm delete-row-btn" data-standard="iso45001" {{$index === 0 ? "disabled" : ""}}>Sil</button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- ISO 22000 TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addIso22000Row">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="iso22000-table">
                        <thead>
                        <tr>
                          <th colspan="7">ISO 22000 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">Kategori</th>
                          <th style="width: 25%">Alt Kategori</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $iso22000Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'iso22000';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($iso22000Records)) {
                              $iso22000Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($iso22000Records as $index => $record)
                          <tr>
                            <td class="row-index-22000">{{ $index + 1 }}</td>
                            <td><input type="text" name="iso22000_kategori{{ $index + 1 }}" class="form-control" value="{{ $record->kategori ?? '' }}"></td>
                            <td><input type="text" name="iso22000_altKategori{{ $index + 1 }}" class="form-control" value="{{ $record->altKategori ?? '' }}"></td>
                            <td><input type="text" name="iso22000_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}"></td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="iso22000_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ isset($record->danismanlikTecrubesi) ? round(floatval(str_replace(',', '.', $record->danismanlikTecrubesi)) * 50) : '' }}">
                                <input type="text" name="iso22000_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td><input type="text" name="iso22000_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-22000" {{$index === 0 ? "disabled" : ""}}>Sil</button></td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- ISO 50001 TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addIso50001Row">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="iso50001-table">
                        <thead class="table-light">
                        <tr>
                          <th colspan="6">ISO 50001 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 15%">Teknik Alan</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 15%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $iso50001Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'iso50001';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($iso50001Records)) {
                              $iso50001Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($iso50001Records as $index => $record)
                          <tr>
                            <td class="row-index-50001">{{ $index + 1 }}</td>
                            <td><input type="text" name="iso50001_teknikAlan{{ $index + 1 }}" class="form-control" value="{{ $record->teknikAlan ?? '' }}"></td>
                            <td><input type="text" name="iso50001_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}"></td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="iso50001_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ isset($record->danismanlikTecrubesi) ? round(floatval(str_replace(',', '.', $record->danismanlikTecrubesi)) * 50) : '' }}">
                                <input type="text" name="iso50001_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td><input type="text" name="iso50001_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-50001" {{$index === 0 ? "disabled" : ""}}>Sil</button></td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- ISO 27001 TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addIso27001Row">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="iso27001-table">
                        <thead>
                        <tr>
                          <th colspan="7">ISO 27001 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">Teknik Alan</th>
                          <th style="width: 25%">Teknolojik Alan</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 15%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $iso27001Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'iso27001';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($iso27001Records)) {
                              $iso27001Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($iso27001Records as $index => $record)
                          <tr>
                            <td class="row-index-27001">{{ $index + 1 }}</td>
                            <td><input type="text" name="iso27001_teknikAlan{{ $index + 1 }}" class="form-control" value="{{ $record->teknikAlan ?? '' }}"></td>
                            <td><input type="text" name="iso27001_teknolojikAlan{{ $index + 1 }}" class="form-control" value="{{ $record->teknolojikAlan ?? '' }}"></td>
                            <td><input type="text" name="iso27001_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}"></td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="iso27001_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ isset($record->danismanlikTecrubesi) ? round(floatval(str_replace(',', '.', $record->danismanlikTecrubesi)) * 50) : '' }}">
                                <input type="text" name="iso27001_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td><input type="text" name="iso27001_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-27001" {{$index === 0 ? "disabled" : ""}}>Sil</button></td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- OIC/SMIIC 1 Atama TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addOicSmiic1Row">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="oicSmiic1Table">
                        <thead class="table-light">
                        <tr>
                          <th colspan="7">OIC/SMIIC 1 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">Kategori</th>
                          <th style="width: 25%">Alt Kategori</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $oicSmiic1Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'oicsmiic1';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($oicSmiic1Records)) {
                              $oicSmiic1Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($oicSmiic1Records as $index => $record)
                          <tr>
                            <td class="row-index-oicSmiic1">{{ $index + 1 }}</td>
                            <td><input type="text" name="oicSmiic1_kategori{{ $index + 1 }}" class="form-control" value="{{ $record->kategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic1_altKategori{{ $index + 1 }}" class="form-control" value="{{ $record->altKategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic1_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}"></td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="oicSmiic1_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ isset($record->danismanlikTecrubesi) ? round(floatval(str_replace(',', '.', $record->danismanlikTecrubesi)) * 50) : '' }}">
                                <input type="text" name="oicSmiic1_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td><input type="text" name="oicSmiic1_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-oicSmiic1" {{$index === 0 ? "disabled" : ""}}>Sil</button></td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- OIC/SMIIC 6 Atama TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addOicSmiic6Row">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="oicSmiic6Table">
                        <thead class="table-light">
                        <tr>
                          <th colspan="7">OIC/SMIIC 6 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">Kategori</th>
                          <th style="width: 25%">Alt Kategori</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $oicSmiic6Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'oicsmiic6';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($oicSmiic6Records)) {
                              $oicSmiic6Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($oicSmiic6Records as $index => $record)
                          <tr>
                            <td class="row-index-oicSmiic6">{{ $index + 1 }}</td>
                            <td><input type="text" name="oicSmiic6_kategori{{ $index + 1 }}" class="form-control" value="{{ $record->kategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic6_altKategori{{ $index + 1 }}" class="form-control" value="{{ $record->altKategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic6_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}"></td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="oicSmiic6_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ isset($record->danismanlikTecrubesi) ? round(floatval(str_replace(',', '.', $record->danismanlikTecrubesi)) * 50) : '' }}">
                                <input type="text" name="oicSmiic6_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td><input type="text" name="oicSmiic6_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-oicSmiic6" {{$index === 0 ? "disabled" : ""}}>Sil</button></td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- OIC/SMIIC 9 Atama TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addOicSmiic9Row">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="oicSmiic9Table">
                        <thead class="table-light">
                        <tr>
                          <th colspan="7">OIC/SMIIC 9 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">Kategori</th>
                          <th style="width: 25%">Alt Kategori</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $oicSmiic9Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'oicsmiic9';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($oicSmiic9Records)) {
                              $oicSmiic9Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($oicSmiic9Records as $index => $record)
                          <tr>
                            <td class="row-index-oicSmiic9">{{ $index + 1 }}</td>
                            <td><input type="text" name="oicSmiic9_kategori{{ $index + 1 }}" class="form-control" value="{{ $record->kategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic9_altKategori{{ $index + 1 }}" class="form-control" value="{{ $record->altKategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic9_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}"></td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="oicSmiic9_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ isset($record->danismanlikTecrubesi) ? round(floatval(str_replace(',', '.', $record->danismanlikTecrubesi)) * 50) : '' }}">
                                <input type="text" name="oicSmiic9_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td><input type="text" name="oicSmiic9_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-oicSmiic9" {{$index === 0 ? "disabled" : ""}}>Sil</button></td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- OIC/SMIIC 17-1 Atama TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addOicSmiic171Row">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="oicSmiic171Table">
                        <thead class="table-light">
                        <tr>
                          <th colspan="7">OIC/SMIIC 17-1 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">Kategori</th>
                          <th style="width: 25%">Alt Kategori</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $oicSmiic171Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'oicsmiic171';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($oicSmiic171Records)) {
                              $oicSmiic171Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($oicSmiic171Records as $index => $record)
                          <tr>
                            <td class="row-index-oicSmiic171">{{ $index + 1 }}</td>
                            <td><input type="text" name="oicSmiic171_kategori{{ $index + 1 }}" class="form-control" value="{{ $record->kategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic171_altKategori{{ $index + 1 }}" class="form-control" value="{{ $record->altKategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic171_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}"></td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="oicSmiic171_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ isset($record->danismanlikTecrubesi) ? round(floatval(str_replace(',', '.', $record->danismanlikTecrubesi)) * 50) : '' }}">
                                <input type="text" name="oicSmiic171_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td><input type="text" name="oicSmiic171_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-oicSmiic171" {{$index === 0 ? "disabled" : ""}}>Sil</button></td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- OIC/SMIIC 23 Atama TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addOicSmiic23Row">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="oicSmiic23Table">
                        <thead class="table-light">
                        <tr>
                          <th colspan="7">OIC/SMIIC 23 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">Kategori</th>
                          <th style="width: 25%">Alt Kategori</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $oicSmiic23Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'oicsmiic23';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($oicSmiic23Records)) {
                              $oicSmiic23Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($oicSmiic23Records as $index => $record)
                          <tr>
                            <td class="row-index-oicSmiic23">{{ $index + 1 }}</td>
                            <td><input type="text" name="oicSmiic23_kategori{{ $index + 1 }}" class="form-control" value="{{ $record->kategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic23_altKategori{{ $index + 1 }}" class="form-control" value="{{ $record->altKategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic23_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}"></td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="oicSmiic23_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ isset($record->danismanlikTecrubesi) ? round(floatval(str_replace(',', '.', $record->danismanlikTecrubesi)) * 50) : '' }}">
                                <input type="text" name="oicSmiic23_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td><input type="text" name="oicSmiic23_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-oicSmiic23" {{$index === 0 ? "disabled" : ""}}>Sil</button></td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                    <hr>
                    <!-- OIC/SMIIC 24 Atama TABLOSU -->
                    <div class="table-responsive">
                      <div class="float-end mt-3">
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addOicSmiic24Row">Satır Ekle</button>
                      </div>
                      <table class="table table-bordered text-center" id="oicSmiic24Table">
                        <thead class="table-light">
                        <tr>
                          <th colspan="7">OIC/SMIIC 24 Atama</th>
                        </tr>
                        <tr>
                          <th style="width: 5%">No</th>
                          <th style="width: 10%">Kategori</th>
                          <th style="width: 25%">Alt Kategori</th>
                          <th style="width: 10%">İş Tecrübesi (Süre Yıl)</th>
                          <th style="width: 15%">Danışmanlık Tecrübesi</th>
                          <th style="width: 25%">Atama Referansı</th>
                          <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                          $oicSmiic24Records = collect($auditor)->filter(function($record) {
                              return strtolower($record->standard) == 'oicsmiic24';
                          })->values()->all();

                          // Eğer kayıt yoksa, en az bir boş satır ekleyelim
                          if (empty($oicSmiic24Records)) {
                              $oicSmiic24Records = [new stdClass()];
                          }
                        @endphp

                        @foreach ($oicSmiic24Records as $index => $record)
                          <tr>
                            <td class="row-index-oicSmiic24">{{ $index + 1 }}</td>
                            <td><input type="text" name="oicSmiic24_kategori{{ $index + 1 }}" class="form-control" value="{{ $record->kategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic24_altKategori{{ $index + 1 }}" class="form-control" value="{{ $record->altKategori ?? '' }}"></td>
                            <td><input type="text" name="oicSmiic24_isTecrubesi{{ $index + 1 }}" class="form-control" value="{{ $record->isTecrubesi ?? '' }}"></td>
                            <td>
                              <div class="danismanlik-input-container">
                                <input type="text" name="oicSmiic24_danismanlikTecrubesiGun{{ $index + 1 }}" class="form-control danismanlik-gun-input" placeholder="Gün" value="{{ isset($record->danismanlikTecrubesi) ? round(floatval(str_replace(',', '.', $record->danismanlikTecrubesi)) * 50) : '' }}">
                                <input type="text" name="oicSmiic24_danismanlikTecrubesi{{ $index + 1 }}" class="form-control danismanlik-yil-input" placeholder="Yıl" value="{{ $record->danismanlikTecrubesi ?? '' }}" readonly>
                              </div>
                            </td>
                            <td><input type="text" name="oicSmiic24_atamaReferansi{{ $index + 1 }}" class="form-control" value="{{ $record->atamaReferansi ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-oicSmiic24" {{$index === 0 ? "disabled" : ""}}>Sil</button></td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>

                  </div>
                </div>
              </div>
            </div>

            <!-- Form Kaydet Butonu -->
            <div class="text-end mt-4">
              <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
          </form>
          @include('_partials/_offcanvas/offcanvas-denetci-dosyasi-upload')
        </div>
      </div>
    </div>
    <!-- Multi  -->
  </div>
  <script type="text/javascript">
    // EA-NACE ilişki verisini global olarak tanımlayalım
    const naceByEaData = @json($naceByEa);
  </script>
@endsection
