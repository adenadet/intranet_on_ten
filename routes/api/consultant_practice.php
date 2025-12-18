<?php

use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'consultant_practices'], function () {
     
    Route::get( '/sessions/initials',    'SessionController@initials')->name('sessions.initials'); 
    
    Route::apiResources([
        'dashboard'     => 'DashboardController',
        'sessions'      => 'SessionController',
    ]);
});