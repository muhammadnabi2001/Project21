<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Meal;
use App\Models\Worker;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SendMealComponent extends Component
{
    public $companies;
    public $selectedcompany;
    public function render()
    {
        $this->companies = Company::all();
        return view('livewire.send-meal-component');
    }

    public function send()
    {
        $workers = Worker::where('company_id', $this->selectedcompany)->get();
        $meals = Meal::all();

        $mealList = "";
        $keyboard = [];

        foreach ($meals as $meal) {
            $mealList .= "{$meal->name} - {$meal->price} so'm\n";
            $keyboard[] = [
                ['text' => "{$meal->name}", 'callback_data' => "meal_{$meal->id}"]
            ];
        }

        $keyboard[] = [
            ['text' => 'Cart', 'callback_data' => 'view_cart']
        ];

        $token = "https://api.telegram.org/bot7911495785:AAGOiDZWQUgbW2P1ajFbsCRGbiLW9OWsdsI";

        foreach ($workers as $worker) {
            Http::post($token . '/sendMessage', [
                'parse_mode' => 'HTML',
                'text' => "Taomlar menyusi: \n" . $mealList,
                'chat_id' => $worker->chat_id,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $keyboard
                ])
            ]);
        }
    }
}
