<?php

namespace App\Providers;
use Illuminate\Support\Facades\View; // Tambahkan ini
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Models\Message;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {


          View::composer('*', function ($view) {
          $user = auth()->guard('karyawan')->user();

          $unreadMessages = 0;
          if ($user) {
              $unreadMessages = Message::where('is_read', 0)
                                       ->count();
          }

          $view->with('unreadMessages', $unreadMessages);
          });

    }
}
