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
    public function botResponse(){
        $message = $_POST['message'];
        if($message == '/help'){
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
        }
    }
    public function testBOT(){
        $data = [
            'chat_id' => '864640107',
            'text' => 'Hi there!'
        ];
        $response = file_get_contents("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
    }
}
