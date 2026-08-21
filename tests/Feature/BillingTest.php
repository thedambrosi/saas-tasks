<?php

use App\Models\User;

test('guests are redirected to login', function () {
    $this->get('/billing')->assertRedirect('/login');
});

test('authenticated user without subscription sees the subscribe button', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/billing')
        ->assertOk()
        ->assertSee('Assinar');
});
