<x-mail::message>
    # Sua tarefa está vencendo

    A tarefa **{{ $task->title }}** vence em {{ $task->due_date->format('d/m/Y') }}.

    <x-mail::button :url="route('tasks')">
        Ver minhas tarefas
    </x-mail::button>

    Obrigado por usar o SaaS Tarefas!
</x-mail::message>