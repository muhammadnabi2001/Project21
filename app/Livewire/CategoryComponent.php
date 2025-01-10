<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;


class CategoryComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $check=false;
    public $editname;
    public $rules=[
        'name'=>'required|min:3|max:255|string'
    ];
    public function render()
    {
        $categories=Category::orderBy('id','desc')->paginate(10);   
        return view('livewire.category-component',compact('categories'));
    }
    public function permit(Category $category)
    {
        
            $this->check=$category->id;
            $this->editname=$category->name;
    }
    public function update(Category $category)
    {
        //dd($category);
        $this->validate([
            'editname'=>'required|min:3|max:255'
        ]);
        $category->update([
            'name'=>$this->editname
        ]);
        $this->check=false;
    }
}
