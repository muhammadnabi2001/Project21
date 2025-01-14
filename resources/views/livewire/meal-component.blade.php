<div>
    <div class="container-fluid">
       
        @if($check)
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Create New Meal</h6>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="storeMeal" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="mealName">Meal Name</label>
                                <input type="text" id="mealName" wire:model="name" class="form-control" placeholder="Enter meal name">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="mealPrice">Price</label>
                                <input type="number" id="mealPrice" wire:model="price" class="form-control" placeholder="Enter meal price">
                                @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="mealCategory">Category</label>
                                <select id="mealCategory" wire:model="category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="mb-3">
                                <label class="form-label">Rasm</label>
                                <input type="file" class="form-control" wire:model.blur="img">
                            </div>
                            @error('img')
                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                            @if ($img)
                            <div class="mt-2">
                                <img src="{{ $img->temporaryUrl() }}" alt="Meal Image" class="img-fluid mt-2"
                                    style="max-width: 200px;">
                            </div>
                            @endif
    
                            <button type="submit" class="btn btn-primary">Create Meal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-12">
                <a class="btn btn-primary mt-3" wire:click="result">
                    {{ $check ? 'Back' : 'Create' }}
                </a>
                
            </div>
        </div>
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Meals Table</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Meal Name</th>
                                        <th>Meal Price</th>
                                        <th>Meal Category</th>
                                        <th>Meal Img</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                    @foreach ($meals as $meal)
                                    @if(!$check)
                                        
                                    <tr>
                                        
                                        <td>{{ $meals->perPage() * ($meals->currentPage() - 1) + $loop->iteration }}</td>
                                            <td>{{ $meal->name }}</td>
                                            <td>{{ $meal->price }}</td>
                                            <td>{{ $meal->category->name }}</td>
                                            <td>
                                                <img src="{{ asset('storage/' . $meal->img) }}" alt="Meal Image"
                                                width="100px" height="100px">
                                            </td>
                                            <td>
                                                <button wire:click="permit({{$meal->id}})" class="btn btn-warning">Update</button>
                                                <a wire:click='delete({{$meal->id}})' class="btn btn-danger">Delete</a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($meal->id==$check)
                                            
                                        <tr>
                                            <td>
                                                {{$meal->id}}
                                            </td>
                                            <td><input type="text" wire:model.blur="editname"></td>
                                            <td>
                                                <button wire:click="update({{$meal->id}})" class="btn btn-success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                                  </svg></button>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            {{$meals->links()}}
                        </div>
                    </div>
                </div>   
            </div>
        </div>
    </div>
</div>
