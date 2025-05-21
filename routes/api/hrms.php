<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'hrms'], function () {

    Route::get( '/designations/search/{id}',            'DesignationController@search')->name('designations.search');
    Route::put( '/employees/assign_manager/{id}',       'EmployeeController@assign_manager')->name('employees.assign_manager');
    Route::post('/employees/import',                    'EmployeeController@import')->name('employees.import');
    Route::get( '/employees/initials',                  'EmployeeController@initials')->name('employees.initials');
    Route::get( '/employees/search/{id}',               'EmployeeController@search')->name('employees.search');
    Route::put( '/employees/update_status/{id}',        'EmployeeController@update_status')->name('employees.update_status');
    Route::get( '/employees/user/{id}',                 'EmployeeController@user')->name('employees.user');
    Route::get( '/employee_leave_types/assigned',       'EmployeeLeaveTypeController@assigned')->name('employee_leave_types.assigned');
    Route::get( '/employee_leave_types/initials',       'EmployeeLeaveTypeController@initials')->name('employee_leave_types.initials');
    Route::put( '/leaves/confirm/{id}',                 'LeaveController@confirm')->name('leaves.confirm');
    Route::get( '/leaves/initials',                     'LeaveController@initials')->name('leaves.initials');
    Route::put( '/leave_allowances/confirm/{id}',       'LeaveAllowanceController@confirm')->name('leave_allowances.confirm');
    Route::post('/leave_types/assign',                  'LeaveTypeController@assign')->name('leave_types.assign');
    Route::get( '/leave_types/initials',                'LeaveTypeController@initials')->name('leave_types.initials');
    Route::get( '/profile',                             'UserController@profile')->name('profile.initials');
    Route::post('/password',                            'UserController@password')->name('profile.password');
    Route::get( '/users/initials',                      'UserController@initials')->name('users.initials');
    Route::get( '/users/search',                        'UserController@search')->name('users.search');
    
    Route::apiResources([
        '/designations'         => 'DesignationController',
        '/employees'            => 'EmployeeController',
        '/employee_leave_types' => 'EmployeeLeaveTypeController',
        '/leaves'               => 'LeaveController',
        '/leave_allowances'     => 'LeaveAllowanceController',
        '/leave_types'          => 'LeaveTypeController',
    ]);
});