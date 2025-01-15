<div>
    <div class="container-fluid">

        @if ($check)
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
                                    <input type="text" id="mealName" wire:model="name" class="form-control"
                                        placeholder="Enter meal name">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="mealPrice">Price</label>
                                    <input type="number" id="mealPrice" wire:model="price" class="form-control"
                                        placeholder="Enter meal price">
                                    @error('price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="mealCategory">Category</label>
                                    <select id="mealCategory" wire:model="category_id" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
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
        @if (!$check)

        <div class="row">
            <div class="col-12 mt-3">
                <div class="card shadow mb-4">
                    <!-- Cart Icon -->
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Meals Table</h6>
                        <li class="nav-item dropdown no-arrow mx-1" style="list-style-type: none;">
                            <a class="nav-link dropdown-toggle" href='/order' wire:navigate id="cartDropdown" role="button">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="badge badge-danger badge-counter">
                                    {{ $cartcount == 0 ? '-0' : $cartcount }}
                                </span>
                            </a>
                        </li>
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
                                            @if($ruxsat !=$meal->id)
                                                
                                            <tr>

                                                <td>{{ $meals->perPage() * ($meals->currentPage() - 1) + $loop->iteration }}
                                                </td>
                                                <td>{{ $meal->name }}</td>
                                                <td>{{ $meal->price }}</td>
                                                <td>{{ $meal->category->name }}</td>
                                                <td>
                                                    <img src="{{ asset('storage/' . $meal->img) }}" alt="Meal Image"
                                                        width="100px" height="100px">
                                                </td>
                                                <td>
                                                    <button wire:click="permit({{ $meal->id }})"
                                                        class="btn btn-warning">Update</button>
                                                    <a wire:click='delete({{ $meal->id }})'
                                                        class="btn btn-danger">Delete</a>
                                                        <a href="javascript:void(0)" class="btn btn-warning" wire:click="addToCart({{ $meal->id }})">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
                                                                <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l.5 2H5V5zM6 5v2h2V5zm3 0v2h2V5zm3 0v2h1.36l.5-2zm1.11 3H12v2h.61zM11 8H9v2h2zM8 8H6v2h2zM5 8H3.89l.5 2H5zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0"/>
                                                            </svg>
                                                        </a>
                                                        
                                                </td>
                                            </tr>
                                            @endif

                                                @csrf
                                                @if ($meal->id == $ruxsat)
                                                    <tr>
                                                        <td>
                                                            {{ $meal->id }}
                                                        </td>
                                                        <td>
                                                            <input type="text" wire:model.blur="editname" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" wire:model.blur='editprice' class="form-control">
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="mealc">Category</label>
                                                                <select id="mealc" wire:model="editcategory_id" class="form-control">
                                                                    @foreach ($categories as $category)
                                                                        <option value="{{ $category->id }}" 
                                                                            {{ (int) $category->id == (int) $editcategory_id ? 'selected' : '' }}>
                                                                            {{ $category->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                
                                                                @error('editcategory_id')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div style="text-align: center;">
                                                                <!-- File Input for Image -->
                                                                <input type="file" wire:model.blur="editimg" class="form-control" style="margin-top: 10px;">
                                                                <img src="{{ asset('storage/' . $meal->img) }}" alt="Meal Image" width="100px" height="100px">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button type="submit" class="btn btn-success" wire:click='update({{$meal->id}})'>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                                                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0" />
                                                                </svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $meals->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
