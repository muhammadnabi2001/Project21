<?php

namespace App\Livewire;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;


class CompanyComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $check=false;
    public function render()
    {
        $companies=Company::orderBy('id','desc')->paginate(10);
        return view('livewire.company-component',compact('companies'));
    }
}
