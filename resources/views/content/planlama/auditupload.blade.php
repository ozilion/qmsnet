@extends('layouts/layoutMaster')

@section('title', '[' . $pno . '] ' . $asama . " | " . $plan[0]->firmaadi)

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/forms-file-upload.js')}}"></script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Forms /</span> File upload
</h4>

<?php
$pot = $plan[0];

//  $pot = array_merge($pot, $basvurubgys);


$asama1 = "";
$asama1 = $pot->asama1;
$bd1 = $pot->bd1;
$d1 = $pot->d1;
$tu1 = $pot->tu1;
$g1 = $pot->g1;
$iku1 = $pot->iku1;

$asama2 = $pot->asama2;
$bd2 = $pot->bd2;
$d2 = $pot->d2;
$tu2 = $pot->tu2;
$g2 = $pot->g2;
$iku2 = $pot->iku2;

$gozetim1 = $pot->gozetim1;
$gbd1 = $pot->gbd1;
$gd1 = $pot->gd1;
$gtu1 = $pot->gtu1;
$gg1 = $pot->gg1;
$ikug1 = $pot->ikug1;

$gozetim2 = $pot->gozetim2;
$gbd2 = $pot->gbd2;
$gd2 = $pot->gd2;
$gtu2 = $pot->gtu2;
$gg2 = $pot->gg2;
$ikug2 = $pot->ikug2;

$ybtar = $pot->ybtar;
$ybbd = $pot->ybbd;
$ybd = $pot->ybd;
$ybtu = $pot->ybtu;
$ybg = $pot->ybg;
$ikuyb = $pot->ikuyb;

$ozeltar = $pot->ozeltar;
$otbd = $pot->otbd;
$otd = $pot->otd;
$ottu = $pot->ottu;
$otg = $pot->otg;
$ikuot = $pot->ikuot;

$asama = trim($asama);
if ($asama == "") $asama = "ilkplan";

$tab1button = $asama === "ilkplan" ? " active" : " readonly";
$tab2button = $asama === "g1" ? " active" : " readonly";
$tab3button = $asama === "g2" ? " active" : " readonly";
$tab4button = $asama === "yb" ? " active" : " readonly";
$tab5button = $asama === "ozel" ? " active" : " readonly";

$tab1 = $asama === "ilkplan" ? " active" : " readonly";
$tab2 = $asama === "g1" ? " active" : " readonly";
$tab3 = $asama === "g2" ? " active" : " readonly";
$tab4 = $asama === "yb" ? " active" : " readonly";
$tab5 = $asama === "ozel" ? " active" : " readonly";

$tab1show = $asama === "ilkplan" ? " show" : "";
$tab2show = $asama === "g1" ? " show" : "";
$tab3show = $asama === "g2" ? " show" : "";
$tab4show = $asama === "yb" ? " show" : "";
$tab5show = $asama === "ozel" ? " show" : "";

$asama1 = substr($asama1, 1);
$asama1 = substr($asama1, 0, -1);
$asama1 = str_replace("|", ", ", $asama1);

$asama2 = substr($asama2, 1);
$asama2 = substr($asama2, 0, -1);
$asama2 = str_replace("|", ", ", $asama2);

$gozetim1 = substr($gozetim1, 1);
$gozetim1 = substr($gozetim1, 0, -1);
$gozetim1 = str_replace("|", ", ", $gozetim1);

$gozetim2 = substr($gozetim2, 1);
$gozetim2 = substr($gozetim2, 0, -1);
$gozetim2 = str_replace("|", ", ", $gozetim2);

$ozeltar = substr($ozeltar, 1);
$ozeltar = substr($ozeltar, 0, -1);
$ozeltar = str_replace("|", ", ", $ozeltar);

$ea = $pot->eakodu;
$nace = $pot->nacekodu;
$kat = str_replace("@", "", $pot->kategori22);
$oickat = str_replace("ß", "", $pot->kategorioic);
$enysteknikalan = str_replace("Æ", "", $pot->teknikalanenys);
$bgkat = str_replace("€", "", $pot->kategoribgys);
$katnace = "";

if ($nace != "") $nace = "|" . $nace;
if ($kat != "") $kat = "@" . str_replace("@", "", $kat);
$oickat = ($oickat != "") ? "ß" . str_replace("ß", "", $oickat) : str_replace("ß", "", $oickat);
$enysteknikalan = ($enysteknikalan != "") ? "Æ" . str_replace("Æ", "", $enysteknikalan) : str_replace("Æ", "", $enysteknikalan);
if ($bgkat != "") $bgkat = "€" . $bgkat;

echo $katnace = $ea . $nace . $kat . $oickat . $enysteknikalan . $bgkat;

$iso900115 = $pot->iso900115varyok == 1 ? true : false;
$iso1400115 = $pot->iso1400115varyok == 1 ? true : false;
$iso2200018 = $pot->iso2200018varyok == 1 ? true : false;
$oicsmiic = $pot->helalvaryok == 1 ? true : false;
$oicsmiic6 = $pot->oicsmiik6varyok == 1 ? true : false;
$oicsmiic9 = $pot->oicsmiik9varyok == 1 ? true : false;
$oicsmiic171 = $pot->oicsmiik171varyok == 1 ? true : false;
$oicsmiic23 = $pot->oicsmiik23varyok == 1 ? true : false;
$oicsmiic24 = $pot->oicsmiik24varyok == 1 ? true : false;
$iso45001 = $pot->iso4500118varyok == 1 ? true : false;
$iso50001 = $pot->iso5000118varyok == 1 ? true : false;
$iso27001 = $pot->iso27001varyok == 1 ? true : false;

$inceleneceksahasayisi = (!is_null($pot->inceleneceksahasayisi) || intval($pot->inceleneceksahasayisi) > 0) ? $pot->inceleneceksahasayisi : 0;

$inceleneceksahasayisisec = $inceleneceksahasayisi;
if($inceleneceksahasayisi > 2) {
  if($asama === "ilkplan")
    $inceleneceksahasayisisec = ceil(sqrt($inceleneceksahasayisi));
  if($asama === "g1")
    $inceleneceksahasayisisec = ceil(sqrt($inceleneceksahasayisi) * floatval("0.6"));
  if($asama === "g2")
    $inceleneceksahasayisisec = ceil(sqrt($inceleneceksahasayisi) * floatval("0.6"));
  if($asama === "yb")
    $inceleneceksahasayisisec = ceil(sqrt($inceleneceksahasayisi) * floatval("0.8"));
}

$sahaa = array(1=>$pot->subevardaa, 2=>$pot->subevardba, 3=>$pot->subevardca);
$sahab = array(1=>$pot->subevardab, 2=>$pot->subevardbb, 3=>$pot->subevardcb);
$sahac = array(1=>$pot->subevardac, 2=>$pot->subevardbc, 3=>$pot->subevardcc);

$sistemsay = 0;
if ($iso900115)
  $sistemsay++;
if ($iso1400115)
  $sistemsay++;
if ($iso2200018)
  $sistemsay++;
if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic23 || $oicsmiic24)
  $sistemsay++;
if ($oicsmiic171)
  $sistemsay++;
if ($iso45001)
  $sistemsay++;
if ($iso50001)
  $sistemsay++;
if ($iso27001)
  $sistemsay++;

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

$sistemversiyongecis = (isset($pot->sistemversiyongecis)) ? $pot->sistemversiyongecis : "";
$cevrim = $pot->belgecevrimi;
$cevrim = ($cevrim == "") ? "1" : $cevrim;

$kurul0 = mb_substr($pot->firmaadi, 0, 10, 'UTF-8');
$bitistarihi = date_create_from_format("Y-m-d", $pot->bitistarihi);
$bitistarihi = ($bitistarihi != "") ? date_format($bitistarihi, "d.m.Y") : "Sertifika kaydı yok...";
?>

<div class="row">
  <div class="col-12 text-danger">
    <h5>[{{$pno}}] {{$pot->firmaadi}}</h5>
    {{$pot->belgelendirmekapsami}}
  </div>
  <!-- Multi  -->
  <div class="col-12">
    <div class="card">
      <h5 class="card-header">Multiple</h5>
      <div class="card-body">
        <form action="/upload" class="dropzone needsclick" id="dropzone-multi">
          {{ csrf_field() }}
          <input type="hidden" id="formDenetimRaporuYukleRoute" value="{{url('formDenetimRaporuYukle')}}">
          <input type="hidden" id="asama" value="{{$asama}}">
          <input type="hidden" id="pno" value="{{$pno}}">

          <div class="dz-message needsclick">
            Yükleme işlemi için dosyayı yükleyin veya buraya tıklayın
            <span class="note needsclick"></span>
          </div>
          <div class="fallback">
            <input name="file" type="file" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Multi  -->
</div>
@endsection
