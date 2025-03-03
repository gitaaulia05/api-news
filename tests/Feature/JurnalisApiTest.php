<?php

namespace Tests\Feature;

use Tests\TestCase;
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
        $this->token = 'c1eb0a2c-d239-4c85-828a-45ad14c5dc52';
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
            'email' => 'ferin@gmail.com',
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

}

