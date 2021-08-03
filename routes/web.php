<?php

use App\Models\ApiKey;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\ApiKeyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome', ['apikey_object' => ApiKey::whereNotNull('id')->first()]);
});

Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
Route::get('/subscribers/list', [SubscriberController::class, 'list'])->name('subscribers.list');
Route::post('/subscribers', [SubscriberController::class, 'add'])->name('subscribers.add');
Route::put('/subscribers', [SubscriberController::class, 'update'])->name('subscribers.update');
Route::delete('/subscribers', [SubscriberController::class, 'delete'])->name('subscribers.delete');

Route::get('/apikey', [ApiKeyController::class, 'index'])->name('apikey.index');
Route::post('/apikey/add', [ApiKeyController::class, 'add'])->name('apikey.add');
Route::delete('/apikey', [ApiKeyController::class, 'delete'])->name('apikey.delete');
