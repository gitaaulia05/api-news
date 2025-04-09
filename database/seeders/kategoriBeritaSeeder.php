<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\kategori_berita;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class kategoriBeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $kategoriList =  ['Ekonomi', 'Teknologi', 'Politik', 'Hiburan', 'Olahraga'];

       foreach($kategoriList as $kl){
    kategori_berita::create([
            'id_kategori_berita' =>(String) Str::uuid(),
            'kategori' => $kl
        ]);
       }
       
    }
}
