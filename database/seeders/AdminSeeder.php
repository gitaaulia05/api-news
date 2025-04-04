<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\Administrator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Administrator::create([
            "id_administrator" => (String) Str::uuid(),
            "nama" => 'gita',
            "email" => 'seomoonamoon@gmail.com',
            "role" => 1,
            "active" => 1,
            'password' => Hash::make('tebakzzz'),
            'token' => 'test'
            
        ]);
    }
}
