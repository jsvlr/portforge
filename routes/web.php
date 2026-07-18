<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home')
    ->name('home');

Route::livewire('/projects', 'pages::projects')
    ->name('projects');
