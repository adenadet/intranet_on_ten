<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'author' => 'Squarem Information Communications',
    'name' => 'St Nicholas Hospital Intranet',
    'name_short' => 'St. Nicholas Hospital',
    'short_code' => 'SNH',
    'logo' => 'img/background/logo/snh-square.png',
    'logo_w' => 'img/background/logo/snh-square.png',
    'logo_h' => 'img/background/logo/snh-square.png',
    'logo_s' => 'img/background/logo/snh-square.png',
    'logo_h_w' => 'img/background/logo/snh-square.png',
    'logo_s_w' => 'img/background/logo/snh-square.png',
    'website' => 'https://saintnicholashospital.com',
    'email' => 'itsupport@saintnicholashospital.com',
    'phone' => '07012345678',
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost:8000'),
    'asset_url' => env('ASSET_URL', null),
    'timezone' => 'Africa/Lagos',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    
    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],
    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        Intervention\Image\ImageServiceProvider::class,
        //Kodegrenade\NaijaFaker\OtpServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class,
        Unicodeveloper\Paystack\PaystackServiceProvider::class,

    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'Image' => Intervention\Image\Facades\Image::class,
        // 'NaijaFaker' => Kodegrenade\NaijaFaker\NaijaFaker::class,
        'Paystack' => Unicodeveloper\Paystack\Facades\Paystack::class,
        'QrCode' => SimpleSoftwareIO\QrCode\Facades\QrCode::class,
    ])->toArray(),

];
