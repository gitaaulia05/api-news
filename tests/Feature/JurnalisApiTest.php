<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\berita;
use App\Models\Administrator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JurnalisApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    protected $token;

    protected function setUp() : void {
        parent::setUp();
        $this->token = '45d14a7f-763e-47b4-b753-fc5d401a3195';
    }


    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    public function testRegister() {
        $this->post('api/jurnalis' , [
            "nama" => "niks",
            "email" => "niks@gmail.com",
            "password" => "tebakzzz"

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
        $this->post('/api/jurnalis/login' , [
            'email' => 'gitaauliahafid@gmail.com',
            'password' => 'tebakzzz'
        ])->assertStatus(200);
    }

    public function testUpdateData(){
        $pengguna = Administrator::where('nama' , 'hihi')->first();

        $this->actingAs($pengguna , 'administrator');

        $admin = Administrator::where('role', 2)->first();

        $this->patch('/api/jurnalis/update/hihi-2'  ,[
            "nama" => "siakk"
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

    public function testAddNews(){
        $this->post('/api/jurnalis/addNews', [
            "judul_berita" => "hym",
            "deks_berita" => "hahaha",
           "gambar" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
           "gambar2" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
           "kategori" => "economics", 
           'keterangan_gambar' => "fmdmdm",
           'keterangan_gambar2' => "fmdmdm"
        ], [
            'Authorization' => $this->token
        ])->assertStatus(201);
    }


    public function testUpdateNews(){
        $berita = berita::first();
        $this->post('/api/jurnalis/updateNews/'. $berita->slug, [
            "judul_berita" => "yaalah",
            "deks_berita" => "huhiha",
           "gambar" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
           "gambar2" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
           "kategori" => "slibaw", 
           'keterangan_gambar' => "hmm ah",
          'keterangan_gambar2' => "hm"
        ], [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }
}

