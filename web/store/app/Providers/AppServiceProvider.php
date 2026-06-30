<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $client = config('firebase.client');
        if (empty($client['apiKey'])) {
            return;
        }

        $expires = time() + 3600;
        $cookies = [
            'XSRF-TOKEN-AK' => $client['apiKey'],
            'XSRF-TOKEN-AD' => $client['authDomain'],
            'XSRF-TOKEN-DU' => $client['databaseURL'],
            'XSRF-TOKEN-PI' => $client['projectId'],
            'XSRF-TOKEN-SB' => $client['storageBucket'],
            'XSRF-TOKEN-MS' => $client['messagingSenderId'],
            'XSRF-TOKEN-AI' => $client['appId'],
            'XSRF-TOKEN-MI' => $client['measurementId'],
        ];

        foreach ($cookies as $name => $value) {
            if ($value !== null && $value !== '') {
                setcookie($name, bin2hex($value), $expires, '/');
            }
        }
    }
}
