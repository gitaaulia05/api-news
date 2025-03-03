<?php

namespace Tests\Feature;

use Log;
use Tests\TestCase;
use App\Models\Pengguna;
use Database\Seeders\PenggunaSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = '5c37e576-d606-4ac4-8b5d-ca919ff15f1f'; // Inisialisasi token
    }
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRegister(){
        $this->post('api/pengguna' , [
            "nama" => "gita",
            "email" => "gita@gmail.com",
            "password" => "test"

        ])->assertStatus(201)->assertJson([
            "data" =>[
                "nama" => "gita",
                "email" => "gita@gmail.com",
            ]
        ]);
    }


    public function testLogin(){
        $this->post('api/pengguna/login' , [
            "email" => "seomoonamoon@gmail.com",
            "password" => "heyBoyyy"

        ])->assertStatus(200)->assertJson([
            "data" =>[
                "nama" => "ya",
                "email" => "seomoonamoon@gmail.com",
            ]
        ]);
    }

    public function testCurrent(){
        $this->seed([PenggunaSeeder::class]);

        $pengguna = Pengguna::first();

        $this->actingAs($pengguna , 'pengguna');

        $this->get('api/pengguna/saatIni' , [
            'Authorization' => $this->token,
        ])->assertStatus(200)->assertJson([
            "data" =>[
                "nama" => "gita",
                "email" => "gita@gmail.com",
            ]
        ]);
    }

    public function testUpdate(){
       // $this->seed([PenggunaSeeder::class]);
        $pengguna = Pengguna::query()->limit(1)->first();
            $this->patch('api/pengguna/'.$pengguna->slug , [
                "nama" => 'hm'
            ], [
                'Authorization' => $this->token,
            ])->assertStatus(200);
    }

    public function testLogout(){
        $pengguna = pengguna::first();
        $pengguna->save();

        $this->actingAs($pengguna, 'pengguna');

        $this->delete('api/pengguna/logout' , [
            'Authorization' => $pengguna->token,
        ])->assertStatus(200);

    }

    public function testPassword(){
       // $this->seed([PenggunaSeeder::class]);
        $this->post('api/gantiPassword' , [
            'email' => 'seomoonamoon@gmail.com'
        ])->assertStatus(200);
    }

    public function testPassNoAuth(){
        $this->patch('/api/lupa-password/147ef93792824eeb8f5847e62e98c321' , [
            'password' => 'heyBoyyy'
        ])->assertStatus(200);
    }

    public function testMailAuth(){

        //$this->seed([PenggunaSeeder::class]);
        $pengguna = Pengguna::where('email' , 'gita@gmail.com')->first();
        $this->actingAs($pengguna , 'pengguna');
        $this->post('/api/auth/gantiPasswordPengguna' ,[
           'email' => 'seomoonamoon@gmail.com'
        ], [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }

    public function testPassAuth(){
        $pengguna = Pengguna::where('email' , 'gita@gmail.com')->first();
        $this->actingAs($pengguna , 'pengguna');
        $this->get('/api/auth/token-ganti-password/fd22fd207ba14631ae2662fb1dbc271c' , [
            'Authorization' => '5c37e576-d606-4ac4-8b5d-ca919ff15f1f'
        ])->assertStatus(200);
    }
}
