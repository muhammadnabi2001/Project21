<?php

use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\ProfileController;
use App\Livewire\CategoryComponent;
use App\Livewire\CompanyComponent;
use App\Livewire\MainComponent;
use App\Livewire\MealComponent;
use App\Livewire\OrderComponent;
use App\Livewire\OrderStatusComponent;
use App\Livewire\ProductComponent;
use App\Livewire\TelegramComponent;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/', MainComponent::class);
    Route::get('/category', CategoryComponent::class);
    Route::get('/bot', TelegramComponent::class);
    Route::get('/products', ProductComponent::class);
    Route::get('/meal', MealComponent::class)->name('meal');
    Route::get('/order', OrderComponent::class);
    Route::get('/orders',[OrderStatusController::class,'index']);
    Route::get('/company',CompanyComponent::class);
});

require __DIR__ . '/auth.php';
