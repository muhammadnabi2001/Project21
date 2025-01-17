<div>
    <div class="container-fluid">
        <div class="row">
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif



            <section class="content">
                <div class="container-fluid">
                    <div class="card card-default">
                        <div class="card-body">
                            <div class="col-12 col-sm-6">
                                <!-- Select Companies -->
                                <form wire:submit.prevent="send">
                                    @csrf
                                    <div class="form-group">
                                        <label for="companies">Select Company</label>
                                        <select id="companies" class="form-control" style="width: 100%;" wire:model="selectedcompany">
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                
                                    <!-- Submit Button -->
                                    <div class="mt-3">
                                        <button class="btn btn-primary" type="submit">Send</button>
                                    </div>
                                </form>
                                

                                <!-- Flash Message -->
                                @if (session()->has('message'))
                                    <div class="alert alert-success mt-3">
                                        {{ session('message') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
