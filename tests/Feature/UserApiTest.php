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
        $this->token = 'f1100330-1714-470c-b8e3-46d6108ef856'; // Inisialisasi token
    }
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRegister(){
        $this->post('api/pengguna' , [
            "nama" => "gita",
            "email" => "ferina@gmail.com",
            "password" => "TestTest1&",
            "password_confirmation" => "TestTest1&"

        ])->assertStatus(201)->assertJson([
            "data" =>[
                "nama" => "gita",
                "email" => "ferina@gmail.com",
            ]
        ]);
    }


    public function testLogin(){
        $this->post('api/pengguna/login' , [
            "email" => "seomoonamoon@gmail.com",
            "password" => "TestTest1&"

        ])->assertStatus(200)->assertJson([
            "data" =>[
                "nama" => "ya",
                "email" => "seomoonamoon@gmail.com",
            ]
        ]);
    }

    public function testCurrent(){
       // $this->seed([PenggunaSeeder::class]);

        $pengguna = Pengguna::first();
       // dd($pengguna->token);
        $this->actingAs($pengguna, 'pengguna')->withHeaders([
            'Authorization' => $pengguna->token,
        ])->get('api/pengguna/saatIni')->assertStatus(200)->assertJson([
            "data" => [
                "nama" => 'Ya'
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
        $tokens = $pengguna->token;

        $this->actingAs($pengguna, 'pengguna')->withHeaders([
            'Authorization' => $this->token,
        ]) ->delete('api/pengguna/logout')->assertStatus(200);
    }

    public function testPassword(){
       // $this->seed([PenggunaSeeder::class]);
        $this->post('api/gantiPassword' , [
            'email' => 'seomoonamoon@gmail.com'
        ])->assertStatus(200);
    }

    public function testPassNoAuth(){
        // $this->patch('/api/lupa-password/4ea0c28c973e4b56868b65b771ae492a' , [
        //     'password' => 'heyBoyyy' ,
        //     'password_confirmation' =>'heyBoyyy' ,
        // ])->assertStatus(200);
        $this->patch('/api/lupa-password/7d3c3ee0a7414e64ba32b8fc514b1f4e' , [
            'password' => 'heyBoyyy' ,
            'password_confirmation' =>'heyBoyyy' ,
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
        $pengguna = Pengguna::where('email' , 'seomoonamoon@gmail.com')->first();
      
        $this->actingAs($pengguna , 'pengguna');
       
        $this->get('/api/auth/token-ganti-password/832b0f526e9c4b8f8d420ff56a147c65' , [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }
}
