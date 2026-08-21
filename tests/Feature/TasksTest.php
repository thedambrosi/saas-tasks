<?php

use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get('/tasks')->assertRedirect('/login');
});

test('user can create a task', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('title', 'Estudar Laravel')
        ->call('addTask')
        ->assertHasNoErrors();

    expect($user->tasks()->count())->toBe(1);
});

test('free user is blocked after reaching the task limit', function () {
    $user = User::factory()->create();
    Task::factory()->count(7)->for($user)->create();

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->set('title', 'Tarefa extra')
        ->call('addTask')
        ->assertHasErrors('title');

    expect($user->tasks()->count())->toBe(7);
});

test('user cannot toggle another users task', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $task = Task::factory()->for($owner)->create();

    Livewire::actingAs($intruder)
        ->test('pages::tasks')
        ->call('toggle', $task)
        ->assertForbidden();

    expect($task->fresh()->completed)->toBeFalse();
});

test('user cannot delete another users task', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $task = Task::factory()->for($owner)->create();

    Livewire::actingAs($intruder)
        ->test('pages::tasks')
        ->call('delete', $task)
        ->assertForbidden();

    expect(Task::find($task))->not->toBeNull();
});

test('owner can toggle their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['completed' => false]);

    Livewire::actingAs($user)
        ->test('pages::tasks')
        ->call('toggle', $task);

    expect($task->fresh()->completed)->toBeTrue();
});
