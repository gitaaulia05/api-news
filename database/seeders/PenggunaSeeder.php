<?php

namespace Database\Seeders;

use Log;
use App\Models\Pengguna;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Seeder dijalankan');
        Pengguna::create([
            "id_pengguna" => (String) Str::uuid(),
            "nama" => 'gita',
            "email" => 'seomoonamoon@gmail.com',
            'password' => Hash::make('test'),
            'token' => 'test'
            
        ]);
    }
}
