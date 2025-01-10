<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Categories Table</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                    @foreach ($categories as $category)
                                    @if(!$check)
                                        
                                    <tr>
                                        
                                        <td>{{ $categories->perPage() * ($categories->currentPage() - 1) + $loop->iteration }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>
                                                <button wire:click="permit({{$category->id}})" class="btn btn-warning">Update</button>
                                                <a wire:click='delete({{$category->id}})' class="btn btn-danger">Delete</a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($category->id==$check)
                                            
                                        <tr>
                                            <td>
                                                {{$category->id}}
                                            </td>
                                            <td><input type="text" wire:model.blur="editname"></td>
                                            <td>
                                                <button wire:click="update({{$category->id}})" class="btn btn-success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                                  </svg></button>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            {{$categories->links()}}
                        </div>
                    </div>
                </div>   
            </div>
        </div>
    </div>
</div>
