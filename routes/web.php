<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::livewire('/tasks', 'pages::tasks')->name('tasks');
        Route::livewire('/billing', 'pages::billing')->name('billing');
    });

require __DIR__.'/settings.php';
