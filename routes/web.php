<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/bot/webhook', 'TelegramBotController@handleWebhook');

// Define the route for the /auto command
Route::get('/bot/auto', 'TelegramBotController@enableAutoJoin');

// Define the route for the /noauto command
Route::get('/bot/noauto', 'TelegramBotController@disableAutoJoin');
