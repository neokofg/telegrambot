<?php

use Illuminate\Support\Facades\Http;
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
Route::post('/5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/webhook', [\App\Http\Controllers\botcontroller::class , 'botResponse']);
Route::get('/passport', function (){
    return redirect("tg://resolve?domain=telegrampassport
        &bot_id=5716304295
        &scope=%7B%22v%22%3A1%2C%22d%22%3A%5B%7B%22_%22%3A%22pp%22%7D%5D%7D
        &public_key=-----BEGIN%20PUBLIC%20KEY-----%0AMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA05FL3StCKstAZgOh4Bk1%0AQEodBenu%2BBM1jwbYPWi0wyzLwrdglUgP3LnGQJk%2BjOoHaGtNbHJb5ejJZ7ETLkJY%0A%2Fdsmsi52%2Bl2QE6CzosBPsbY1M3MUrVJvDUQZFWAs3BO%2BY%2F2CimNNcGC0HQn1AEYO%0AsoNrZN1GqdIjQlNCfvBoaqm8BvmkKEL3hiZPQfO0TUwPpLaf9ERHzIuYyVpyhroG%0AsZ8jaN14br259ZVuQl9k1qMBX8%2FAqNvthjhI3mSc0vNquBDRUEFReLPO8ai%2FU9sm%0AS8DSg%2Fb50hcP56EA6fY1NK7Yhz4V4yeqeKU%2BvbxxDkhnN1aub10M%2F5Ay94cbJPUc%0AeQIDAQAB%0A-----END%20PUBLIC%20KEY-----
        &nonce=".rand(0,9999999)."
        &payload=nonce");
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
