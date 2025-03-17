@php
  $containerNav = $containerNav ?? 'container-fluid';
  $navbarDetached = ($navbarDetached ?? '');
@endphp

  <!-- Navbar -->
@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
  <nav
    class="layout-navbar {{$containerNav}} navbar navbar-expand-xl {{$navbarDetached}} align-items-center bg-navbar-theme"
    id="layout-navbar">
    @endif
    @if(isset($navbarDetached) && $navbarDetached == '')
      <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="{{$containerNav}}">
          @endif

          <!--  Brand demo (display only for navbar-full and hide on below xl) -->
          @if(isset($navbarFull))
            <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
              <a href="{{url('/')}}" class="app-brand-link gap-2">
                <span
                  class="app-brand-logo demo">@include('_partials.macros',["width"=>50,"withbg"=>'var(--bs-primary)'])</span>
                <span class="app-brand-text demo menu-text fw-bold">{{config('variables.templateName')}}</span>
              </a>
              @if(isset($menuHorizontal))
                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                  <i class="mdi mdi-close align-middle"></i>
                </a>
              @endif
            </div>
          @endif

          <!-- ! Not required for layout-without-menu -->
          @if(!isset($navbarHideToggle))
            <div
              class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ?' d-xl-none ' : '' }}">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="mdi mdi-menu mdi-24px"></i>
              </a>
            </div>
          @endif

          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

            @if(!isset($menuHorizontal))
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div class="nav-item navbar-search-wrapper mb-0">
                  <a class="nav-item nav-link search-toggler fw-normal px-0" href="javascript:void(0);">
                    <i class="mdi mdi-magnify mdi-24px scaleX-n1-rtl"></i>
                    <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                  </a>
                </div>
              </div>
              <!-- /Search -->
            @endif
            <!-- company name -->
{{--            <div class="navbar-nav align-items-center ms-1">--}}
{{--              <div class="nav-item navbar-search-wrapper mb-0">--}}
{{--                <a class="nav-item nav-link fw-normal px-0" href="#">--}}
{{--                  <i class="mdi mdi-office-building mdi-24px scaleX-n1-rtl"></i>--}}
{{--                  <span class="d-none d-md-inline-block text-danger">{{ \App\Http\Controllers\Planlama\Plan::getCompanyName(Auth::user()->kurulusid) }}</span>--}}
{{--                </a>--}}
{{--              </div>--}}
{{--            </div>--}}
            <!-- /company name -->

            <ul class="navbar-nav flex-row align-items-center ms-auto">
              @if(isset($menuHorizontal))
                <!-- Search -->
                <li class="nav-item navbar-search-wrapper me-1 me-xl-0">
                  <a class="nav-link search-toggler fw-normal" href="javascript:void(0);">
                    <i class="mdi mdi-magnify mdi-24px scaleX-n1-rtl"></i>
                  </a>
                </li>
                <!-- /Search -->
              @endif
              <!-- Language -->
              <li class="nav-item dropdown-language dropdown me-1 me-xl-0">
                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                   href="javascript:void(0);" data-bs-toggle="dropdown">
                  <i class='mdi mdi-translate mdi-24px'></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'tr' ? 'active' : '' }}" href="{{url('lang/tr')}}"
                       data-language="tr" data-text-direction="ltr">
                      <span class="align-middle">Türkçe</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{url('lang/en')}}"
                       data-language="en" data-text-direction="ltr">
                      <span class="align-middle">English</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="{{url('lang/fr')}}"
                       data-language="fr" data-text-direction="ltr">
                      <span class="align-middle">French</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{url('lang/ar')}}"
                       data-language="ar" data-text-direction="rtl">
                      <span class="align-middle">Arabic</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'de' ? 'active' : '' }}" href="{{url('lang/de')}}"
                       data-language="de" data-text-direction="ltr">
                      <span class="align-middle">German</span>
                    </a>
                  </li>
                </ul>
              </li>
              <!--/ Language -->

              @if($configData['hasCustomizer'] == true)
                <!-- Style Switcher -->
                <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                  <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                     href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class='mdi mdi-24px'></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                    <li>
                      <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                        <span class="align-middle"><i class='mdi mdi-weather-sunny me-2'></i>Light</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                        <span class="align-middle"><i class="mdi mdi-weather-night me-2"></i>Dark</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                        <span class="align-middle"><i class="mdi mdi-monitor me-2"></i>System</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ Style Switcher -->
              @endif

              <!-- Quick links  -->
              <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-1 me-xl-0">
                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                   href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                   aria-expanded="false">
                  <i class='mdi mdi-view-grid-plus-outline mdi-24px'></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0">
                  <div class="dropdown-menu-header border-bottom">
                    <div class="dropdown-header d-flex align-items-center py-3">
                      <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                      <a href="javascript:void(0)" class="dropdown-shortcuts-add text-muted" data-bs-toggle="tooltip"
                         data-bs-placement="top" title="Add shortcuts"><i
                          class="mdi mdi-view-grid-plus-outline mdi-24px"></i></a>
                    </div>
                  </div>
                  <div class="dropdown-shortcuts-list scrollable-container">
                    <div class="row row-bordered overflow-visible g-0">
                      <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-calendar fs-4"></i>
                  </span>
                        <a href="{{url('app/calendar')}}" class="stretched-link">Calendar</a>
                        <small class="text-muted mb-0">Appointments</small>
                      </div>
                      <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-file-document-outline fs-4"></i>
                  </span>
                        <a href="{{url('app/invoice/list')}}" class="stretched-link">Invoice App</a>
                        <small class="text-muted mb-0">Manage Accounts</small>
                      </div>
                    </div>
                    <div class="row row-bordered overflow-visible g-0">
                      <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-account-outline fs-4"></i>
                  </span>
                        <a href="{{url('app/user/list')}}" class="stretched-link">User App</a>
                        <small class="text-muted mb-0">Manage Users</small>
                      </div>
                      <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-shield-check-outline fs-4"></i>
                  </span>
                        <a href="{{url('app/access-roles')}}" class="stretched-link">Role Management</a>
                        <small class="text-muted mb-0">Permission</small>
                      </div>
                    </div>
                    <div class="row row-bordered overflow-visible g-0">
                      <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-chart-pie-outline fs-4"></i>
                  </span>
                        <a href="{{url('/')}}" class="stretched-link">Dashboard</a>
                        <small class="text-muted mb-0">Analytics</small>
                      </div>
                      <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-cog-outline fs-4"></i>
                  </span>
                        <a href="{{url('pages/account-settings-account')}}" class="stretched-link">Setting</a>
                        <small class="text-muted mb-0">Account Settings</small>
                      </div>
                    </div>
                    <div class="row row-bordered overflow-visible g-0">
                      <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-help-circle-outline fs-4"></i>
                  </span>
                        <a href="{{url('pages/faq')}}" class="stretched-link">FAQs</a>
                        <small class="text-muted mb-0">FAQs & Articles</small>
                      </div>
                      <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-dock-window fs-4"></i>
                  </span>
                        <a href="{{url('modal-examples')}}" class="stretched-link">Modals</a>
                        <small class="text-muted mb-0">Useful Popups</small>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <!-- Quick links -->

              <!-- Notification -->
                <?php
                use Illuminate\Support\Facades\DB;

                $say = 0;       // Gösterilecek toplam bildirim sayısı
                $dugme = "";    // Bildirimleri HTML olarak biriktireceğimiz değişken

// Farklı listeler:
                $denetimNotifs = [];   // Denetim bildirimlerini tutacağız
                $expiryNotifs  = [];   // Sertifika bitiş bildirimlerini tutacağız

// planlar & basvuru verilerini tek sorguyla çekiyoruz
                $planlar = DB::table('planlar')
                  ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
                  ->select('basvuru.*', 'planlar.*')
                  ->orderBy('planlar.planno', 'DESC')
                  ->get();

// Bugünün tarihini (saat 00:00:00) timestamp olarak almak
                $bugun = strtotime(date("Y-m-d"));             // Bugünün timestamp değeri (00:00)
                $ucAySonra = strtotime("+2 months", $bugun);   // Bugünden 3 ay sonrası timestamp

                foreach ($planlar as $ret) {
                  // 1) Denetim tarihi hesapla
                  list($dentarihi, $dtipi) = \App\Http\Controllers\Planlama\Plan::getDenetimTarihi($ret);

                  // 2) Denetim başlangıç tarihi (string) virgüllü mü diye bak
                  $denbastarihi = \App\Http\Controllers\Planlama\Plan::getDenetimBaslangicTarihi($dentarihi);
                  $dbt = strtotime($denbastarihi);

                  // 3) Denetim bildirimi: eğer denetim başlangıcı bugünden ileri ise
                  if ($dbt && $dbt > $bugun) {
                    // Burada bildirim HTML'ini hemen `$dugme`ye eklemek yerine,
                    // denetimNotifs listesine ekleyelim (sıralamak istiyorsak benzer mantıkla).
                    // Şimdilik isterseniz ekleyeceğiz, ya da direkt `$dugme .=$...` diyebilirsiniz.
                    $denetimNotifs[] = [
                      'timestamp' => $dbt,
                      'html'      => \App\Http\Controllers\Planlama\Plan::buildDenetimBildirimHTML($ret, $dentarihi, $dtipi)
                    ];
                  }

                  // 4) Belge geçerlilik süresi yaklaşmış mı?
                  //    - g1 / g2 için bitiş tarihinden 2 ay önce
                  //    - yb için bitiş tarihinden 1 ay önce
                  if (!empty($ret->bitistarihi)) {
                    $bitisTarihTimestamp = strtotime($ret->bitistarihi);
                    if ($bitisTarihTimestamp) {
                      $oncekiTarihTimestamp = \App\Http\Controllers\Planlama\Plan::getOncekiTarih($bitisTarihTimestamp, $ret->asama);

                      // eğer bugün, bu "önceki tarih"i geçmişse veya tam o tarihteyse, bildirim göster
                      if ($bitisTarihTimestamp > $bugun && $bitisTarihTimestamp <= $ucAySonra) {
                        $expiryNotifs[] = [
                          'timestamp' => $bitisTarihTimestamp,
                          'html'      => \App\Http\Controllers\Planlama\Plan::buildBelgeSureBildirimHTML($ret)
                        ];
                      }
                    }
                  }
                }

// Artık elimizde iki ayrı liste var:
// - $denetimNotifs (timestamp => $dbt)
// - $expiryNotifs  (timestamp => $bitisTarihTimestamp)

                /**
                 * 5) Sertifika bitiş bildirimlerini, en yakın tarihten en uzak tarihe sıralayalım
                 *    (timestamp ASC)
                 */
                usort($expiryNotifs, function ($a, $b) {
                  return $a['timestamp'] <=> $b['timestamp'];
                });

// Eğer denetim bildiriminin de tarihsel sıralı görünmesini istiyorsanız, benzer şekilde:
// usort($denetimNotifs, function ($a, $b) {
//     return $a['timestamp'] <=> $b['timestamp'];
// });

                /**
                 * 6) HTML birleştirme ve bildirim sayısı güncelleme
                 *    - Önce denetim bildirimlerini ekleyip ardından sertifika bitiş bildirimi ekleyebilirsiniz
                 *    - Ya da sadece sertifika bitiş bildirimi sıralı olacaksa, denetim bildirimlerini en başa ekleyip
                 *      sonra expiryNotifs'i sıralı olarak ekleyebilirsiniz.
                 */

// Örneğin, denetim bildirimleri ekle
                foreach ($denetimNotifs as $dn) {
                  $dugme .= $dn['html'];
                  $say++;
                }

// Sonra sertifika bitiş bildirimlerini tarihsel sırayla ekle
                foreach ($expiryNotifs as $en) {
                  $dugme .= $en['html'];
                  $say++;
                }
                $displayNumber = ($say > 99) ? "99+" : $say;

// Bildirim dropdown HTML (sadece bu kısmı footer'a / blade'e yerleştiriyoruz)
                ?>
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-1">
                  <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                     href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                     aria-expanded="false">
                    <i class="mdi mdi-bell-outline mdi-24px"></i>
                    <span class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border">
      <?= $say; ?>
    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                      <div class="dropdown-header d-flex align-items-center py-3">
                        <h6 class="mb-0 me-auto">Bildirimler</h6>
                        <span class="badge rounded-pill bg-label-primary custom-badge" data-count="<?= $displayNumber; ?>">
          <?= $say; ?> Yeni
        </span>
                      </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                      <ul class="list-group list-group-flush">
                        <?= $dugme ?>
                      </ul>
                    </li>
                    <li class="dropdown-menu-footer border-top p-2">
                      <a href="javascript:void(0);" class="btn btn-primary d-flex justify-content-center">
                        Tüm bildirimleri gör
                      </a>
                    </li>
                  </ul>
                </li>

              <!--/ Notification -->

              <!-- User -->
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}"
                         alt class="w-px-40 h-auto rounded-circle">
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item"
                       href="{{ Route::has('profile.show') ? route('profile.show') : url('pages/profile-user') }}">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <img
                              src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}"
                              alt class="w-px-40 h-auto rounded-circle">
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          @if (Auth::check())
                            <span class="fw-medium d-block">
                      {{ Auth::user()->name }}
                    </span>
                            <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                          @else
                            <span class="fw-medium d-block">
                      John Doe
                    </span>
                            <small class="text-muted">Admin</small>

                          @endif
                        </div>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li>
                    <a class="dropdown-item"
                       href="{{ Route::has('profile.show') ? route('user-view-account', ['id' => Auth::user()->id]) : url('pages/profile-user') }}">
                      <i class="mdi mdi-account-outline me-2"></i>
                      <span class="align-middle">My Profile</span>
                    </a>
                  </li>
                  @if (Auth::check() && Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <li>
                      <a class="dropdown-item" href="{{ route('api-tokens.index') }}">
                        <i class='mdi mdi-key-outline me-2'></i>
                        <span class="align-middle">API Tokens</span>
                      </a>
                    </li>
                  @endif
{{--                  <li>--}}
{{--                    <a class="dropdown-item" href="{{url('app/user/view/billing')}}">--}}
{{--                      <i class="mdi mdi-credit-card-outline me-2"></i>--}}
{{--                      <span class="align-middle">Billing</span>--}}
{{--                    </a>--}}
{{--                  </li>--}}
                  @if (Auth::User() && Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <h6 class="dropdown-header">Manage Team</h6>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item"
                         href="{{ Auth::user() ? route('teams.show', Auth::user()->currentTeam->id) : 'javascript:void(0)' }}">
                        <i class='mdi mdi-cog-outline me-2'></i>
                        <span class="align-middle">Team Settings</span>
                      </a>
                    </li>
                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                      <li>
                        <a class="dropdown-item" href="{{ route('teams.create') }}">
                          <i class='mdi mdi-account-outline me-2'></i>
                          <span class="align-middle">Create New Team</span>
                        </a>
                      </li>
                    @endcan
                    @if (Auth::user()->allTeams()->count() > 1)
                      <li>
                        <div class="dropdown-divider"></div>
                      </li>
                      <li>
                        <h6 class="dropdown-header">Switch Teams</h6>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                      </li>
                    @endif
                    @if (Auth::user())
                      @foreach (Auth::user()->allTeams() as $team)
                        {{-- Below commented code read by artisan command while installing jetstream. !! Do not remove if you want to use jetstream. --}}

                        <x-switchable-team :team="$team"/>
                      @endforeach
                    @endif
                  @endif
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  @if (Auth::check())
                    <li>
                      <a class="dropdown-item" href="{{ route('logout') }}"
                         onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class='mdi mdi-logout me-2'></i>
                        <span class="align-middle">Logout</span>
                      </a>
                    </li>
                    <form method="POST" id="logout-form" action="{{ route('logout') }}">
                      @csrf
                    </form>
                  @else
                    <li>
                      <a class="dropdown-item"
                         href="{{ Route::has('login') ? route('login') : url('auth/login-basic') }}">
                        <i class='mdi mdi-login me-2'></i>
                        <span class="align-middle">Login</span>
                      </a>
                    </li>
                  @endif
                </ul>
              </li>
              <!--/ User -->
            </ul>
          </div>

          <!-- Search Small Screens -->
          <div
            class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
            <input type="text"
                   class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0"
                   placeholder="Search..." aria-label="Search...">
            <i class="mdi mdi-close search-toggler cursor-pointer"></i>
          </div>
          @if(!isset($navbarDetached))
        </div>
        @endif
      </nav>
      <!-- / Navbar -->

