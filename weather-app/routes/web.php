<?php

use App\Http\Controllers\WeatherStatisticsController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/stats/excel', [WeatherStatisticsController::class, 'exportStatisticsToExcel']);
