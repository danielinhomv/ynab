<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Mechanisms\HandleRouting\LivewirePageController;
use Symfony\Component\HttpFoundation\Response;

class HomePageController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $request->route()->action['livewire_component'] = Auth::check()
            ? 'pages::home'
            : 'pages::register';

        return app(LivewirePageController::class)();
    }
}
