<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Meal;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;


class MealComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $check = false;
    public $categories;
    public $name;
    public $img;
    public $price;
    public $extension;
    public $filename;
    public $category_id;
    public $ruxsat = false;
    public $editname;
    public $editprice;
    public $editimg;
    public $editcategory_id;
    public $cart = []; 
    public $cartcount;

    public function mount()
    {
        $this->cart = session()->get('cart', []);
        $this->cartcount = collect($this->cart)->sum('quantity');
    }
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules());
    }

    public function rules()
    {
       
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ];
    }

    public function render()
    {
        // $this->categories=Cache::remember('check',60,function():Collection{
        //     return Category::all();
        // });
       $this->categories = Category::all();
        $meals = Meal::orderBy('id', 'desc')->paginate(10);
        // $meals = Cache::remember('meals_page_' . request('page', 1), 60, function () {
        //     return Meal::orderBy('id', 'desc')->paginate(10);
        // });
        return view('livewire.meal-component', compact('meals'));
    }
    public function storeMeal(Request $request)
    {
       
        $this->validate();

        if ($this->img) {
            $this->extension = $this->img->getClientOriginalExtension();
            $this->filename = date("Y-m-d") . '_' . time() . '.' . $this->extension;

            $path = $this->img->storeAs('img_uploaded', $this->filename, 'public');
        }
        Meal::create([
            'name' => $this->name,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'img' => $path,
        ]);

        session()->flash('success', 'Meal successfully created!');
        $this->check = false;
        $this->reset(['name', 'price', 'category_id', 'img']);
    }
    public function result()
    {
        //dd(123);
        if ($this->check == false) {
            $this->check = true;
        } else {
            $this->check = false;
        }
    }
    public function permit(Meal $meal)
    {
        $this->ruxsat = $meal->id;
        $this->editprice = $meal->price;
        $this->editname = $meal->name;
        $this->editcategory_id = $meal->category_id;
        //dd($this->editcategory_id);
    }
    public function update($id)
    {
        $this->validate([
            'editname' => 'required|string|max:255',
            'editcategory_id' => 'required|exists:categories,id',
            'editprice' => 'required|numeric',
            'editimg' => 'nullable|max:10240',
        ]);
        if ($this->editimg) {
            $this->extension = $this->editimg->getClientOriginalExtension();
            $this->filename = date("Y-m-d") . '_' . time() . '.' . $this->extension;
            $path = $this->editimg->storeAs('img_uploaded', $this->filename, 'public');
        } else {
            $path = Meal::findOrFail($id)->img;
        }

        $food = Meal::findOrFail($id);
        $food->update([
            'name' => $this->editname,
            'category_id' => $this->editcategory_id,
            'price' => $this->editprice,
            'img' => $path,
        ]);

        $this->ruxsat = false;
    }
    public function delete(Meal $meal)
    {
        $meal->delete();

    }
    public function addToCart($mealId)
    {
       // dd(session()->get('cart'));
        $meal = Meal::findOrFail($mealId);

        $this->cart[$mealId] = [
            'id' => $meal->id,
            'name' => $meal->name,
            'price' => $meal->price,
            'quantity' => isset($this->cart[$mealId]) ? $this->cart[$mealId]['quantity'] + 1 : 1,
        ];

        session()->put('cart', $this->cart);
        $this->cartcount = collect($this->cart)->sum('quantity');
        session()->flash('success', "{$meal->name} savatga qo'shildi!");
    }
}
