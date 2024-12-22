<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::factory()->create([
            User::FIRST_NAME => 'Kasia',
            User::LAST_NAME => 'Trzeciakiewicz',
            User::EMAIL => 'kasia@mail.com',
            User::PASSWORD =>  Hash::make('haslo'),
        ]);

        User::factory()->create([
            User::FIRST_NAME => 'Random',
            User::LAST_NAME => 'User',
            User::EMAIL => 'random@mail.com',
            User::PASSWORD =>  Hash::make('haslo'),
        ]);

        User::factory(8)->create();
    }
}