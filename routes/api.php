<?php

use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [\App\Http\Controllers\Controller::class, 'login']);
Route::post('/auth/logout', [\App\Http\Controllers\Controller::class, 'logout']);
Route::post('/auth/register', [\App\Http\Controllers\Controller::class, 'register']);
Route::get('/product', [\App\Http\Controllers\Controller::class, 'list']);
