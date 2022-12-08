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
    $result = file_get_contents('php://input');
    $update = json_decode($result, true);
    $data = [
        'chat_id' => '864640107',
        'text' => $update->message->text
    ];
    $response = file_get_contents("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
});
Route::get('/test', [\App\Http\Controllers\botcontroller::class, 'testBOT'])->name('testBOT');
/*if($message == '/help'){
    $data = [
        'chat_id' => '864640107',
        'text' => 'Helping you'
    ];
    $response = file_get_contents("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
}else{
    $data = [
        'chat_id' => '864640107',
        'text' => 'dont understand you'
    ];
    $response = file_get_contents("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
}*/
