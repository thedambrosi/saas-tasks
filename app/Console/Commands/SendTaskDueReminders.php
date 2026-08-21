<?php

namespace App\Console\Commands;

use App\Mail\TaskDueReminder;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTaskDueReminders extends Command
{
    protected $signature = 'tasks:send-due-reminders';

    protected $description = 'Envia lembrete por e-mail de tarefas vencendo amanhã para assinantes Pro';

    public function handle(): void
    {
        $tasks = Task::whereDate('due_date', now()->addDay())
            ->where('completed', false)
            ->with('user')
            ->get()
            ->filter(fn(Task $task) => $task->user->subscribed('default'));

        foreach ($tasks as $task) {
            Mail::to($task->user->email)->queue(new TaskDueReminder($task));
        }

        $this->info("Enviados {$tasks->count()} lembretes.");
    }
}
