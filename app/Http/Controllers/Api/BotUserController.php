<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendMessage;
use App\Models\Random;
use App\Models\Step;
use App\Models\User;
use App\Models\Verification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BotUserController extends Controller
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

        if ($step && $step->step == 'photo') {

            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'sizni emailigizga tasdiqlash code junatildi tasdiqlash kodini kiriting>>>'
            ]);
            $step->update(['step' => 'tasdiqlash', 'img' => 'img']);
            $code = rand(10000, 99999);
            SendMessage::dispatch($step->email, $code);
            Verification::where('chat_id', $chat_id)->delete();
            Verification::create(
                [
                    'chat_id' => $chat_id,
                    'code' => $code
                ]
            );

            return;
        }
        if ($step && $step->step == 'tasdiqlash') {
            $verify = Verification::where('chat_id', $chat_id)->first();
            if ($verify->code == $text) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => 'Password tug\'g\'ri kiritildi adminni javobini kuting!!!'

                ]);
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => '6611982902',
                    'text' => "User ni tasdiqlaysizmi \n Name: " . $step->name . "\n" . "Email: " . $step->email,
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Tasdiqlash✅', 'callback_data' => 'button_1'],
                                ['text' => 'Qaytarish❌', 'callback_data' => 'button_2'],
                            ],
                        ]
                    ])
                ]);
            } else {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $verify->code
                ]);
            }
        }
    }
    public function sengTelegram(Request $request)
    {
        $data = $request->all();

        $photo = end($data['message']['photo']);
        $fileId = $photo['file_id'];

        $response = Http::get("https://api.telegram.org/bot8189554946:AAHJDfN16ghjTkjOkG_wcx1z8mHrxz4z6cQ/getFile?file_id=" . $fileId);

        if ($response->ok()) {
            $filePath = $response->json()['result']['file_path'];
            Log::info('file_path: ', ['file_path' => $filePath]);

            $fileUrl = "https://api.telegram.org/file/bot8189554946:AAHJDfN16ghjTkjOkG_wcx1z8mHrxz4z6cQ/" . $filePath;
            Log::info('fileUrl: ', ['fileUrl' => $fileUrl]);

            $fileContents = Http::get($fileUrl)->body();
            Log::info('fileContents: ', ['fileContents' => $fileContents]);

            $fileName = 'telegram_photos/' . basename($filePath);
            Storage::put($fileName, $fileContents);

            return response()->json(['message' => 'Rasm muvaffaqiyatli yuklandi', 'path' => $fileName]);
        }

        return response()->json(['message' => 'Fayl ma\'lumotini olishda xato yuz berdi.'], 500);
    }

    public function index(Request $request)
{
    $token = "https://api.telegram.org/bot8167278261:AAHYALYcMj1B33jZcm0wOHnVX9mnVk2Slbw";

    $data = $request->all();
    Log::info('Webhook Data:', $data);

    $chat_id = $data['message']['chat']['id'] ?? null;
    $text = $data['message']['text'] ?? null;

    Log::info('Chat ID:', ['chat_id' => $chat_id]);
    Log::info('Text:', ['text' => $text]);

    if ($chat_id && $text === '/start') {
        $response = Http::post($token . '/sendMessage', [
            'parse_mode' => 'HTML',
            'chat_id' => $chat_id,
            'text' => 'Salom',
        ]);

        Log::info('Telegram Response:', $response->json());
    }
}

    
}
