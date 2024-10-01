<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'hrms'], function () {
    
    Route::get('/leaves/initials',      'LeaveController@initials')->name('leaves.initials');
    Route::post('/leave_types/assign',  'LeaveTypeController@assign')->name('leave_types.assign');
    Route::get('/profile',              'UserController@profile')->name('profile.initials');
    Route::post('/password',            'UserController@password')->name('profile.password');
    Route::get('/users/initials',       'UserController@initials')->name('users.initials');
    Route::get('/users/search',         'UserController@search')->name('users.search');
    
    Route::apiResources([
        '/employee_leave_types' => 'EmployeeLeaveTypeController',
        '/leaves' => 'LeaveController',
        '/leave_types' => 'LeaveTypeController',
    ]);
});