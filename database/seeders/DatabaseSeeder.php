<?php

namespace Database\Seeders;

use App\Models\Atrebute;
use App\Models\AtrebuteCharacter;
use App\Models\Category;
use App\Models\Character;
use App\Models\Element;
use App\Models\Option;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password'=>bcrypt('password'),
            'role'=>'admin',
            'chat_id'=>'6611982902',
            'img'=>'img'
        ]);
        $roles = [ 'editor', 'user', 'moderator', 'guest']; 

        for ($i = 1; $i <=10; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->email(),
                'password' => bcrypt('password'), 
                'role' => $roles[array_rand($roles)],
                'chat_id'=>rand(1000000000,9999999999),
                'img'=>'img'.$i
            ]);
        }
        for ($i=1; $i <=30; $i++) { 
            Category::create([
                'name'=>'Category'.$i
            ]);
        }
        for ($i=1; $i <=50; $i++) { 
            Atrebute::create([
                'name'=>'Atrebut'.$i,
                'category_id'=>rand(1,30)
            ]);
        }
        for ($i=1; $i <=100; $i++) { 
            Character::create([
                'name'=>'Character'.$i
            ]);
        }
        for ($i=1; $i <=50; $i++) { 
            AtrebuteCharacter::create([
                'atrebute_id'=>rand(1,50),
                'character_id'=>rand(1,100)
            ]);
        }
        for ($i=1; $i <=20; $i++) { 
            Product::create([
                'category_id'=>rand(1,30),
                'name'=>'Product'.$i,
                'description'=>'Description'.$i,
            ]);
        }
        for ($i=1; $i <=20; $i++) { 
            Element::create([
                'product_id'=>rand(1,20),
                'title'=>'title'.$i,
                'price'=>rand(1000,90000)
            ]);
        }
        for ($i=1; $i <=50 ; $i++) { 
            Option::create([
                'element_id'=>rand(1,20),
                'atrebute_character_id'=>rand(1,50)
            ]);
        }
    }
}
