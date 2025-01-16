<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendMessage;
use App\Models\Company;
use App\Models\Remember;
use App\Models\User;
use App\Models\Verification;
use App\Models\Worker;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class DeveloperbotController extends Controller
{
    private function validateName($name)
    {
        if (empty($name)) {
            return 'Ismni kiriting!';
        }

        if (strlen($name) < 3) {
            return 'Ismning uzunligi kamida 3 ta belgi bo\'lishi kerak!';
        }

        return true;
    }

    private function validateEmail($email)
    {
        if (empty($email)) {
            return 'Emailni kiriting!';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Email formatini tekshiring!';
        }
        $check = Worker::where('email', $email)->first();
        if ($check) {
            return 'Ushbu user allaqachon registiratsiyadan utgan';
        }
        return true;
    }

    private function validatePassword($password)
    {
        if (empty($password)) {
            return 'Parolni kiriting!';
        }

        if (strlen($password) < 6) {
            return 'Parol uzunligi kamida 6 ta belgidan iborat bo\'lishi kerak!';
        }

        return true;
    }
    private function latitude($latitude)
    {
        if (empty($latitude)) {
            return 'Latitude maydoni bo\'sh!';
        }

        if (!is_numeric($latitude)) {
            return 'Latitude faqat raqam bo\'lishi kerak!';
        }

        if ($latitude < -90 || $latitude > 90) {
            return 'Latitude qiymati -90 va 90 orasida bo\'lishi kerak!';
        }

        return true;
    }
    private function longitude($longitude)
    {
        if (empty($longitude)) {
            return 'Longitude maydoni bo\'sh!';
        }

        if (!is_numeric($longitude)) {
            return 'Longitude faqat raqam bo\'lishi kerak!';
        }

        if ($longitude < -180 || $longitude > 180) {
            return 'Longitude qiymati -180 va 180 orasida bo\'lishi kerak!';
        }

        return true;
    }

    public function store(string $text, int $chat_id)
    {
        $token = "https://api.telegram.org/bot7911495785:AAGOiDZWQUgbW2P1ajFbsCRGbiLW9OWsdsI";

        if ($text == '/start') {

            $response = Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => "Assalom Alaykum register botimizga xush kelibsiz!!! Iltimos birini tanlang",
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => 'Copmany Holder', 'callback_data' => "login"],
                            ['text' => 'Employee of Company', 'callback_data' => "register"],
                        ],
                    ],
                    'resize_keyboard' => true
                ])
            ]);
        }

        if ($text == 'Copmany Holder') {
            Remember::updateOrCreate(
                ['chat_id' => $chat_id],
                ['step' => 'name']
            );

            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'Iltimos ismingizni kiriting>>>'
            ]);
            return;
        }
        $step = Remember::where('chat_id', $chat_id)->first();
        if ($text == 'Employee of Company') {
            $companies = Company::all();
            $step->update(['step' => 'choose_company']);

            $buttons = [];
            foreach ($companies as $company) {
                $buttons[] = [
                    ['text' => $company->name, 'callback_data' => 'company_' . $company->id]
                ];
            }

            $keyboard = [
                'inline_keyboard' => $buttons
            ];

            $response = Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => "Iltimos, kompaniyani tanlang:",
                'reply_markup' => json_encode($keyboard)
            ]);
        }

        if ($step && $step->step == 'name') {
            $validatedName = $this->validateName($text);
            if ($validatedName !== true) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $validatedName
                ]);
                return;
            }

            $step->update(['step' => 'email', 'name' => $text]);
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'Ismingiz qabul qilindi endi emailingizni kiriting>>>'
            ]);
            return;
        }
        if ($step && $step->step == 'username') {
            $validatedName = $this->validateName($text);
            if ($validatedName !== true) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $validatedName
                ]);
                return;
            }

            $step->update(['step' => 'useremail', 'name' => $text]);
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'Ismingiz qabul qilindi endi emailingizni kiriting>>>'
            ]);
            return;
        }
        if ($step && $step->step == 'email') {
            $validatedEmail = $this->validateEmail($text);
            if ($validatedEmail !== true) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $validatedEmail
                ]);
                return;
            }

            $step->update(['step' => 'password', 'email' => $text]);
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'Emailingiz qabul qilindi. Endi parolingizni kiriting>>>'
            ]);
            return;
        }
        if ($step && $step->step == 'useremail') {
            $validatedEmail = $this->validateEmail($text);
            if ($validatedEmail !== true) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $validatedEmail
                ]);
                return;
            }

            $step->update(['step' => 'userpassword', 'email' => $text]);
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'Emailingiz qabul qilindi. Endi parolingizni kiriting>>>'
            ]);
            return;
        }
        if ($step && $step->step == 'password') {
            $validatedPassword = $this->validatePassword($text);
            if ($validatedPassword !== true) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $validatedPassword
                ]);
                return;
            }

            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'parol qabul qilindi endi rasmingni kiriting>>>'
            ]);
            $step->update(['step' => 'photo', 'password' => bcrypt($text)]);
            return;
        }
        if ($step && $step->step == 'userpassword') {
            $validatedPassword = $this->validatePassword($text);
            if ($validatedPassword !== true) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $validatedPassword
                ]);
                return;
            }

            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'parol qabul qilindi endi rasmingni kiriting>>>'
            ]);
            $step->update(['step' => 'userphoto', 'password' => bcrypt($text)]);
            return;
        }
        if ($step && $step->step == 'companyname') {
            $validatedName = $this->validateName($text);
            if ($validatedName !== true) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $validatedName
                ]);
                return;
            }
            $step->update(['step' => 'latitude', 'companyname' => $text]);
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'Companiyani nomi qabul qilindi endi company uchun latitude>>>'
            ]);
            return;
        }
        if ($step && $step->step == 'latitude') {
            $validate = $this->latitude($text);
            if ($validate !== true) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $validate
                ]);
                return;
            }

            $step->update(['step' => 'longitude', 'latitude' => $text]);
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'Companiya uchun latitude qabul qilindi. Endi companya longitudenikiriting>>>'
            ]);
            return;
        }
        if ($step && $step->step == 'longitude') {
            $valid = $this->longitude($text);
            if ($valid !== true) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $valid
                ]);
                return;
            }

            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'companya uchun longitude qabul qilindi endi company uchun img ni kiriting'
            ]);
            $step->update(['step' => 'companyimg', 'longitude' => $text]);
            return;
        }

        if ($step && $step->step == 'confirmation') {
            $verify = Verification::where('chat_id', $chat_id)->first();
            if ($verify->code == $text) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => 'Password tug\'ri kiritildi adminni javobini kuting!!!'

                ]);
                $step->update(['step' => 'success']);
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => '6611982902',
                    'text' => "User ni tasdiqlaysizmi \n Name: " . $step->name . "\n" . "Email: " . $step->email,
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Tasdiqlash✅', 'callback_data' => "confirm_{$chat_id}"],
                                ['text' => 'Qaytarish❌', 'callback_data' => "cancel_{$chat_id}"],
                            ],
                        ]
                    ])
                ]);
            } else {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => 'tasdiqlash code xato kiritildi'
                ]);
            }
        } elseif ($text != '/start' && $text != '/profile') {
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => $text
            ]);
        }
    }

    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $chat_id = $data['message']['chat']['id'] ?? null;
            $text = $data['message']['text'] ?? null;
            $photo  = $data['message']['photo'] ?? null;
            $call = $data['callback_query'] ?? null;

            if (isset($data['message']['text'])) {
                $text = $data['message']['text'];
                $this->store($text, $chat_id);
            } elseif (isset($photo)) {
                $token = "https://api.telegram.org/bot7911495785:AAGOiDZWQUgbW2P1ajFbsCRGbiLW9OWsdsI";

                Log::info('message', $data['message']['photo']);
                $fileId = end($photo)['file_id'] ?? null;

                if ($fileId) {
                    $response = Http::get("https://api.telegram.org/bot7911495785:AAGOiDZWQUgbW2P1ajFbsCRGbiLW9OWsdsI/getFile?file_id=" . $fileId);
                    if ($response->ok()) {

                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                        $filePath = $response->json()['result']['file_path'];
                        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                        if (!$fileExtension) {
                            Log::error("Fayl kengaytmasi aniqlanmadi: " . $filePath);
                        }

                        if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                            $fileUrl = "https://api.telegram.org/file/bot7911495785:AAGOiDZWQUgbW2P1ajFbsCRGbiLW9OWsdsI/" . $filePath;

                            $fileContents = Http::get($fileUrl)->body();

                            $fileName = 'rasmlar/' . date("Y-m-d") . '_' . time() . '.' . $fileExtension;
                            Storage::disk('public')->put($fileName, $fileContents);
                            $step = Remember::where('chat_id', $chat_id)->first();
                            if ($step->step == 'photo') {
                                $step->update(['step' => 'companyname', 'img' => $fileName]);
                                Http::post($token . '/sendMessage', [
                                    'parse_mode' => 'HTML',
                                    'chat_id' => $chat_id,
                                    'text' => 'Sizning rasmingiz qabul qilindi endi company uchun nameni kiriting>>>'
                                ]);
                                return;
                            }
                            if ($step->step == 'companyimg') {
                                $step->update(['step' => 'confirmation', 'companyimg' => $fileName]);
                                $code = rand(10000, 99999);
                                SendMessage::dispatch($step->email, $code);
                                Verification::where('chat_id', $chat_id)->delete();
                                Verification::create(
                                    [
                                        'chat_id' => $chat_id,
                                        'code' => $code
                                    ]
                                );

                                Http::post($token . '/sendMessage', [
                                    'parse_mode' => 'HTML',
                                    'chat_id' => $chat_id,
                                    'text' => 'Sizning emailingizga tasdiqlash codi junatildi'
                                ]);
                                return;
                            }
                            if ($step->step == 'userphoto') {
                                $step->update(['step' => 'confirmation', 'photo' => $fileName]);
                                $code = rand(10000, 99999);
                                SendMessage::dispatch($step->email, $code);
                                Verification::where('chat_id', $chat_id)->delete();
                                Verification::create(
                                    [
                                        'chat_id' => $chat_id,
                                        'code' => $code
                                    ]
                                );

                                Http::post($token . '/sendMessage', [
                                    'parse_mode' => 'HTML',
                                    'chat_id' => $chat_id,
                                    'text' => 'Sizning emailingizga tasdiqlash codi junatildi'
                                ]);
                                return;
                            }
                        } else {
                            Http::post($token . '/sendMessage', [
                                'parse_mode' => 'HTML',
                                'chat_id' => $chat_id,
                                'text' => 'rasm formati invalid'
                            ]);
                        }
                    }
                }
            }
            if ($call) {
                $token = "https://api.telegram.org/bot7911495785:AAGOiDZWQUgbW2P1ajFbsCRGbiLW9OWsdsI";

                $calldata = $call['data'];
                $call_id = Str::after($calldata, 'confirm_');
                $res = Remember::where('chat_id', $call_id)->first();


                if ($res) {
                    // Http::post($token . '/sendMessage', [
                    //     'parse_mode' => 'HTML',
                    //     'chat_id' => '6611982902',
                    //     'text' => $res->chat_id
                    // ]);

                    $this->store("Sizning profilingiz admin tomonidan tasdiqlandi! Endi tizimdan foydalanishingiz mumkin.", $call_id);
                    $this->store("Foydalanuvchi muvaffaqiyatli tasdiqlandi.", User::where('role', 'admin')->first()->chat_id);
                    $Company = Company::create([
                        'name' => $res->companyname,
                        'img' => $res->companyimg,
                        'latitude' => $res->latitude,
                        'longitude' => $res->longitude
                    ]);
                    Worker::create([
                        'name' => $res->name,
                        'email' => $res->email,
                        'password' => $res->password,
                        'chat_id' => $res->chat_id,
                        'img' => $res->img,
                        'status' => 1,
                        'company_id' => $Company->id,
                        'role' => 'user'
                    ]);
                    return;
                }

                if (Str::startsWith($calldata, 'cancel_')) {
                    $call_id = Str::after($calldata, 'cancel_');
                    $u = Remember::where('chat_id', $call_id)->first();
                    if ($u) {
                        $u->delete();
                    }


                    $this->store("Sizning profilingiz admin tomonidan bekor qilindi.", $call_id);
                    $this->store("Foydalanuvchi muvaffaqiyatli o'chirildi.", User::where('role', 'admin')->first()->chat_id);
                    return;
                }
                if (Str::startsWith($calldata, 'company_')) {
                    $companyId = Str::after($calldata, 'company_');
                    $chatId = $call['message']['chat']['id'];
                    $company = Company::find($companyId);
                    $remeber = Remember::where('chat_id', $chatId)->first();
                    if ($company) {
                        $response = Http::post($token . '/sendMessage', [
                            'parse_mode' => 'HTML',
                            'chat_id' => $chatId,
                            'text' => "Siz \"{$company->name}\" kompaniyasini tanladingiz endi Ismingizni kiriting>>>"
                        ]);
                        $remeber->update(['step' => 'username', 'companyname' => $company->name]);
                    }
                }
            }
            Log::info('Telegram: ', $data);
            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage());
        }
    }
}
