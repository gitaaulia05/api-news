<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\berita;
use App\Models\kategori_berita;
use App\Models\Administrator;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

class JurnalisApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    protected $token;

    protected function setUp() : void {
        parent::setUp();
        $this->token = '96f1ff26-f325-4459-9b34-77336f01d119';
    }


    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    public function testRegister() {
        $this->post('api/jurnalis' , [
            "nama" => "niks",
            "email" => "niki@gmail.com",
            "password" => "Cobatebakk1*",
            "password_confirmation" => "Cobatebakk1*",

        ])->assertStatus(201)->assertJson([
            "data" => [
                "nama" => "niks",
                "email" => "niks@gmail.com",
                "role" => 2,
                "active" => 0,
            ]
        ]);
    }

    public function testLogin(){
        // Cobatebakk1* ->gitaauliahafd@gmail.com
        $this->post('/api/jurnalis/login' , [
            'email' => 'niki@gmail.com',
            'password' => 'Cobatebakk1*'
        ])->assertStatus(200);
    }

    public function testUpdateData(){
        $pengguna = Administrator::where('nama' , 'siakk')->first();
         
        $this->actingAs($pengguna , 'administrator');

        $admin = Administrator::where('role', 2)->first();
        //dd($admin);
        $this->post('/api/jurnalis/update/siakk'  ,[
            "nama" => "gitas"
        ], [
            'Authorization' => $this->token
        ])->assertStatus(200)->assertJson([
            "data" => [
                 "nama" => "hihi",
                 "email" => "gigiaull35@gmail.com", 
                 "role" => 1, 
                 "active" => 1
            ]
        ]);
    }



    public function testDetail(){
        $pengguna = Administrator::where('nama' , 'niks')->first();
        $this->actingAs($pengguna , 'pengguna');

        $this->get('/api/jurnalis/niks' , [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }


    public function testCurrent(){
        
        $administrator = Administrator::where('role' , '2')->first();
        
        $this->actingAs($administrator , 'administrator');
           
        $this->get('/api/jurnalis', [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }

    // public function testAddNews(){
    //     $this->post('/api/jurnalis/addNews', [
    //         "judul_berita" => "lalas",
    //         "deks_berita" => "hahaha",
    //        "gambar" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
    //        "gambar2" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
    //        "kategori" => "Teknologi", 
    //        'keterangan_gambar' => "fmdmdm",
    //        'keterangan_gambar2' => "fmdmdm"
    //     ], [
    //         'Authorization' => $this->token
    //     ])->assertStatus(201);
    // }


    public function testAddNews(){
        $faker = Faker::create();
        $categories = kategori_berita::pluck('id_kategori_berita')->toArray();
        $imageFiles = File::files(resource_path('testImg'));
        $kategoriDipilih= [];   

        foreach($categories as $kI) {
            for($i =0; $i<5; $i++) {
                $kategoriDipilih[]= $kI;
            }
        }
        shuffle($kategoriDipilih);

        foreach ($kategoriDipilih as $index => $kategoriId) {

            $randomImage = $imageFiles[array_rand($imageFiles)];  // Memilih gambar secara acak

            $this->post('/api/jurnalis/addNews', [
                "judul_berita" => $faker->sentence,
                "deks_berita" => $faker->paragraph,
                "gambar" => new UploadedFile($randomImage, $randomImage->getFilename(), null, null, true),
                "gambar2" => new UploadedFile($randomImage, $randomImage->getFilename(), null, null, true),
                "id_kategori_berita" => $kategoriId, 
                'keterangan_gambar' => $faker->sentence,
                'keterangan_gambar2' => $faker->sentence
            ], [
                'Authorization' => $this->token
            ])->assertStatus(201);
        }
    }

    public function testUpdateNews(){
        $berita = berita::where('slug' , 'yaalah')->first();

        $this->post('/api/jurnalis/updateNews/'. $berita->slug, [
            "judul_berita" => "yaalah",
            "deks_berita" => "huhiha",
        //    "gambar" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
        //    "gambar2" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
           "kategori" => "Politik", 
           'keterangan_gambar' => "hmm ah",
          'keterangan_gambar2' => "hm"
        ], [
            'Authorization' => $this->token
        ])->assertStatus(200)->assertJson([
            'data' => [
                "judul_berita" => "yaalah",
            "deks_berita" => "huhiha",
           "gambar" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
           "gambar2" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
           "kategori" => "Politik", 
           'keterangan_gambar' => "hmm ah",
          'keterangan_gambar2' => "hm"
            ]
        ]
        );
    }


    public function testSearchNews(){
        dd(date('Y-m-d'));
        $this->get('/api/berita/pengguna?newest='.date('Y-m-d'), [
        ])->dump();

        // $this->get('/api/berita/pengguna?selectedTopics=true&newest=2025-04-06', [
        //     ])->assertStatus(200);

    }

    public function testDeleteNews(){
        $berita = berita::where('slug' , 'dolores-magni-a-voluptate-neque-ipsa-tempora')->first();
    
        $this->get('/api/berita/delete/'.$berita->slug, [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }

    public function testRestoreNews(){
       // $berita = berita::where('slug' , 'hymz')->first();
       $berita = berita::withTrashed()->where('slug' , 'lala')->first();
        //dd($berita);
        $this->get('/api/berita/restore/'.$berita->slug, [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }

    public function testDetailNews() {
        $berita = berita::withTrashed()->where('slug' , 'lala')->first();
        //dd($berita);
        $this->get('/api/jurnalis/berita/'.$berita->slug, [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }

    public function testEmailSend() {
        $this->post('/api/gantiPassword/', [
            'email' => 'gitaauliahafid@gmail.com'
        ])->assertStatus(200);
    
    }

    public function testEmailSendAuth() {
        $pengguna = Administrator::where('nama' , 'gitas')->first();

        $this->actingAs($pengguna , 'administrator');
        $this->post('/api/auth/gantiPasswordPengguna', [
            'email' => 'gitaauliahafid@gmail.com'
        ], 
        [
            'Authorization' => $this->token
        ])->assertStatus(200);
    
    }

    public function testStorePass(){
        $pengguna = Administrator::where('nama' , 'gitas')->first();

        $this->actingAs($pengguna , 'administrator');
        $this->patch('/api/auth/lupa-password-store/bc0f29a7b10c4de5a7ddbc64e822609d', [
            'password' => 'Tebakzzz1@', 
            'password_confirmation' => 'Tebakzzz1@', 

        ], 
        [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }
    
    public function testAuthToken(){
        $pengguna = Administrator::where('nama' , 'gitas')->first();
        //dd($pengguna);
        $this->actingAs($pengguna , 'administrator');
        $this->get('/api/auth/token-ganti-password/c828696d5ef341e8873d0b794710f512', 
        [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }
}

