<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // message ->

        if (isset($update->message)) {
            $user = DB::table('users')->where('userid','=',$update->message->from->id)->get();
            $keyboard =
            '{
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
                if($user == '[]'){
                    if(isset($update->message->from->last_name)){
                        $userdata = array(
                            'userid' => $update->message->from->id,
                            'chatid' => $update->message->chat->id,
                            'first_name' => $update->message->from->first_name,
                            'last_name' => $update->message->from->last_name,
                            'username' => '@'.$update->message->from->username,
                            'language_code' => $update->message->from->language_code,
                            'status' => 'started',
                            'isstart' => 'true',
                            'passport' => 'false',
                            'created_at' =>  date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                    }else{
                        $userdata = array(
                            'userid' => $update->message->from->id,
                            'chatid' => $update->message->chat->id,
                            'first_name' => $update->message->from->first_name,
                            'last_name' => ' ',
                            'username' => '@'.$update->message->from->username,
                            'language_code' => $update->message->from->language_code,
                            'status' => 'started',
                            'isstart' => 'true',
                            'passport' => 'false',
                            'created_at' =>  date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                    }
                    DB::table('users')->insert($userdata);
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
                    $userdata = array(
                        'status' => 'started',
                        "updated_at" => date('Y-m-d H:i:s')
                    );
                    DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                }else{
                    foreach ($user as $userItem){
                        if($userItem->isstart == 'true'){
                            $data2 = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Что вы хотите сделать?',
                                'reply_markup' => json_encode($decode)
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                        }
                    }
                }
            }else{
                foreach($user as $userItem){
                    if($userItem->status == 'firstclaimcity'){
                        $userdata = array(
                            'status' => 'secondclaimcity',
                            'firstcity' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Город прибытия',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }else if($userItem->status == 'firstsendcity'){
                        $userdata = array(
                            'status' => 'secondsendcity',
                            'firstcity' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Город прибытия',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }else if($userItem->status == 'secondclaimcity'){
                        $userdata = array(
                            'status' => 'both',
                            'secondcity' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                        // ПОМЕНЯТЬ НА НОРМАЛЬНЫЙ СПИСОК----
                        $parcels = DB::table('parcels')->where('firstcity', '=', $userItem->firstcity)->where('secondcity', '=', $userItem->secondcity)->get();
                        foreach ($parcels as $parcel){
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Откуда:'. $parcel->firstcity .
                                    '
                                    Куда:'. $parcel->secondcity .
                                    '
                                    Дата:'. $parcel->date .
                                    '
                                    Вес:'. $parcel->weight .
                                    '
                                    Что:'. $parcel->item.
                                    '
                                    Номер:'. $parcel->phone.
                                    '
                                    '. $parcel->username,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        }
                        // ----
                    }else if($userItem->status == 'secondsendcity'){
                        $userdata = array(
                            'status' => 'both',
                            'secondcity' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                        // ПОМЕНЯТЬ НА НОРМАЛЬНЫЙ СПИСОК----
                        $parcels = DB::table('parcels')->where('firstcity', '=', $userItem->firstcity)->where('secondcity', '=', $userItem->secondcity)->get();
                        foreach ($parcels as $parcel){
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Откуда: '. $parcel->firstcity .
                                    '
                                    Куда: '. $parcel->secondcity .
                                    '
                                    Дата: '. $parcel->date .
                                    '
                                    Вес: '. $parcel->weight .
                                    '
                                    Что: '. $parcel->item.
                                    '
                                    Номер: '. $parcel->phone.
                                    '
                                    '. $parcel->username,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        }
                        // ----
                    }else if($userItem->status == 'firstadvertclaimcity'){
                        $userdata = array(
                            'status' => 'secondadvertclaimcity',
                            'firstcity' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Куда вы хотите взять посылку?',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }else if($userItem->status == 'secondadvertclaimcity'){
                        $userdata = array(
                            'status' => 'dateadvertclaim',
                            'secondcity' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Когда вы едете?',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }else if($userItem->status == 'dateadvertclaim'){
                        $userdata = array(
                            'status' => 'weightadvertclaim',
                            'date' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Посылку с каким весом вы можете взять с собой? Введите число в кг, а если только документы, то введите 0',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }else if($userItem->status == 'weightadvertclaim'){
                        $userdata = array(
                            'status' => 'itemadvertclaim',
                            'weight' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Что можете взять с собой? Пример: документы, мелкие посылки, багаж',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }else if($userItem->status == 'itemadvertclaim'){
                        $userdata = array(
                            'status' => 'phoneadvertclaim',
                            'item' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Напишите ваш контактный телефон',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }else if($userItem->status == 'phoneadvertclaim'){
                        $keyboard =
                            '{
                                "inline_keyboard": [[
                                    {
                                        "text": "Да",
                                        "callback_data": "7"
                                    },
                                    {
                                        "text": "Нет",
                                        "callback_data": "8"
                                    }]
                                ]
                            }';
                        $decode = json_decode($keyboard);
                        $userdata = array(
                            'status' => 'advertclaimall',
                            'phone' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid','=',$update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Вы хотите взять с собой посылку
                            Откуда:'. $userItem->firstcity . '
                            Куда:'. $userItem->secondcity . '
                            Дата:'. $userItem->date . '
                            Вес:'. $userItem->weight .'
                            Что:'. $userItem->item.'
                            Номер:'. $update->message->text.'
                            '. $userItem->username,
                            'reply_to_message_id' => $update->message->message_id,
                            'reply_markup' => json_encode($decode)
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }else{
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Я вас не понял',
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }
                }
            }
        }

        // callback ->

        if (isset($update->callback_query)) {
            $user = DB::table('users')->where('userid','=',$update->callback_query->from->id)->get();
            if($update->callback_query->data == 2){
                $keyboard =
                    '{
                    "inline_keyboard": [[
                        {
                            "text": "Отправлю посылку",
                            "callback_data": "5"
                        },
                        {
                            "text": "Возьму посылку",
                            "callback_data": "6"
                        }]
                    ]
                }';
                $decode = json_decode($keyboard);
                $data2 = [
                    'chat_id' => $update->callback_query->from->id,
                    'text' => 'Выберите тип обьявления',
                    'reply_markup' => json_encode($decode)
                ];
                $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
            }else if($update->callback_query->data == 1){
                $keyboard =
                    '{
                    "inline_keyboard": [[
                        {
                            "text": "Отправлю",
                            "callback_data": "3"
                        },
                        {
                            "text": "Возьму",
                            "callback_data": "4"
                        }]
                    ]
                }';
                $decode = json_decode($keyboard);
                $data2 = [
                    'chat_id' => $update->callback_query->from->id,
                    'text' => 'Выберите тип обьявления',
                    'reply_markup' => json_encode($decode)
                ];
                $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
            }else if($update->callback_query->data == 2){

            }else if($update->callback_query->data == 3){
                $userdata = array(
                    'status' => 'firstsendcity',
                    "updated_at" => date('Y-m-d H:i:s')
                );
                DB::table('users')->where('userid','=',$update->callback_query->from->id)->update($userdata);
                $data2 = [
                    'chat_id' => $update->callback_query->from->id,
                    'text' => 'Город отправления',
                ];
                $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
            }else if($update->callback_query->data == 4){
                $userdata = array(
                    'status' => 'firstclaimcity',
                    "updated_at" => date('Y-m-d H:i:s')
                );
                DB::table('users')->where('userid','=',$update->callback_query->from->id)->update($userdata);
                $data2 = [
                    'chat_id' => $update->callback_query->from->id,
                    'text' => 'Город отправления',
                ];
                $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
            }else if($update->callback_query->data == 5){

            }else if($update->callback_query->data == 6){
                $userdata = array(
                    'status' => 'firstadvertclaimcity',
                    "updated_at" => date('Y-m-d H:i:s')
                );
                DB::table('users')->where('userid','=',$update->callback_query->from->id)->update($userdata);
                $data2 = [
                    'chat_id' => $update->callback_query->from->id,
                    'text' => 'Откуда вы можете взять посылку?',
                ];
                $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
            }else if($update->callback_query->data == 7){
                foreach ($user as $userItem){
                    $parceldata = array(
                        'userid' => $userItem->userid,
                        'firstcity' => $userItem->firstcity,
                        'secondcity' => $userItem->secondcity,
                        'date' => $userItem->date,
                        'weight' => $userItem->weight,
                        'item'=> $userItem->item,
                        'phone'=> $userItem->phone,
                        'username' =>$userItem->username,
                        'created_at' =>  date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    DB::table('parcels')->insert($parceldata);
                    $userdata = array(
                        'status' => 'started',
                        'firstcity' => 'null',
                        'secondcity' => 'null',
                        'date' => 'null',
                        'weight' => 'null',
                        'item' => 'null',
                        'phone' => 'null',
                        "updated_at" => date('Y-m-d H:i:s')
                    );
                    DB::table('users')->where('userid','=',$update->callback_query->from->id)->update($userdata);
                    $data2 = [
                        'chat_id' => $update->callback_query->from->id,
                        'text' => 'Ваше обьявление было добавлено!',
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                }
            }else if($update->callback_query->data == 8){
                $userdata = array(
                    'status' => 'firstadvertclaimcity',
                    'firstcity' => 'null',
                    'secondcity' => 'null',
                    'date' => 'null',
                    'weight' => 'null',
                    'item' => 'null',
                    'phone' => 'null',
                    "updated_at" => date('Y-m-d H:i:s')
                );
                DB::table('users')->where('userid','=',$update->callback_query->from->id)->update($userdata);
                $data2 = [
                    'chat_id' => $update->callback_query->from->id,
                    'text' => 'Откуда вы можете взять посылку?',
                ];
                $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
            }
        }
    }
}
