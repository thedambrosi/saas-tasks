<?php

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tarefas')] class extends Component {
    public string $title = '';
    public ?string $due_date = null;

    public function addTask(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user->subscribed('default') && $user->tasks()->count() >= config('subscriptions.free_task_limit')) {
            $this->addError('title', 'Você atingiu o limite de ' . config('subscriptions.free_task_limit') . ' tarefas do plano Free. Assine o Pro para continuar.');
            return;
        }

        $user->tasks()->create([
            'title' => $this->title,
            'due_date' => $this->due_date,
        ]);

        $this->reset(['title', 'due_date']);
    }
    public function toggle(Task $task): void
    {
        $this->authorize('update', $task);

        $task->update(['completed' => ! $task->completed]);
    }

    public function delete(Task $task): void
    {
        $this->authorize('delete', $task);

        $task->delete();
    }
    public function with(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return [
            'tasks' => $user->tasks()->latest()->get(),
        ];
    }
};
?>

<div class="flex flex-col gap-6 p-4">
    <form wire:submit="addTask" class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="flex-1">
            <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Título</label>
            <input
                type="text"
                wire:model="title"
                class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm shadow-sm transition focus:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-neutral-300 dark:border-neutral-700 dark:bg-neutral-800 dark:focus:ring-neutral-600">
            @error('title') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Prazo</label>
            <input
                type="date"
                wire:model="due_date"
                class="rounded-lg border border-neutral-300 px-3 py-2 text-sm shadow-sm transition focus:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-neutral-300 dark:border-neutral-700 dark:bg-neutral-800 dark:focus:ring-neutral-600">
        </div>
        <button
            type="submit"
            class="rounded-lg bg-neutral-900 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all duration-150 hover:scale-105 hover:bg-neutral-700 active:scale-95 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
            Adicionar
        </button>
    </form>

    <ul class="flex flex-col gap-2">
        @forelse ($tasks as $task)
        <li class="flex items-center justify-between rounded-lg border border-neutral-200 p-3 shadow-sm transition hover:shadow-md dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <input
                    type="checkbox"
                    wire:click="toggle({{ $task->id }})"
                    @checked($task->completed)
                class="h-4 w-4 rounded border-neutral-300 text-neutral-900 transition focus:ring-neutral-400 dark:border-neutral-600"
                >
                <span class="{{ $task->completed ? 'text-neutral-400 line-through' : '' }}">
                    {{ $task->title }}
                </span>
                @if ($task->due_date)
                <span class="text-xs text-neutral-400">{{ $task->due_date->format('d/m/Y') }}</span>
                @endif
            </div>
            <button
                wire:click="delete({{ $task->id }})"
                wire:confirm="Excluir esta tarefa?"
                class="text-sm text-red-500 transition hover:text-red-700 hover:underline">
                Excluir
            </button>
        </li>
        @empty
        <li class="text-neutral-400">Nenhuma tarefa ainda.</li>
        @endforelse
    </ul>
</div>