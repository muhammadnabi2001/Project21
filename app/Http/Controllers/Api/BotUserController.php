<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Random;
use App\Models\Step;
use App\Models\User;
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
    public function store(string $text, int $chat_id, $photo)
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
            $this->createUser($chat_id);


            //Step::where('chat_id', $chat_id)->delete();
            return;
        }
        if ($photo) {
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => 'Rasm keldi'
            ]);
        }
        if ($step && $step->step == 'photo') {
            if (empty($photo)) {
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => 'Rasm yuklanmadi! Iltimos, qayta urinib ko\'ring.'
                ]);
                return;
            }

            $file_info_response = Http::get("https://api.telegram.org/bot{$token}/getFile?file_id={$photo}");
            $file_info = $file_info_response->json(); // JSON formatida javobni olish
            
            if (isset($file_info['ok']) && $file_info['ok']) {
                $file_path = $file_info['result']['file_path'];
                $file_url = "https://api.telegram.org/file/bot{$token}/{$file_path}";
            
                // Rasmni yuklab olish
                $image_response = Http::get($file_url);
                $image_content = $image_response->body();
            
                if ($image_content) {
                    // Fayl kengaytmasini olish
                    $extension = pathinfo($file_path, PATHINFO_EXTENSION);
                    $filename = date("Y-m-d") . '_' . time() . '.' . $extension;
            
                    // Faylni saqlash
                    $path = Storage::disk('public')->put("img_uploaded/{$filename}", $image_content);
            
                    if ($path) {
                        // Step ma'lumotlarini yangilash
                        $step->update(['step' => 'completed', 'photo_path' => "storage/img_uploaded/{$filename}"]);
            
                        Http::post($token . '/sendMessage', [
                            'parse_mode' => 'HTML',
                            'chat_id' => $chat_id,
                            'text' => 'Rasmingiz muvaffaqiyatli saqlandi!'
                        ]);
            
                        // Qadamni yakunlash
                        Step::where('chat_id', $chat_id)->delete();
                        return;
                    } else {
                        // Faylni saqlashda xatolik yuz berdi
                        Http::post($token . '/sendMessage', [
                            'parse_mode' => 'HTML',
                            'chat_id' => $chat_id,
                            'text' => 'Rasmni saqlashda xatolik yuz berdi.'
                        ]);
                    }
                } else {
                    // Rasmni yuklashda xatolik yuz berdi
                    Http::post($token . '/sendMessage', [
                        'parse_mode' => 'HTML',
                        'chat_id' => $chat_id,
                        'text' => 'Rasmni yuklashda xatolik yuz berdi.'
                    ]);
                }
            } else {
                // Telegram'dan rasm fayl ma'lumotlarini olishda xatolik yuz berdi
                Log::error('Telegram getFile failed: ' . $file_info_response->body());
            
                Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => 'Rasmni olishda xatolik yuz berdi.'
                ]);
            }
        }  
    }
    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $chat_id = $data['message']['chat']['id'];
            $text = $data['message']['text'] ?? null;
            $photo = $data['message']['photo'] ?? null;
            Log::info($photo);

            $this->store($text, $chat_id, $photo);
            Log::info('Telegram: ', $data);
            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage());
        }
    }
}
