<!DOCTYPE html>
@php
$menuFixed = ($configData['layout'] === 'vertical') ? ($menuFixed ?? '') : (($configData['layout'] === 'front') ? '' : $configData['headerType']);
$navbarType = ($configData['layout'] === 'vertical') ? $configData['navbarType']: (($configData['layout'] === 'front') ? 'layout-navbar-fixed': '');
$isFront = ($isFront ?? '') == true ? 'Front' : '';
$contentLayout = (isset($container) ? (($container === 'container-xxl') ? "layout-compact" : "layout-wide") : "");
@endphp

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}" class="{{ $configData['style'] }}-style {{($contentLayout ?? '')}} {{ ($navbarType ?? '') }} {{ ($menuFixed ?? '') }} {{ $menuCollapsed ?? '' }} {{ $menuFlipped ?? '' }} {{ $menuOffcanvas ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}" dir="{{ $configData['textDirection'] }}" data-theme="{{ $configData['theme'] }}" data-assets-path="{{ asset('/assets') . '/' }}" data-base-url="{{url('/')}}" data-framework="laravel" data-template="{{ $configData['layout'] . '-menu-' . $configData['theme'] . '-' . $configData['style'] }}" get-plan-route-path="{{url('/getplanlar')}}" get-eanace-route-path="{{url('/geteanace')}}" get-cat22-route-path="{{url('/get22cats')}}" get-catsmiic-route-path="{{url('/getSmiiccats')}}" get-cat27001-route-path="{{url('/get27001cats')}}" get-cat50001-route-path="{{url('/get50001cats')}}" get-denetim-onerilen-basdenetci-path="{{url('/getDenetimOnerilenBasdenetciRoute')}}" get-onerilen-karar-uyeleri-path="{{url('/getOnerilenKararUyeleriRoute')}}" get-basdenetci-path="{{url('/getBasdenetciRoute')}}" get-denetci-path="{{url('/getDenetciRoute')}}" get-teknik-uzman-path="{{url('/getTeknikUzmanRoute')}}" get-gozlemci-path="{{url('/getGozlemciRoute')}}" get-iku-path="{{url('/getIkuRoute')}}" get-aday-denetci-path="{{url('/getAdayDenetciRoute')}}" denetim-takvimi-path="{{url('/denetimTakvimiRoute')}}" tblbelgelifirmalarals05-path="{{url('/tblbelgelifirmalarals05Route')}}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>@yield('title') |
    {{ config('variables.templateName') }}</title>

{{--  <title>@yield('title') |--}}
{{--    {{ config('variables.templateName') ? config('variables.templateName') : 'TemplateName' }} ---}}
{{--    {{ config('variables.templateSuffix') ? config('variables.templateSuffix') : 'TemplateSuffix' }}</title>--}}
  <meta name="description" content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
  <meta name="keywords" content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Canonical SEO -->
  <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />



  <!-- Include Styles -->
  <!-- $isFront is used to append the front layout styles only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/styles' . $isFront)

  <!-- Include Scripts for customizer, helper, analytics, config -->
  <!-- $isFront is used to append the front layout scriptsIncludes only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scriptsIncludes' . $isFront)
  <script>

    function myRoutes(asama, pno){
      let myrt = "{{route('crm-planlama', ['asama' => ':asama', 'pno' => ':pno'])}}";
      myrt = myrt.replace(":asama", asama);
      myrt = myrt.replace(":pno", pno);

      return myrt;
    }

    function auditPlanRoutes(asama, pno){
      let myrt = "{{route('audit-plan', ['asama' => ':asama', 'pno' => ':pno'])}}";
      myrt = myrt.replace(":asama", asama);
      myrt = myrt.replace(":pno", pno);

      return myrt;
    }

    function checkCevrimRoutes(cevrim, pno, asama){
      let myrt = "{{route('cevrim-planlama', ['cevrim' => ':cevrim', 'pno' => ':pno', 'asama' => ':asama'])}}";
      myrt = myrt.replace(":cevrim", cevrim);
      myrt = myrt.replace(":asama", asama);
      myrt = myrt.replace(":pno", pno);

      return myrt;
    }

    function formKaydetRoutes(rota){
      let myrt = "{{route(":rota")}}";
      myrt = myrt.replace(":rota", rota);

      return myrt;
    }

    function getUserViewUrl(param) {
      let myrt = "{{route('user-view-account', ['id' => ':param'])}}";
      myrt = myrt.replace(":param", param);

      // console.log(myrt);
      return myrt;
    }
  </script>
</head>

<body>


  <!-- Layout Content -->
  @yield('layoutContent')
  <!--/ Layout Content -->



  <!-- Include Scripts -->
  <!-- $isFront is used to append the front layout scripts only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scripts' . $isFront)

</body>

</html>
