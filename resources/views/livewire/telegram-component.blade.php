<div>
    <div class="container-fluid">
        <div class="row">
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            

            <div class="col-5">
                <h1>
                    SendMessage
                </h1>
                <form wire:submit.prevent="send" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="text">Message Text:</label>
                        <input type="text" id="text" class="form-control" wire:model="text">
                    </div>
                
                    <div class="form-group mt-3">
                        <label for="rasm">Attach Image (Optional):</label>
                        <input type="file" id="rasm" class="form-control" wire:model="img">
                    </div>
                    <div class="form-group mt-3">
                        <label for="fayl">Attach File (Optional):</label>
                        <input type="file" id="fayl" class="form-control" wire:model="vedyo" accept="image/*,video/*">
                    </div>
                    <button type="submit" class="btn btn-primary m-3">Send</button>
                </form>
                
                
                
                
            </div>
        </div>
    </div>
</div>
