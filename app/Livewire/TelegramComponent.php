<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\WithFileUploads;
use Livewire\Component;

class TelegramComponent extends Component
{
    use WithFileUploads;
    public $text;
    public $users;
    public $img;
    public $vedyo;
    public function render()
    {
        return view('livewire.telegram-component');
    }
    public function send()
    {
        $token = "https://api.telegram.org/bot7843257118:AAGRZA6vHjOOU_rUgXlVtggL1K58ZmXCO3k";

        $data = $this->validate([
            'text' => 'required',
            'img' => 'required|mimes:png,jpg|max:5000',
            'vedyo' => 'required|mimes:mp4,avi,mkv|max:10000',
        ]);

        $staffList = '';
        $users = User::orderBy('id', 'asc')->get();

        foreach ($users as $user) {
            $staffList .= "{$user->id}. {$user->name} - {$user->email}\n";
        }

        $chatIds = ['6611982902', '5759278715','6295425864'];

        $imgFilePath = $this->img->getRealPath();
        $vedyoFilePath = $this->vedyo->getRealPath();

        foreach ($chatIds as $chatId) {
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'chat_id' => $chatId,
                'text' => "<b>{$this->text}</b>\n\nHodimlar ro'yxati:\n{$staffList}",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => 'Tugma 1', 'callback_data' => 'button_1'],
                            ['text' => 'Tugma 2', 'callback_data' => 'button_2'],
                            ['text' => 'Tugma 3', 'callback_data' => 'button_3'],
                        ],
                        [
                            ['text' => 'Tugma 4', 'callback_data' => 'button_4'],
                            ['text' => 'Tugma 5', 'callback_data' => 'button_5'],
                            ['text' => 'Tugma 6', 'callback_data' => 'button_6'],
                        ],
                        [
                            ['text' => 'Tugma 7', 'callback_data' => 'button_7'],
                            ['text' => 'Tugma 8', 'callback_data' => 'button_8'],
                            ['text' => 'Tugma 9', 'callback_data' => 'button_9'],
                        ],
                    ],
                ]),
            ]);

            Http::attach('photo', file_get_contents($imgFilePath), $this->img->getClientOriginalName())
                ->post($token . '/sendPhoto', [
                    'chat_id' => $chatId,
                    'caption' => '',
                ]);

            Http::attach('video', file_get_contents($vedyoFilePath), $this->vedyo->getClientOriginalName())
                ->post($token . '/sendVideo', [
                    'chat_id' => $chatId,
                ]);
        }

        $this->text = '';
        $this->img = null;
        $this->vedyo = null;
        session()->flash('success', 'Message sent successfully!');
    }
}
