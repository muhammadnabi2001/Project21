<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendMessage;
use App\Models\Step;
use App\Models\User;
use App\Models\Verification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class LastController extends Controller
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
        $check = User::where('email', $email)->first();
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
    private function createUser($chat_id)
    {
        $step = Step::where('chat_id', $chat_id)->first();

        User::create([
            'name' => $step->name,
            'email' => $step->email,
            'password' => $step->password,
            'chat_id' => $chat_id,
            'role' => 'user',
        ]);
    }
    public function store(string $text, int $chat_id)
    {
        $token = "https://api.telegram.org/bot8167278261:AAHYALYcMj1B33jZcm0wOHnVX9mnVk2Slbw";
        if ($text == '/start') {
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => "Iltimos birini tanlang",
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => 'Login', 'callback_data' => "login"],
                            ['text' => 'Register', 'callback_data' => "register"],
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
            Step::updateOrCreate(
                ['chat_id' => $chat_id],
                ['step' => 'begin']
            );
        }
        if ($text == 'Register') {
            Step::updateOrCreate(
                ['chat_id' => $chat_id],
                ['step' => 'name']
            );

            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'Assalom Alaykum! Siz registratsiya botidan foydalanyapsiz. Ismingizni kiriting>>>'
            ]);
            return;
        }
        $step = Step::where('chat_id', $chat_id)->first();
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
                'text' => 'Ismingiz qabul qilindi. Endi emailingizni kiriting>>>'
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
            // $this->createUser($chat_id);


            //Step::where('chat_id', $chat_id)->delete();
            return;
        }
        if ($step && $step->step == 'confirmation') {
            $verify = Verification::where('chat_id', $chat_id)->first();
            if ($verify->code == $text) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => 'Password tug\'g\'ri kiritildi adminni javobini kuting!!!'

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
        } elseif($step->step !='begin') {
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
            Log::info('message', $data);
            $chat_id = $data['message']['chat']['id'] ?? null;
            $text = $data['message']['text'] ?? null;
            $photo  = $data['message']['photo'] ?? null;
            $call = $data['callback_query'] ?? null;

            if (isset($data['message']['text'])) {
                $text = $data['message']['text'];
                $this->store($text, $chat_id);
            } elseif (isset($photo)) {
                $token = "https://api.telegram.org/bot8167278261:AAHYALYcMj1B33jZcm0wOHnVX9mnVk2Slbw";

                Log::info('message', $data['message']['photo']);
                $fileId = end($photo)['file_id'] ?? null;

                if ($fileId) {
                    $response = Http::get("https://api.telegram.org/bot8167278261:AAHYALYcMj1B33jZcm0wOHnVX9mnVk2Slbw/getFile?file_id=" . $fileId);

                    if ($response->ok()) {

                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                        $filePath = $response->json()['result']['file_path'];
                        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                        if (!$fileExtension) {
                            Log::error("Fayl kengaytmasi aniqlanmadi: " . $filePath);
                        }

                        if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                            $fileUrl = "https://api.telegram.org/file/bot8189554946:AAHJDfN16ghjTkjOkG_wcx1z8mHrxz4z6cQ/" . $filePath;
                            $fileContents = Http::get($fileUrl)->body();

                            $fileName = 'telegram_photos/' . date("Y-m-d") . '_' . time() . '.' . $fileExtension;
                            Storage::disk('public')->put($fileName, $fileContents);
                            $step = Step::where('chat_id', $chat_id)->first();
                            if ($step->step == 'photo') {
                                $step->update(['step' => 'confirmation', 'img' => $fileName]);
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
                $token = "https://api.telegram.org/bot8167278261:AAHYALYcMj1B33jZcm0wOHnVX9mnVk2Slbw";

                $calldata = $call['data'];
                $call_id = Str::after($calldata, 'confirm_');
                $res = Step::where('chat_id', $call_id)->first();


                if ($res) {
                    Http::post($token . '/sendMessage', [
                        'parse_mode' => 'HTML',
                        'chat_id' => '6611982902',
                        'text' => $res->chat_id
                    ]);
                    
                    $this->store("Sizning profilingiz admin tomonidan tasdiqlandi! Endi tizimdan foydalanishingiz mumkin.", $call_id);
                    $this->store("Foydalanuvchi muvaffaqiyatli tasdiqlandi.", User::where('role', 'admin')->first()->chat_id);
                    User::create([
                        'name' => $res->name,
                        'email' => $res->email,
                        'password' => $res->password,
                        'chat_id' => $res->chat_id,
                        'img' => $res->img,
                        'status' => 1,
                        'role'=>'user'
                    ]);
                    return;
                }
                if (Str::startsWith($calldata, 'cancel_')) {
                    $call_id = Str::after($calldata, 'cancel_');
                    $u = Step::where('chat_id', $call_id)->first();
                    if ($u) {
                        $u->delete();
                    }


                    $this->store("Sizning profilingiz admin tomonidan bekor qilindi.", $call_id);
                    $this->store("Foydalanuvchi muvaffaqiyatli o'chirildi.", User::where('role', 'admin')->first()->chat_id);
                    return;
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage());
        }
    }
}
