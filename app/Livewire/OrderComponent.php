<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class OrderComponent extends Component
{
    public $users;
    public $cart;
    public $selectedUser;
    public $longitude;
    public $latitude;
    public $totalPrice;
    public $items;
    public function mount()
    {
        $this->users = User::all();
        $this->cart = session()->get('cart', []);
        $this->calculateTotalPrice();
    }
    public function render()
    {
        $this->users = User::all();
        return view('livewire.order-component');
    }
    public function calculateTotalPrice()
    {
        $this->totalPrice = collect($this->cart)->sum(function ($meal) {
            return $meal['price'] * $meal['quantity'];
        });
    }
    public function increaseQuantity($mealId)
    {
        if (isset($this->cart[$mealId])) {
            $this->cart[$mealId]['quantity']++;
            session()->put('cart', $this->cart);
            $this->calculateTotalPrice();
        }
    }
    public function decreaseQuantity($mealId)
    {
        if (isset($this->cart[$mealId]) && $this->cart[$mealId]['quantity'] > 1) {
            $this->cart[$mealId]['quantity']--;
            session()->put('cart', $this->cart);
            $this->calculateTotalPrice();
        } elseif (isset($this->cart[$mealId]) && $this->cart[$mealId]['quantity'] == 1) {
            unset($this->cart[$mealId]);
            session()->put('cart', $this->cart);
            $this->calculateTotalPrice();
        }
    }
    public function resetForm()
    {
        session()->forget('cart');
        $this->cart = [];
        $this->totalPrice = 0;

        $this->reset(['selectedUser', 'latitude','longitude']);
        return redirect()->route('meal');
    }
    public function placeOrder()
    {
            $this->validate([
                'selectedUser' => 'required|exists:users,id',
                'latitude' => 'required|string|max:255',
                'longitude' => 'required|string|max:255',
            ]);
            // dd($this->totalPrice,$this->location);
            $user = Auth::user();

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'totalprice' => $this->totalPrice,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'payment_status' => 'unpaid',
            ]);
            foreach ($this->cart as $mealId => $meal) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'meal_id' => $mealId,
                    'quantity' => $meal['quantity'],
                ]);
            }
           // dd(123);
            session()->flash('message', 'Zakaz muvaffaqiyatli joylandi!');
            session()->forget('cart');

        $this->sendOrderNotificationToBot($order);
        $this->resetForm();

        return redirect()->route('meal');
    }
    public function sendOrderNotificationToBot($order)
    {
        $user = User::find($this->selectedUser);

        $token = "https://api.telegram.org/bot8167278261:AAHYALYcMj1B33jZcm0wOHnVX9mnVk2Slbw";
        $message = "<b>Yangi zakaz!</b>\n\n";
        $message .= "<b>Ma'sul:</b> {$user->name} ({$user->email})\n";
        
        $message .= "<b>Taomlar:</b>\n";
        foreach ($this->cart as $meal) {
            $message .= "- <i>{$meal['name']}</i> x {$meal['quantity']} (Narxi: {$meal['price']} so'm)\n";
        }
    
        $message .= "<b>Jami summa:</b> {$this->totalPrice} so'm\n";
    
        $message .= "\n<b>Zakazni qabul qilasizmi?</b>";
        Http::post($token . '/sendLocation', [
            'parse_mode' => 'HTML',
            'chat_id' => $user->chat_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);
        Http::post($token . '/sendMessage', [
            'parse_mode' => 'HTML',
            'chat_id' => $user->chat_id,
            'text' => $message,
            'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Tasdiqlash✅', 'callback_data' => "tasdiqlash_{$order->id}"],
                                ['text' => 'Qaytarish❌', 'callback_data' => "qaytarish_{$order->id}"],
                            ],
                        ]
            ])
        ]);
       
    }
}
