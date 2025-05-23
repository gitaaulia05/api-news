<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\berita;
use App\Models\kategori_berita;
use App\Models\Pengguna;
use App\Models\Administrator;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */

     public $token;
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    protected function setUp(): void
    {
        parent::setUp();
        $this->token = '8ec220a6-6af6-4aaf-a24d-9177065dd0ce'; // 
        // Inisialisasi token
    }

    public function testUpdateActive(){
        $jurnalis = Administrator::where('role', 2)->first();
      
       //dd($jurnalis->id_administrator);
        $this->post('/api/jurnalis/active/'.$jurnalis->slug ,  [
            'slug' => $jurnalis->slug,
            'active' => "0"
        ], [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }

    public function testLogin(){
       // $this->seed([AdminSeeder::class]);
        $this->post('/api/admin/login' , [
            'email' => 'seomoonamoon@gmail.com',
            'password' => 'Cobatebakk1&'
        ])->assertStatus(200);
    }

     public function testGetToken(){
       // $this->seed([AdminSeeder::class]);
        $this->patch('/api/lupa-password/83921ade7c26434f864e2e5d250fb50d', [
            'password' => 'Cobatebakk1&',
            'password_confirmation' => 'Cobatebakk1&'
        ])->assertStatus(200);
    }

    public function testUpdateData(){

        // $pengguna = Administrator::where('nama' , 'gita')->first();

        // $this->actingAs($pengguna , 'administrator');
        $pengguna = Pengguna::where('email' , 'seomoonamoon@gmail.com')->first();
      
        $this->actingAs($pengguna , 'pengguna');

        $admin = Administrator::where('role', 1)->first();
        
        $this->patch('/api/admin/update/'. $admin->slug  ,[
            "nama" => "kim", 
           // "gambar" => new \Illuminate\Http\UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true)
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

    public function testLogout(){
        
        $admin = Administrator::where('role' , 1)->first();
        $this->actingAs($admin , 'administrator');
        $this->delete('/api/admin/logout',[
            'Authorization' => $this->token
        ])->assertStatus(200);
    }

    public function testSearch(){
        $this->get('/api/admin/jurnalis/search?nama=siakk' , [
            'Authorization' => $this->token
        ])->assertStatus(200)->assertJson([
            "data" => [
                'nama' => 'siakk',
                'email' =>'ferin@gmail.com',
                'gambar' =>'NULL',
                'role' => '2',
                'active' => '1',
            ]
        ]);
    }

    public function testDetail(){
      ///  $this->seed([AdminSeeder::class]);
        $administrator = Administrator::where('nama' , 'hihi')->first();
        $this->actingAs($administrator , 'administrator');

        $this->get('/api/admin/jurnalis/gita', [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }

    public function testDetailJurnalis(){
        
        $administrator = Administrator::where('nama' , 'hihi')->first();
        $this->actingAs($administrator , 'administrator');

        $this->get('/api/admin/jurnalis/niks', [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }

    public function testSearchBerita() {
          $this->get('/api/berita/', [
            'Authorization' => $this->token
        ])->dump();
      
    }
     public function testAddCategory(){
        $this->withHeaders([  'Authorization' => 'Bearer '.$this->token])->post('api/kategoriBerita' , [
            'kategori' => "huhu"
        ])->assertStatus(201);
    }

    public function testShowCategory(){
          $this->withHeaders([  'Authorization' => 'Bearer '.$this->token])->get('api/kategoriBerita?kategori=hiburan')->assertStatus(200);
    }

    public function testUpdateCategory(){
         $this->withHeaders([  'Authorization' => 'Bearer '.$this->token])->patch('api/kategoriBerita/0652d4ea-637b-4b93-b1a1-33a57b79b061' , [
            'kategori' => 'hahah'
         ])->assertStatus(200);
    }

     public function testDeleteCategory(){
        $berita = kategori_berita::where('kategori' , 'huhu')->first();

         $this->withHeaders([  'Authorization' => 'Bearer '.$this->token])->delete('api/kategoriBerita/'.$berita->id_kategori_berita)->assertStatus(200);
    }

     public function testBerita(){
         $this->withHeaders([  'Authorization' => 'Bearer '.$this->token])->get('api/berita')->assertStatus(200);
    }

      public function testCurrent(){
          $this->withHeaders([  'Authorization' => 'Bearer '.$this->token])->get('api/administrator')->dump();
    }
}

