<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public function logout(): void
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('home'), navigate: true);
    }
};
?>

<div>
    <button type="button" wire:click="logout" class="block w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-mint">
        Cerrar sesión
    </button>
</div>
