<?php

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?int $openPlanId = null;

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Plan>
     */
    #[Computed]
    public function plans()
    {
        $user = Auth::user();

        if ($user === null) {
            return new \Illuminate\Database\Eloquent\Collection;
        }

        return $user->plans()->orderBy('id')->get();
    }

    public function open(int $planId): void
    {
        $plan = Plan::query()
            ->where('user_id', Auth::id())
            ->whereKey($planId)
            ->first();

        if ($plan === null) {
            return;
        }

        $this->openPlanId = $plan->id;
        $this->dispatch('plan-abierto', planId: $plan->id)->to('plan-sobres');
    }
};
?>

<div>
    @if (auth()->check())
        <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-emerald-100">
            <div class="flex items-center justify-between gap-3">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Planes</p>
                <a href="{{ route('planes.crear') }}" wire:navigate class="rounded-xl bg-forest px-3 py-2 text-sm font-semibold text-white shadow-sm">Crear plan</a>
            </div>

            @if ($this->plans->isEmpty())
                <p class="mt-4 text-sm text-slate-600">Aún no tienes planes.</p>
            @else
                <ul class="mt-3 space-y-1">
                    @foreach ($this->plans as $plan)
                        <li>
                            <button
                                type="button"
                                wire:click="open({{ $plan->id }})"
                                @class([
                                    'flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm',
                                    'bg-mint font-semibold text-forest' => $openPlanId === $plan->id,
                                    'text-slate-700' => $openPlanId !== $plan->id,
                                ])
                            >
                                <span>{{ $plan->name }}</span>
                                <span>{{ $plan->currency }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    @endif
</div>
