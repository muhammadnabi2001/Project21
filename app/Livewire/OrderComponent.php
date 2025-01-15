<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class OrderComponent extends Component
{
    public $users;
    public $cart;
    public $selectedUser;
    public $location;
    public $totalPrice;
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

        $this->reset(['selectedUser', 'location']);
        return redirect()->route('meal');
    }
}
