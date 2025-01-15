<div>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Zakaz qilish</h4>
            </div>
            <div class="card-body">
                <!-- Foydalanuvchini tanlash -->
                <div class="form-group">
                    <label for="userSelect" class="form-label">Foydalanuvchini tanlang:</label>
                    <select id="userSelect" class="form-control" wire:model="selectedUser">
                        <option value="">Foydalanuvchini tanlang</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    @error('selectedUser') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
    
                <!-- Taomlar ro'yxati -->
                <div class="form-group mt-4">
                    <label for="mealsList" class="form-label">Taomlar:</label>
                    <ul class="list-group">
                        @foreach($cart as $mealId => $meal)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $meal['name'] }} ({{ $meal['price'] }} so'm)
                                </div>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-sm btn-outline-danger mx-1" wire:click="decreaseQuantity({{ $mealId }})">-</button>
                                    <span>{{ $meal['quantity'] }}</span>
                                    <button class="btn btn-sm btn-outline-success mx-1" wire:click="increaseQuantity({{ $mealId }})">+</button>
                                </div>
                            </li>
                        @endforeach
                        <!-- Jami summa -->
                        <li class="list-group-item d-flex justify-content-between align-items-center font-weight-bold">
                            Jami summa:
                            <span>{{ $totalPrice }} so'm</span>
                        </li>
                    </ul>
                </div>
    
                <!-- Manzil kiritish -->
                <div class="form-group mt-4">
                    <label for="locationInput" class="form-label">Manzil:</label>
                    <input type="text" id="locationInput" class="form-control" wire:model="location" placeholder="Manzilni kiriting...">
                    @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
    
                <!-- Tasdiqlash tugmalari -->
                <div class="form-group mt-4">
                    <button class="btn btn-success" wire:click="placeOrder">Zakaz qilish</button>
                    <button class="btn btn-secondary" wire:click="resetForm"  wire:click="resetForm">Bekor qilish</button>
                </div>
            </div>
        </div>
    </div>
    
</div>
