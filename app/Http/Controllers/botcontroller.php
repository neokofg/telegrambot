<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class botcontroller extends Controller
{
    public function botControl($route = '', $params = [], $method = 'POST'){
        $response = new Client(['base_uri' => 'https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/setwebhook?url=https://mpit14.ru/response']);
        $result = $response->request($method, $route, $params);
        return(string) $result->getBody();
    }
    public function testBOT(){

    }
    public function botResponse(){
        $result = file_get_contents('php://input');
        $update = json_decode($result);
        $keyboard = '{
            "inline_keyboard": [[
                {
                    "text": "Посмотреть обьявления",
                    "callback_data": "1"
                },
                {
                    "text": "Добавить обьявление",
                    "callback_data": "2"
                }]
            ]
        }';
        $decode = json_decode($keyboard);
        if($update->message->text == '/start'){
            $data = [
                'chat_id' => $update->message->chat->id,
                'reply_to_message_id' => $update->message->message_id,
                'text' => 'Все мы попадали в ситуацию, когда срочно нужно отправить документы в другой город. Почтой России очень долго, другими службами дорого, а ещё нужно, чтобы документы прибыли на следующий день. Особенно в нынешнее неспокойное время.

Для этого и был создан канал GETLET https://t.me/getlet, в первую очередь для взаимопомощи между земляками из Республики Саха (Якутия), а также как возможность получить дополнительный заработок'
            ];
            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
            $data2 = [
                'chat_id' => $update->message->chat->id,
                'text' => 'Что вы хотите сделать?',
                'reply_markup' => json_encode($decode)
            ];
            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
        }else if($update->message->text == '/help'){
            $data = [
                'chat_id' => $update->message->chat->id,
                'text' => 'Извините что я вас выебал'
            ];
            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
        }else if($update->message->text == 'K4N') {
            $data = [
                'chat_id' => $update->message->chat->id,
                'text' => 'КАНЧА'
            ];
            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
        }else{
            $data = [
                'chat_id' => $update->message->chat->id,
                'text' => 'Я вас не понял'
            ];
            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
    }
        return true;
    }
}
