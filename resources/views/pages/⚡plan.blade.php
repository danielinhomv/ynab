<?php

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Plan')] class extends Component
{
    public Plan $plan;

    public function mount(Plan $plan): void
    {
        abort_unless((int) $plan->user_id === (int) Auth::id(), 404);

        $this->plan = $plan->load('categories.sobres');
    }
};
?>

<div class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-lg space-y-4">
        @if (session('status'))
            <p class="rounded-2xl bg-white px-4 py-3 text-sm font-medium text-forest shadow-sm ring-1 ring-emerald-100">
                {{ session('status') }}
            </p>
        @endif

        <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-emerald-100 sm:p-8">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-forest">Plan</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $plan->name }}</h1>

            <div class="mt-6 space-y-4">
                @foreach ($plan->categories as $category)
                    <section>
                        <h2 class="text-sm font-semibold text-slate-800">{{ $category->name }}</h2>
                        <ul class="mt-2 space-y-1">
                            @foreach ($category->sobres as $sobre)
                                <li class="text-sm text-slate-600">{{ $sobre->name }}</li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>
        </article>
    </div>
</div>
