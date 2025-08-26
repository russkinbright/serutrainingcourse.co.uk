<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
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
    

        public function boot()
        {
            if (request()->has('utm_source')) {
                Session::put('ref_source', request()->get('utm_source'));
            } elseif (!Session::has('ref_source')) {
                // fallback if no utm and no session
                $referer = request()->headers->get('referer');
                $source = 'Direct';
                if ($referer) {
                    if (str_contains($referer, 'google.')) $source = 'Google';
                    elseif (str_contains($referer, 'bing.')) $source = 'Bing';
                    elseif (str_contains($referer, 'facebook.')) $source = 'Facebook';
                    else $source = parse_url($referer, PHP_URL_HOST);
                }
                Session::put('ref_source', $source);
            }
        }

}
