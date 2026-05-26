<?php

use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'consultant_practices'], function () {
     
    Route::get( '/accounts/initials', 'AccountController@initials')->name('accounts.initials'); 
    Route::get( '/consultants/initials', 'ConsultantController@initials')->name('consultants.initials'); 
    Route::get( '/consultant_services/consultant/{id}', 'ConsultantServiceController@consultant')->name('consultant_services.consultant'); 
    Route::get( '/consultant_services/initials', 'ConsultantServiceController@initials')->name('consultant_services.initials'); 
    Route::post('/consultant_services/multiple', 'ConsultantServiceController@multiple')->name('consultant_services.multiple'); 
    Route::put( '/payments/{id}/confirm', 'PaymentController@confirm')->name('payments.confirm');
    Route::put( '/payments/{id}/reverse', 'PaymentController@reverse')->name('payments.reverse');
    Route::get( '/payments/initials', 'PaymentController@initials')->name('payments.initials');
    Route::get( '/services/initials', 'ServiceController@initials')->name('services.initials'); 
    Route::post('/sessions/confirm_payment', 'SessionController@confirm_payment')->name('sessions.confirm_payment'); 
    Route::post('/sessions/confirm_service', 'SessionController@confirm_service')->name('sessions.confirm_service'); 
    Route::get( '/sessions/initials',    'SessionController@initials')->name('sessions.initials'); 
    
    Route::apiResources([
        'accounts'              => 'AccountController',
        'companies'             => 'CompanyController',
        'consultants'           => 'ConsultantController',
        'consultant_services'   => 'ConsultantServiceController',
        'dashboard'             => 'DashboardController',
        'patients'              => 'PatientController',
        'payments'              => 'PaymentController',
        'services'              => 'ServiceController',
        'sessions'              => 'SessionController',
        'specialties'           => 'SpecialtyController',
    ]);
});