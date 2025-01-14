<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Meal;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;


class MealComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $check=false;
    public $categories;
    public $name;
    public $img;
    public $price;
    public $extension;
    public $filename;
    public $category_id;
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
        $this->categories=Category::all();
        $meals=Meal::orderBy('id','desc')->paginate(10);
        return view('livewire.meal-component',compact('meals'));
    }
    public function storeMeal(Request $request)
    {
       // dd($request->all());
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
        $this->check=false;
        $this->reset(['name', 'price', 'category_id', 'img']);
    }
    public function result()
    {
        //dd(123);
        if($this->check ==false)
        {
            $this->check=true;
        }else{
            $this->check=false;
        }
    }
}
