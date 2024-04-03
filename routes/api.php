<?php

use App\Http\Controllers\Employee\Auth\EmployeeAuthController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Hr\Auth\HrAuthController;
use App\Http\Controllers\Hr\HrController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// auth employee
Route::controller(EmployeeAuthController::class)->group(function () {
    Route::group(['middleware' => ['auth:sanctum', 'employee']], function () {
        Route::post('/employee/logout', 'logout');
    });
    Route::post('/employee/register', 'register');
    Route::post('/employee/login', 'login');
});

// auth hr
Route::controller(HrAuthController::class)->group(function () {
    Route::group(['middleware' => ['auth:sanctum', 'hr']], function () {
        Route::post('/hr/logout', 'logout');
    });
    Route::post('/hr/register', 'register');
    Route::post('/hr/login', 'login');
});

Route::group(['middleware' => ['auth:sanctum', 'employee']], function () {
    Route::controller(EmployeeController::class)->group(function() {
        Route::get('/employee/counter-sisa-cuti', 'counterSisaCuti');
        Route::post('/employee/pengajuan-cuti', 'pengajuanCuti');
        Route::get('/employee/history-pengajuan-cuti', 'listHistoryPengajuanCuti');
    });
});

Route::group(['middleware' => ['auth:sanctum', 'hr']], function () {
    Route::controller(HrController::class)->group(function() {
        Route::post('/hr/new-save', 'tambahKaryawan');
        Route::post('/hr/show-save/{id}', 'ubahKaryawan');
        Route::delete('/hr/delete/{id}', 'hapusKaryawan');

        Route::get('/hr/list-pengajuan-cuti', 'listPengajuanCuti');
        Route::post('/hr/action-pengajuan-cuti/{id}', 'approveRejectPengajuanCuti');
        Route::get('/hr/list-history-pengajuan-cuti', 'listHistoryPengajuanCuti');
    });
});

