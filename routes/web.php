<?php

use App\Http\Controllers\HomePageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePageController::class)->name('home');

Route::livewire('/login', 'pages::login')
    ->middleware('guest')
    ->name('login');

Route::livewire('/configuracion', 'pages::account-settings')
    ->middleware('auth')
    ->name('configuracion');
