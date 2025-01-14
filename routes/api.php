<?php

use App\Http\Controllers\Api\BotController;
use App\Http\Controllers\Api\BotUserController;
use App\Http\Controllers\Api\LastController;
use App\Http\Controllers\Api\TelegramBotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/bot-send', [BotController::class, 'bot']);
Route::post('/send-message',[TelegramBotController::class,'index']);
Route::post('/check-bot',[BotUserController::class,'index']);
Route::post('last-bot',[LastController::class,'index']);