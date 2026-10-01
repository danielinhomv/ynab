<?php

use App\Http\Controllers\HomePageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePageController::class)->name('home');

Route::livewire('/login', 'pages::login')
    ->middleware('guest')
    ->name('login');

Route::livewire('/registro', 'pages::register')
    ->middleware('guest')
    ->name('register');

Route::livewire('/configuracion', 'pages::account-settings')
    ->middleware('auth')
    ->name('configuracion');

Route::livewire('/planes/crear', 'pages::create-plan')
    ->middleware('auth')
    ->name('planes.crear');

Route::livewire('/planes/{plan}', 'pages::plan')
    ->middleware('auth')
    ->name('planes.show');

Route::livewire('/planes/{plan}/configuracion', 'pages::plan-configuracion')
    ->middleware('auth')
    ->name('plan.configuracion');

Route::livewire('/cuentas/{cuenta}', 'pages::cuenta')
    ->middleware('auth')
    ->name('cuentas.show');
