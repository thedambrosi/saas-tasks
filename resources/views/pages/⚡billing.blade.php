<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Assinatura')] class extends Component {
    public function subscribe(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->subscribed('default')) {
            return;
        }

        $checkout = $user->newSubscription('default', config('subscriptions.pro_price_id'))
            ->checkout([
                'success_url' => route('billing'),
                'cancel_url' => route('billing'),
            ]);

        $this->redirect($checkout->url);
    }

    public function manageSubscription(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->redirect($user->billingPortalUrl(route('billing')));
    }

    public function with(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return [
            'subscribed' => $user->subscribed('default'),
        ];
    }
};
?>

<div class="flex flex-col gap-4 p-4">
    @if ($subscribed)
    <div class="rounded-lg border border-green-300 bg-green-50 p-4 text-green-800 dark:border-green-700 dark:bg-green-900/30 dark:text-green-300">
        <p>Você já é assinante do <strong>Plano Pro</strong>. 🎉</p>
        <button
            wire:click="manageSubscription"
            class="mt-3 rounded-lg border border-green-700 px-4 py-2 text-sm font-medium text-green-800 transition hover:bg-green-100 dark:border-green-400 dark:text-green-300 dark:hover:bg-green-900/50">
            Gerenciar assinatura
        </button>
    </div>
    @else
    <div class="rounded-lg border border-neutral-200 p-6 dark:border-neutral-700">
        <h2 class="text-lg font-semibold">Plano Pro</h2>
        <p class="mt-1 text-sm text-neutral-500">R$ 19,90/mês — tarefas ilimitadas e lembretes por e-mail.</p>
        <button
            wire:click="subscribe"
            class="mt-4 rounded-lg bg-neutral-900 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all duration-150 hover:scale-105 hover:bg-neutral-700 active:scale-95 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
            Assinar
        </button>
    </div>
    @endif
</div>