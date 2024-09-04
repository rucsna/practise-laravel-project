<?php

use App\Http\Controllers\WeatherStatisticsController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $images = [
        'carousel1.jpg',
        'carousel2.jpg',
        'carousel3.jpg',
        'carousel4.jpg',
        'carousel5.jpg'
    ];

    return view('welcome', compact('images'));
});

Route::get('/stats/excel', [WeatherStatisticsController::class, 'exportStatisticsToExcel']);
