<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Meal;
use App\Models\Worker;
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
        $workers = Worker::where('worker_id', $this->selectedcompany)
            ->get();

        $meals = Meal::all();

        $workersData = [];

        foreach ($workers as $worker) {
            $workerMeals = [];

            foreach ($meals as $meal) {
                $workerMeals[] = [
                    'meal_name' => $meal->name,
                    'price' => $meal->price,
                    'count' => 0
                ];
            }

            $workersData[] = [
                'chat_id' => $worker->chat_id,
                'meals' => $workerMeals
            ];

            $this->sendMealsToWorker($worker->chat_id, $workerMeals);
        }
    }

    public function sendMealsToWorker($chatId, $meals)
    {
        $mealOptions = [];

        // Ovqatlarni tanlov formatida yuborish uchun ularni o'zgartiramiz
        foreach ($meals as $meal) {
            $mealOptions[] = $meal['meal_name'] . ' - ' . $meal['price'] . ' so\'m';
        }

        // Telegram API orqali bot orqali xabar yuborish
        $response = Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => $chatId,
            'text' => "Quyidagi ovqatlarni tanlang va miqdorini kiriting:\n" . implode("\n", $mealOptions)
        ]);

        // Agar kerak bo'lsa, javobni tekshirib ko'rish
        dd($response->json());
    }
}
