<?php

namespace App\Livewire;

use Livewire\WithFileUploads;
use App\Models\Atrebute;
use App\Models\Category;
use App\Models\Character;
use App\Models\Option;
use Livewire\Component;
use Livewire\WithPagination;

class ProductComponent extends Component
{
    use WithFileUploads;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $check;
    public $categories;
    public $characters;
    public $atrebutes;
    public function render()
    {
        $this->categories=Category::all();
        $this->atrebutes=Atrebute::all();
        $this->characters=Character::all();
        $options=Option::orderBy('id','desc')->paginate(10);
        //dd($options);
        return view('livewire.product-component',compact('options'));
    }
}
