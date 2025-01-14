<?php

use App\Livewire\CategoryComponent;
use App\Livewire\MainComponent;
use App\Livewire\ProductComponent;
use App\Livewire\TelegramComponent;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('index');
// });
Route::get('/', MainComponent::class);
Route::get('/category', CategoryComponent::class);
Route::get('/bot', TelegramComponent::class);
Route::get('products', ProductComponent::class);
