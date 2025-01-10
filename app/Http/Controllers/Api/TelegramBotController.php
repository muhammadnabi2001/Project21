<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Step;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
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
        $token = 'https://api.telegram.org/bot7565145935:AAFexAAA8ScWqYt5vk_VjLCw4GswRfmF4K8';

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

            $step->update(['step' => 'completed', 'password' => bcrypt($text)]);
            $this->createUser($chat_id);

            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'Ro\'yxatdan o\'tish muvaffaqiyatli yakunlandi!'
            ]);

            Step::where('chat_id', $chat_id)->delete();
            return;
        }
    }

    public function index(Request $request)
    {
        Log::info($request->all());
        // try {
        //     $data = $request->all();
        //     $chat_id = $data['message']['chat']['id'];
        //     $text = $data['message']['text'];
        //     $this->store($text, $chat_id);

        //     return response()->json(['status' => 'success'], 200);
        // } catch (Exception $e) {
        //     return response()->json(['status' => 'error: ' . $e->getMessage()], 400);
        // }
    }
}
