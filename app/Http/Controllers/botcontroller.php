<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Werk365\IdentityDocuments\IdentityDocument;

class botcontroller extends Controller
{
    public function botControl()
    {
    }

    public function testBOT()
    {
            $data2 = [
                'file_id' => 'AgACAgIAAxkBAAIKYWO1whJQMdVZ7wiyhEUtevWK3a-sAAK-xTEbEdWpSTPpKUJTq0c0AQADAgADeAADLQQ'
            ];
            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/getFile?" . http_build_query($data2));
            $responseupdate = json_decode($response);
            if (isset($responseupdate->result->file_path)) {
                $url = "https://api.telegram.org/file/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/" . $responseupdate->result->file_path;
                $file = Http::get($url);
                $filename = $responseupdate->result->file_path;
                $filename = explode('/', $filename);
                $filename = explode('.', $filename[1]);
                $hash = Hash::make($filename[0]);
                $hash = str_replace('/', '', $hash);
                $hash = str_replace('$', '', $hash);
                $hash = str_replace('.', '', $hash);
                $filename = date('YmdHi') . $hash . '.' . $filename[1];
                Storage::disk('public')->put($filename, $file);
                $document = new IdentityDocument(public_path('images/').$filename);
                $parsed = $document->getParsedMrz();
                $viz = $document->getViz();
                echo $parsed;
                echo $viz;
            }
    }

    public function botResponse()
    {
        $result = file_get_contents('php://input');
        $update = json_decode($result);

        // message ->
        if (isset($update->message->text)) {
            $user = DB::table('users')->where('userid', '=', $update->message->from->id)->get();
            $parceluser = DB::table('parcels')->where('userid', '=', $update->message->from->id)->get();
            $keyboard2 =
                '{
                             "inline_keyboard": [[
                                            {
                                                "text": "Пройти",
                                                "callback_data": "13"
                                            }]
                                        ]
                                    }';
            $decode2 = json_decode($keyboard2);
            if ($parceluser == '[]') {
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
            } else {
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
                    },
                    {
                        "text": "Мои обьявления",
                        "callback_data": "11"
                    }]
                ]
            }';
            }

            $decode = json_decode($keyboard);
            if ($update->message->text == '/start') {
                if ($user == '[]') {
                    if (isset($update->message->from->last_name)) {
                        $userdata = array(
                            'userid' => $update->message->from->id,
                            'chatid' => $update->message->chat->id,
                            'first_name' => $update->message->from->first_name,
                            'last_name' => $update->message->from->last_name,
                            'username' => '@' . $update->message->from->username,
                            'language_code' => $update->message->from->language_code,
                            'status' => 'started',
                            'isstart' => 'true',
                            'passport' => 'false',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                    } else {
                        $userdata = array(
                            'userid' => $update->message->from->id,
                            'chatid' => $update->message->chat->id,
                            'first_name' => $update->message->from->first_name,
                            'last_name' => ' ',
                            'username' => '@' . $update->message->from->username,
                            'language_code' => $update->message->from->language_code,
                            'status' => 'started',
                            'isstart' => 'true',
                            'passport' => 'false',
                            'created_at' => date('Y-m-d H:i:s'),
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
                        'text' => 'Вам нужно пройти паспортную аутентификацию для того чтобы пользоваться нашим сервисом',
                        'reply_markup' => json_encode($decode2)
                    ];
                    $userdata = array(
                        'status' => 'started',
                        "updated_at" => date('Y-m-d H:i:s')
                    );
                    DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                } else {
                    foreach ($user as $userItem) {
                        if ($userItem->passport == 'false') {
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
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data2 = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Вам нужно пройти паспортную аутентификацию для того чтобы пользоваться нашим сервисом',
                                'reply_markup' => json_encode($decode2)
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                        } else if ($userItem->passport != 'false') {
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
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data2 = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Что вы хотите сделать?',
                                'reply_markup' => json_encode($decode)
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                        }
                    }
                }
            } else {
                foreach ($user as $userItem) {
                    if ($userItem->status == 'firstclaimcity') {
                        $userdata = array(
                            'status' => 'secondclaimcity',
                            'firstcity' => Str::ucfirst($update->message->text),
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Город прибытия',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    } else if ($userItem->status == 'firstsendcity') {
                        $userdata = array(
                            'status' => 'secondsendcity',
                            'firstcity' => Str::ucfirst($update->message->text),
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Город прибытия',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    } else if ($userItem->status == 'secondclaimcity') {
                        $userdata = array(
                            'status' => 'both',
                            'secondcity' => Str::ucfirst($update->message->text),
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        // ПОМЕНЯТЬ НА НОРМАЛЬНЫЙ СПИСОК----
                        $parcels = DB::table('parcels')->where('type', '=', 'claim')->where('firstcity', '=', $userItem->firstcity)->where('secondcity', '=', Str::ucfirst($update->message->text))->get();
                        if ($parcels == '[]') {
                            $data2 = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Таких обьявлений нету' . PHP_EOL . 'Если хотите сделать что-то еще, то напишите /start',
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                        } else {
                            foreach ($parcels as $parcel) {
                                $data = [
                                    'chat_id' => $update->message->chat->id,
                                    'text' => 'Откуда: ' . $parcel->firstcity . PHP_EOL . 'Куда: ' . $parcel->secondcity . PHP_EOL . 'Дата: ' . $parcel->date . PHP_EOL . 'Вес: ' . $parcel->weight . PHP_EOL . 'Что: ' . $parcel->item . PHP_EOL . 'Номер: ' . $parcel->phone . PHP_EOL . '' . $parcel->username,
                                ];
                                $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                            }
                            $data2 = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Если хотите сделать что-то еще, то напишите /start',
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                        }
                        // ----
                    } else if ($userItem->status == 'secondsendcity') {
                        $userdata = array(
                            'status' => 'both',
                            'secondcity' => Str::ucfirst($update->message->text),
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        // ПОМЕНЯТЬ НА НОРМАЛЬНЫЙ СПИСОК----
                        $parcels = DB::table('parcels')->where('type', '=', 'send')->where('firstcity', '=', $userItem->firstcity)->where('secondcity', '=', Str::ucfirst($update->message->text))->get();
                        if ($parcels == '[]') {
                            $data2 = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Таких обьявлений нету' . PHP_EOL . 'Если хотите сделать что-то еще, то напишите /start',
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                        } else {
                            foreach ($parcels as $parcel) {
                                $data = [
                                    'chat_id' => $update->message->chat->id,
                                    'text' => 'Откуда: ' . $parcel->firstcity . PHP_EOL . 'Куда: ' . $parcel->secondcity . PHP_EOL . 'Вес: ' . $parcel->weight . PHP_EOL . 'Что: ' . $parcel->item . PHP_EOL . 'Цена: ' . $parcel->price . PHP_EOL . 'Номер: ' . $parcel->phone . PHP_EOL . '' . $parcel->username,
                                ];
                                $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                            }
                            $data2 = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Если хотите сделать что-то еще, то напишите /start',
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                        }
                        // ----
                    } else if ($userItem->status == 'firstadvertclaimcity') {
                        $userdata = array(
                            'status' => 'secondadvertclaimcity',
                            'firstcity' => Str::ucfirst($update->message->text),
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Куда вы хотите взять посылку?',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    } else if ($userItem->status == 'secondadvertclaimcity') {
                        $userdata = array(
                            'status' => 'dateadvertclaim',
                            'secondcity' => Str::ucfirst($update->message->text),
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Когда вы едете?' . PHP_EOL . 'Формат: дд.мм.гггг',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    } else if ($userItem->status == 'dateadvertclaim') {
                        $input = [
                            'date' => $update->message->text
                        ];
                        $validator = Validator::make($input, [
                            'date' => 'date_format:d.m.Y|after:today'
                        ]);
                        if ($validator->fails()) {
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Дата должна быть по формату, либо не позднее сегодняшнего дня!' . PHP_EOL . 'Формат: дд.мм.гггг',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        } else {
                            $userdata = array(
                                'status' => 'weightadvertclaim',
                                'date' => $update->message->text,
                                "updated_at" => date('Y-m-d H:i:s')
                            );
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Посылку с каким весом вы можете взять с собой?' . PHP_EOL . 'Введите число в кг, а если только документы, то введите 0',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        }
                    } else if ($userItem->status == 'weightadvertclaim') {
                        $input = [
                            'weight' => $update->message->text
                        ];
                        $validator = Validator::make($input, [
                            'weight' => 'starts_with:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50|ends_with:кг'
                        ]);
                        if ($update->message->text == 0 or $update->message->text == '0 кг') {
                            $userdata = array(
                                'status' => 'itemadvertclaim',
                                'weight' => 'Документы',
                                "updated_at" => date('Y-m-d H:i:s')
                            );
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Что можете взять с собой?' . PHP_EOL . 'Пример: документы, мелкие посылки, багаж',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        } else if ($validator->fails()) {
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Введите вес в кг!' . PHP_EOL . 'Формат: вес кг',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        } else {
                            $userdata = array(
                                'status' => 'itemadvertclaim',
                                'weight' => $update->message->text,
                                "updated_at" => date('Y-m-d H:i:s')
                            );
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Что можете взять с собой?' . PHP_EOL . 'Пример: документы, мелкие посылки, багаж',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        }
                    } else if ($userItem->status == 'itemadvertclaim') {
                        $userdata = array(
                            'status' => 'phoneadvertclaim',
                            'item' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Напишите ваш контактный телефон' . PHP_EOL . 'Пример: +79249683023',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    } else if ($userItem->status == 'phoneadvertclaim') {
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
                        $input = [
                            'phone' => $update->message->text
                        ];
                        $validator = Validator::make($input, [
                            'phone' => 'starts_with:+'
                        ]);
                        if ($validator->fails()) {
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Введите телефон по формату!' . PHP_EOL . 'Формат: +79249683023',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        } else {
                            $userdata = array(
                                'status' => 'advertclaimall',
                                'phone' => $update->message->text,
                                "updated_at" => date('Y-m-d H:i:s')
                            );
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Вы хотите взять с собой посылку ' . PHP_EOL . 'Откуда: ' . $userItem->firstcity . PHP_EOL . 'Куда: ' . $userItem->secondcity . PHP_EOL . 'Дата: ' . $userItem->date . PHP_EOL . 'Вес: ' . $userItem->weight . PHP_EOL . 'Что: ' . $userItem->item . PHP_EOL . 'Номер: ' . $update->message->text . PHP_EOL . '' . $userItem->username,
                                'reply_to_message_id' => $update->message->message_id,
                                'reply_markup' => json_encode($decode)
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        }
                    } else if ($userItem->status == 'firstadvertsendcity') {
                        $userdata = array(
                            'status' => 'secondadvertsendcity',
                            'firstcity' => Str::ucfirst($update->message->text),
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Куда вы хотите отправить посылку?',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    } else if ($userItem->status == 'secondadvertsendcity') {
                        $userdata = array(
                            'status' => 'weightadvertsend',
                            'secondcity' => Str::ucfirst($update->message->text),
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Какой вес вашей посылки?' . PHP_EOL . 'Введите число в кг, а если у вас документ, то введите 0',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    } else if ($userItem->status == 'weightadvertsend') {
                        $input = [
                            'weight' => $update->message->text
                        ];
                        $validator = Validator::make($input, [
                            'weight' => 'starts_with:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50|ends_with:кг'
                        ]);
                        if ($update->message->text == 0 or $update->message->text == '0 кг') {
                            $userdata = array(
                                'status' => 'descriptionadvertsend',
                                'weight' => 'Документы',
                                "updated_at" => date('Y-m-d H:i:s')
                            );
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Напишите описание вашей посылки.' . PHP_EOL . 'Пример: телефон/ пакет с одеждой/ багаж',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        } else if ($validator->fails()) {
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Введите вес в кг!' . PHP_EOL . 'Формат: вес кг',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        } else {
                            $userdata = array(
                                'status' => 'descriptionadvertsend',
                                'weight' => $update->message->text,
                                "updated_at" => date('Y-m-d H:i:s')
                            );
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Напишите описание вашей посылки.' . PHP_EOL . 'Пример: телефон/ пакет с одеждой/ багаж',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        }
                    } else if ($userItem->status == 'descriptionadvertsend') {
                        $userdata = array(
                            'status' => 'priceadvertsend',
                            'item' => $update->message->text,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Укажите цену' . PHP_EOL . 'Пример: 1000 рублей',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    } else if ($userItem->status == 'priceadvertsend') {
                        $input = [
                            'price' => $update->message->text
                        ];
                        $validator = Validator::make($input, [
                            'price' => 'starts_with:1,2,3,4,5,6,7,8,9|ends_with:рублей,руб'
                        ]);
                        if ($validator->fails()) {
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Укажите цену по формату' . PHP_EOL . 'Формат: 1000 рублей',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        } else {
                            $userdata = array(
                                'status' => 'phoneadvertsend',
                                'price' => $update->message->text,
                                "updated_at" => date('Y-m-d H:i:s')
                            );
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Напишите ваш контактный телефон' . PHP_EOL . 'Пример: +79249683023',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        }
                    } else if ($userItem->status == 'phoneadvertsend') {
                        $keyboard =
                            '{
                                "inline_keyboard": [[
                                    {
                                        "text": "Да",
                                        "callback_data": "9"
                                    },
                                    {
                                        "text": "Нет",
                                        "callback_data": "10"
                                    }]
                                ]
                            }';
                        $decode = json_decode($keyboard);
                        $input = [
                            'phone' => $update->message->text
                        ];
                        $validator = Validator::make($input, [
                            'phone' => 'starts_with:+'
                        ]);
                        if ($validator->fails()) {
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Введите телефон по формату!' . PHP_EOL . 'Формат: +79249683023',
                                'reply_to_message_id' => $update->message->message_id,
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        } else {
                            $userdata = array(
                                'status' => 'advertsendall',
                                'phone' => $update->message->text,
                                "updated_at" => date('Y-m-d H:i:s')
                            );
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'text' => 'Вы хотите отправить посылку' . PHP_EOL . 'Откуда: ' . $userItem->firstcity . PHP_EOL . 'Куда: ' . $userItem->secondcity . PHP_EOL . 'Вес: ' . $userItem->weight . PHP_EOL . 'Что: ' . $userItem->item . PHP_EOL . 'Цена: ' . $userItem->price . PHP_EOL . 'Номер: ' . $update->message->text . PHP_EOL . '' . $userItem->username,
                                'reply_to_message_id' => $update->message->message_id,
                                'reply_markup' => json_encode($decode)
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        }
                    }else if($userItem->status == 'passportsend'){
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Отправьте фотографию вашего паспорта!',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    } else if ($userItem->status == 'selfiesend') {
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Отправьте фотографию селфи вместе с вашим паспортом!',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }else {
                        $data = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Если хотите сделать что-то еще, то напишите /start',
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                    }
                }
            }
        }else if(isset($update->message->photo)){
            $user = DB::table('users')->where('userid', '=', $update->message->from->id)->get();
            foreach ($user as $userItem) {
                if ($userItem->status == 'passportsend') {
                        $data2 = [
                            'file_id' => $update->message->photo[0]->file_id
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/getFile?" . http_build_query($data2));
                        $responseupdate = json_decode($response);
                        if (isset($responseupdate->result->file_path)) {
                            $url = "https://api.telegram.org/file/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/" . $responseupdate->result->file_path;
                            $file = Http::get($url);
                            $filename = $responseupdate->result->file_path;
                            $filename = explode('/', $filename);
                            $filename = explode('.', $filename[1]);
                            $hash = Hash::make($filename[0]);
                            $hash = str_replace('/', '', $hash);
                            $filename = date('YmdHi') . $hash . '.' . $filename[1];
                            Storage::disk('public')->put($filename, $file);
                            $userdata = array(
                                'status' => 'selfiesend',
                                'firstpassport' => $filename,
                                "updated_at" => date('Y-m-d H:i:s')
                            );
                            DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                            $data = [
                                'chat_id' => $update->message->chat->id,
                                'reply_to_message_id' => $update->message->message_id,
                                'text' => 'Отправьте ваше селфи с 2 и 3 страницы вашего паспорта' . PHP_EOL . '(Кем выдано/сведения о личности владельца паспорта)',
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data));
                        }
                }else if($userItem->status == 'selfiesend'){
                    $data2 = [
                        'file_id' => $update->message->photo[0]->file_id
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/getFile?" . http_build_query($data2));
                    $responseupdate = json_decode($response);
                    if (isset($responseupdate->result->file_path)) {
                        $url = "https://api.telegram.org/file/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/" . $responseupdate->result->file_path;
                        $file = Http::get($url);
                        $filename = $responseupdate->result->file_path;
                        $filename = explode('/', $filename);
                        $filename = explode('.', $filename[1]);
                        $hash = Hash::make($filename[0]);
                        $hash = str_replace('/', '', $hash);
                        $filename = date('YmdHi') . $hash . '.' . $filename[1];
                        Storage::disk('public')->put($filename, $file);
                        $userdata = array(
                            'status' => 'started',
                            'passport' => 'true',
                            'firstselfie' => $filename,
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->message->from->id)->update($userdata);
                        $data2 = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Работая в нашем сервисе вы соглашаетесь о политике конфиденциальности /policy',
                            'reply_to_message_id' => $update->message->message_id,
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                        $data3 = [
                            'chat_id' => $update->message->chat->id,
                            'text' => 'Если хотите сделать что-то еще, то напишите /start',
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data3));
                    }
                }
            }
        }else if (isset($update->callback_query)) {
                $user = DB::table('users')->where('userid', '=', $update->callback_query->from->id)->get();
                $parceluser = DB::table('parcels')->where('userid', '=', $update->callback_query->from->id)->get();
                if ($update->callback_query->data == 2) {
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
                        'message_id' => $update->callback_query->message->message_id,
                        'text' => 'Выберите тип обьявления',
                        'reply_markup' => json_encode($decode)
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                } else if ($update->callback_query->data == 1) {
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
                        'message_id' => $update->callback_query->message->message_id,
                        'text' => 'Выберите тип обьявления',
                        'reply_markup' => json_encode($decode)
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                } else if ($update->callback_query->data == 3) {
                    $userdata = array(
                        'status' => 'firstsendcity',
                        "updated_at" => date('Y-m-d H:i:s')
                    );
                    DB::table('users')->where('userid', '=', $update->callback_query->from->id)->update($userdata);
                    $data2 = [
                        'chat_id' => $update->callback_query->from->id,
                        'message_id' => $update->callback_query->message->message_id,
                        'text' => 'Город отправления',
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                } else if ($update->callback_query->data == 4) {
                    $userdata = array(
                        'status' => 'firstclaimcity',
                        "updated_at" => date('Y-m-d H:i:s')
                    );
                    DB::table('users')->where('userid', '=', $update->callback_query->from->id)->update($userdata);
                    $data2 = [
                        'chat_id' => $update->callback_query->from->id,
                        'message_id' => $update->callback_query->message->message_id,
                        'text' => 'Город отправления',
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                } else if ($update->callback_query->data == 5) {
                    $userdata = array(
                        'status' => 'firstadvertsendcity',
                        "updated_at" => date('Y-m-d H:i:s')
                    );
                    DB::table('users')->where('userid', '=', $update->callback_query->from->id)->update($userdata);
                    $data2 = [
                        'chat_id' => $update->callback_query->from->id,
                        'message_id' => $update->callback_query->message->message_id,
                        'text' => 'Откуда вы хотите отправить посылку?',
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                } else if ($update->callback_query->data == 6) {
                    $userdata = array(
                        'status' => 'firstadvertclaimcity',
                        "updated_at" => date('Y-m-d H:i:s')
                    );
                    DB::table('users')->where('userid', '=', $update->callback_query->from->id)->update($userdata);
                    $data2 = [
                        'chat_id' => $update->callback_query->from->id,
                        'message_id' => $update->callback_query->message->message_id,
                        'text' => 'Откуда вы можете взять посылку?',
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                } else if ($update->callback_query->data == 7) {
                    foreach ($user as $userItem) {
                        $parceldata = array(
                            'userid' => $userItem->userid,
                            'firstcity' => $userItem->firstcity,
                            'secondcity' => $userItem->secondcity,
                            'date' => $userItem->date,
                            'weight' => $userItem->weight,
                            'item' => $userItem->item,
                            'phone' => $userItem->phone,
                            'username' => $userItem->username,
                            'type' => 'claim',
                            'created_at' => date('Y-m-d H:i:s'),
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
                        DB::table('users')->where('userid', '=', $update->callback_query->from->id)->update($userdata);
                        $data2 = [
                            'chat_id' => $update->callback_query->from->id,
                            'message_id' => $update->callback_query->message->message_id,
                            'text' => 'Ваше обьявление было добавлено!' . PHP_EOL . 'Если хотите сделать что-то еще, то напишите /start',
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                    }
                } else if ($update->callback_query->data == 8) {
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
                    DB::table('users')->where('userid', '=', $update->callback_query->from->id)->update($userdata);
                    $data2 = [
                        'chat_id' => $update->callback_query->from->id,
                        'message_id' => $update->callback_query->message->message_id,
                        'text' => 'Откуда вы можете взять посылку?',
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                } else if ($update->callback_query->data == 9) {
                    foreach ($user as $userItem) {
                        $parceldata = array(
                            'userid' => $userItem->userid,
                            'firstcity' => $userItem->firstcity,
                            'secondcity' => $userItem->secondcity,
                            'weight' => $userItem->weight,
                            'item' => $userItem->item,
                            'phone' => $userItem->phone,
                            'price' => $userItem->price,
                            'username' => $userItem->username,
                            'type' => 'send',
                            'created_at' => date('Y-m-d H:i:s'),
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
                            'price' => 'null',
                            "updated_at" => date('Y-m-d H:i:s')
                        );
                        DB::table('users')->where('userid', '=', $update->callback_query->from->id)->update($userdata);
                        $data2 = [
                            'chat_id' => $update->callback_query->from->id,
                            'message_id' => $update->callback_query->message->message_id,
                            'text' => 'Ваше обьявление было добавлено!' . PHP_EOL . 'Если хотите сделать что-то еще, то напишите /start',
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                    }
                } else if ($update->callback_query->data == 10) {
                    $userdata = array(
                        'status' => 'firstadvertsendcity',
                        'firstcity' => 'null',
                        'secondcity' => 'null',
                        'date' => 'null',
                        'weight' => 'null',
                        'item' => 'null',
                        'phone' => 'null',
                        "updated_at" => date('Y-m-d H:i:s')
                    );
                    DB::table('users')->where('userid', '=', $update->callback_query->from->id)->update($userdata);
                    $data2 = [
                        'chat_id' => $update->callback_query->from->id,
                        'message_id' => $update->callback_query->message->message_id,
                        'text' => 'Откуда вы хотите отправить посылку?',
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                } else if ($update->callback_query->data == 11) {
                    $data = [
                        'chat_id' => $update->callback_query->from->id,
                        'message_id' => $update->callback_query->message->message_id,
                        'text' => 'Вот список ваших обьявлений:',
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data));
                    foreach ($parceluser as $parcel) {
                        $keyboard =
                            '{
                            "inline_keyboard": [[
                                {
                                    "text": "Удалить",
                                    "callback_data": "12 ' . $parcel->id . '"
                                }]
                            ]
                        }';
                        $decode = json_decode($keyboard);
                        if ($parcel->type == 'claim') {
                            $data2 = [
                                'chat_id' => $update->callback_query->from->id,
                                'text' => 'Откуда: ' . $parcel->firstcity . PHP_EOL . 'Куда: ' . $parcel->secondcity . PHP_EOL . 'Дата: ' . $parcel->date . PHP_EOL . 'Вес: ' . $parcel->weight . PHP_EOL . 'Что: ' . $parcel->item . PHP_EOL . 'Номер: ' . $parcel->phone . PHP_EOL . '' . $parcel->username,
                                'reply_markup' => json_encode($decode)
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                        } else {
                            $data2 = [
                                'chat_id' => $update->callback_query->from->id,
                                'text' => 'Откуда: ' . $parcel->firstcity . PHP_EOL . 'Куда: ' . $parcel->secondcity . PHP_EOL . 'Вес: ' . $parcel->weight . PHP_EOL . 'Что: ' . $parcel->item . PHP_EOL . 'Номер: ' . $parcel->phone . PHP_EOL . '' . $parcel->username,
                                'reply_markup' => json_encode($decode)
                            ];
                            $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                        }
                    }
                    $data2 = [
                        'chat_id' => $update->callback_query->from->id,
                        'text' => 'Если хотите сделать что-то еще, то напишите /start',
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data2));
                }else if ($update->callback_query->data == 13){
                    $userdata = array(
                        'status' => 'passportsend',
                        "updated_at" => date('Y-m-d H:i:s')
                    );
                    DB::table('users')->where('userid', '=', $update->callback_query->from->id)->update($userdata);
                    $data3 = [
                        'chat_id' => $update->callback_query->from->id,
                        'message_id' => $update->callback_query->message->message_id,
                        'text' => 'Мы храним паспортные данные всех пользователей для безопасности всех заинтересованных лиц',
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data3));
                    $data4 = [
                        'chat_id' => $update->callback_query->from->id,
                        'text' => 'Отправьте фотографию 2 и 3 страницы вашего паспорта '.PHP_EOL.'(Кем выдано/сведения о личности владельца паспорта)'
                    ];
                    $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/sendMessage?" . http_build_query($data4));
                } else {
                    $callback = $update->callback_query->data;
                    $callbackpieces = explode(" ", $callback);
                    if ($callbackpieces[0] == 12) {
                        DB::table('parcels')->where('id', '=', $callbackpieces[1])->delete();
                        $data2 = [
                            'chat_id' => $update->callback_query->from->id,
                            'message_id' => $update->callback_query->message->message_id,
                            'text' => 'Вы удалили это обьявление',
                        ];
                        $response = Http::get("https://api.telegram.org/bot5716304295:AAHVDPCzodAQOwQU5G-7kLfRUU7AVa2VTRg/editMessageText?" . http_build_query($data2));
                    }
                }
            }
        }
    }
