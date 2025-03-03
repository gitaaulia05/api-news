<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Administrator;
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
        $this->token = '1a6a3344-6f77-4df1-9c7a-aef79bd3a21c'; // Inisialisasi token
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
    
        $this->post('/api/admin/login' , [
            'email' => 'gigiaull35@gmail.com',
            'password' => 'tebakzzz'
        ])->assertStatus(200);
    }

    public function testUpdateData(){

        $pengguna = Administrator::where('nama' , 'hihi')->first();

        $this->actingAs($pengguna , 'administrator');

        $admin = Administrator::where('role', 1)->first();
        
        $this->patch('/api/admin/update/'. $admin->slug  ,[
            "nama" => "hihi", 
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
        $administrator = Administrator::where('nama' , 'hihi')->first();
        $this->actingAs($administrator , 'administrator');

        $this->get('/api/admin/jurnalis/hihi', [
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
}

