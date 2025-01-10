<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary">Create</button>
            </div>
        </div>
        <div class="row">
            <div class="container">
                <h1 class="h3 mb-4 text-gray-800">Product Management</h1>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Add Product</h6>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Chap qism -->
                                <div class="col-md-6">
                                    <!-- Product Name -->
                                    <div class="form-group">
                                        <label for="name">Product Name</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter product name" required>
                                    </div>

                                    <!-- Category -->
                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="" disabled selected>Select category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Attributes -->
                                    <div class="form-group">
                                        <label for="attributes">Attributes</label>
                                        <select class="form-control" id="attributes" name="attributes[]" multiple
                                            size="5">
                                            @foreach ($atrebutes as $attribute)
                                                <option value="{{ $attribute->id }}"
                                                    wire:click="addAttribute({{ $attribute->id }})">
                                                    {{ $attribute->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Hold Ctrl (Windows) or Command (Mac) to
                                            select multiple attributes.</small>
                                    </div>
                                    <!-- Plus Tugmasi -->
                                    
                                    <!-- Characteristics -->
                                    <div class="form-group">
                                        <label for="characters">Characteristics</label>
                                        <select class="form-control" id="characters" name="characters[]" multiple
                                            size="5">
                                            @foreach ($characters as $character)
                                                <option value="{{ $character->id }}">{{ $character->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Hold Ctrl (Windows) or Command (Mac) to
                                            select multiple characteristics.</small>
                                    </div>
                                </div>

                                <!-- O'ng qism -->
                                <div class="col-md-6">
                                    <!-- Title -->
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            placeholder="Enter product title" required>
                                    </div>

                                    <!-- Price -->
                                    <div class="form-group">
                                        <label for="price">Price</label>
                                        <input type="number" step="0.01" class="form-control" id="price"
                                            name="price" placeholder="Enter product price" required>
                                    </div>

                                    <!-- Product Image -->
                                    <button type="button" class="btn btn-success btn-circle"
                                        wire:click="addAttribute(attributeId)" title="Add Attribute">
                                        <i class="fas fa-plus"></i>
                                    </button>

                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Save Product</button>
                                <a href="" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="container mt-4">
                <button id="add-select" class="btn btn-primary">Add Select</button>
            
                <div id="selects-container" class="mt-3">
                    <!-- Yangi selectlar shu yerga qo'shiladi -->
                </div>
            </div>
            
            <script>
                document.getElementById("add-select").addEventListener("click", function() {
                    var container = document.getElementById("selects-container");
            
                    // Yangi select yaratish
                    var newSelect = document.createElement("div");
                    newSelect.classList.add("form-group");
            
                    newSelect.innerHTML = `
                        <label for="newSelect">New Select</label>
                        <select class="form-control" name="newSelect[]">
                            <option value="option1">Option 1</option>
                            <option value="option2">Option 2</option>
                            <option value="option3">Option 3</option>
                        </select>
                    `;
                    
                    container.appendChild(newSelect);  // Yangi selectni qo'shish
                });
            </script>
            

        </div>
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Products Table</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Atrebute Name</th>
                                        <th>Character Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($options as $option)
                                        @if (!$check)
                                            <tr>

                                                <td>{{ $options->perPage() * ($options->currentPage() - 1) + $loop->iteration }}
                                                </td>
                                                <td>{{ $option->element->product->name }}</td>
                                                <td>{{ $option->atrebute_character->atrebute->name }}</td>
                                                <td>{{ $option->atrebute_character->character->name }}</td>
                                                <td>
                                                    <button wire:click="permit({{ $option->id }})"
                                                        class="btn btn-warning">Update</button>
                                                    <a wire:click='delete({{ $option->id }})'
                                                        class="btn btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($option->id == $check)
                                            <tr>
                                                <td>
                                                    {{ $option->id }}
                                                </td>
                                                <td><input type="text" wire:model.blur="editname"></td>
                                                <td>
                                                    <button wire:click="update({{ $option->id }})"
                                                        class="btn btn-success"><svg xmlns="http://www.w3.org/2000/svg"
                                                            width="16" height="16" fill="currentColor"
                                                            class="bi bi-check2" viewBox="0 0 16 16">
                                                            <path
                                                                d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0" />
                                                        </svg></button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $options->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
