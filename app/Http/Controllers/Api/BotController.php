<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Random;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    public function store(string $text, int $chat_id)
    {
        $token = "https://api.telegram.org/bot7843257118:AAGRZA6vHjOOU_rUgXlVtggL1K58ZmXCO3k";

        if ($text == '/start') {
            $son = rand(1, 1000);
            Random::truncate();
            Random::create([
                'son' => $son
            ]);
            $response = Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => "Assalom Alaykum! 1-1000 orasida son kiriting..."
            ]);
        } else {
            $check = Random::latest()->first();
            if(!isset($check))
            {
                $response = Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => "Siz hali O'yinga /start buyrug'ini kiritmadingiz"
                ]);
            }
            elseif ($check && $text == $check->son) {
                $response = Http::post($token . '/sendMessage', [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => "Tabriklayman! Siz usha sonni topdingiz."
                ]);

                $check->delete();
            }
             elseif ($text != '/start') {
                if($text>$check->son)
                {
                    $response = Http::post($token . '/sendMessage', [
                        'parse_mode' => 'HTML',
                        'chat_id' => $chat_id,
                        'text' => "Xato son kiritildi, Siz kiritgan son katta"
                    ]);
                }
                else{
                    $response = Http::post($token . '/sendMessage', [
                        'parse_mode' => 'HTML',
                        'chat_id' => $chat_id,
                        'text' => "Xato son kiritildi, Siz kiritgan son kichik"
                    ]);
                }
            }
        }
    }

    public function bot(Request $request)
    {
        try {
            $data = $request->all();
            $chat_id = $data['message']['chat']['id'];
            $text = $data['message']['text'];
            $this->store($text, $chat_id);
            Log::info('Telegram: ', $data);
            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage());
        }
    }
}
