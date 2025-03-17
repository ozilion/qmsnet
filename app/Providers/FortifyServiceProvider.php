<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Auth\Events\Login;

class FortifyServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Fortify::createUsersUsing(CreateNewUser::class);
    Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
    Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
    Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

    RateLimiter::for('login', function (Request $request) {
      $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
      return Limit::perMinute(5)->by($throttleKey);
    });

    RateLimiter::for('two-factor', function (Request $request) {
      return Limit::perMinute(5)->by($request->session()->get('login.id'));
    });

    // Login event'ine dinleyici ekleyerek, kullanıcının rolüne göre yönlendirme URL'sini ayarlıyoruz.
    Event::listen(Login::class, function ($event) {
      $user = $event->user;
      // Spatie/laravel-permission kullanıyorsanız; rolleri kontrol edebilirsiniz.
      if ($user->hasRole('superadmin')) {
        session(['url.intended' => url('/')]);
      } else
      if ($user->hasRole('admin')) {
        session(['url.intended' => route('sbasvuruyeni')]);
      } elseif ($user->hasRole('bgmuduru')) {
        session(['url.intended' => route('auditor-user-managementt')]);
      } elseif ($user->hasRole('auditor')) {
        session(['url.intended' => route('sbasvuruyeni')]);
      } elseif ($user->hasRole('tuzman')) {
        session(['url.intended' => route('sbasvuruyeni')]);
      } elseif ($user->hasRole('iku')) {
        session(['url.intended' => route('sbasvuruyeni')]);
      } else {
        session(['url.intended' => route('login')]);
      }
    });
  }
}
