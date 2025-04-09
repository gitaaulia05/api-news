<?php

namespace Tests\Feature;

use Log;
use Tests\TestCase;
use App\Models\kategori_berita;
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
        //f40d2694-246a-485a-a05f-2c83c63485a2
        //86767c18-d7e0-490f-b9b2-8ef3aaa7ae19
       $this->token = 'f40d2694-246a-485a-a05f-2c83c63485a2'; // Inisialisasi token
    }
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRegister(){
        $this->post('api/pengguna' , [
            "nama" => "gita haha",
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
            "email" => "ferina@gmail.com",
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
        $this->patch('/api/auth/lupa-password-store/71ec2727968a4a73a6d41ca691ccbb2f' , [
            'password' => 'TestTest1&' ,
            'password_confirmation' =>'TestTest1&' ,
        ], [
            'Authorization' => $this->token,
        ])->assertStatus(200);
        // $this->patch('/api/lupa-password/7d3c3ee0a7414e64ba32b8fc514b1f4e' , [
        //     'password' => 'heyBoyyy' ,
        //     'password_confirmation' =>'heyBoyyy' ,
        // ])->assertStatus(200);
    }



    public function testMailAuth(){
        //$this->seed([PenggunaSeeder::class]);
        $pengguna = Pengguna::where('email' , 'ggitaauliahafid@gmail.com')->first();
        $this->actingAs($pengguna , 'pengguna');
        $this->post('/api/auth/gantiPasswordPengguna' ,[
           'email' => 'ggitaauliahafid@gmail.com'
        ], [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }

    public function testPassAuth(){
        $pengguna = Pengguna::where('email' , 'ggitaauliahafid@gmail.com')->first();
      
        $this->actingAs($pengguna , 'pengguna');
        $this->get('api/auth/token-ganti-password/1b5120b762794ebca47f34177d2aeb9b' , [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }


    // MAIN FEATURE

    public function testCounterNews(){
        $this->get('/api/berita/Politik/voluptatem-quis-illum-aut-et')->assertStatus(200);
    }

    public function testCounterNewsToken(){
        $this->get('/api/berita/ekonomi/lala' , [
            'Authorization' => $this->token
        ])->assertStatus(200);
    }


    public function testPopularNews(){
        $this->get('/api/berita/populer')->assertStatus(200);
    }

    public function testAllNews(){
         $this->get('/api/berita/pengguna')->assertStatus(200);
      
    }

  //  Route::get('/berita/related/{kategori}' , [NewsController::class , 'relatedNews']);
    public function testRelatedNews(){
        $kategori = kategori_berita::first();
    //    dd( $kategori->kategori);
      $this->get('/api/news/related/Ekonomi', [
        'If-None-Match' => '1a698d69fae0ce3a954caf5b40fab553'
      ])->assertStatus(200);
    
    }
}
