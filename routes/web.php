<?php

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
    return view('welcome');
});
Route::get('/bot', [\App\Http\Controllers\botcontroller::class, 'botControl'])->name('botControl');
Route::post('/5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/webhook', function () {
    $updates = Telegram::getWebhookUpdates();
    return redirect()->route('testBOT');
});
Route::get('/test', [\App\Http\Controllers\botcontroller::class, 'testBOT'])->name('testBOT');
