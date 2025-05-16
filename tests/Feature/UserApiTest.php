<?php

namespace Tests\Feature;

use Log;
use Tests\TestCase;
use App\Models\berita;
use App\Models\Pengguna;
use App\Models\kategori_berita;
use Illuminate\Http\UploadedFile;
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
      //  e986b551-3d1e-4c83-92e6-c33c007011e4
        // b40cda48-c6ac-473b-813a-74f10b9a996f
       $this->token = 'b40cda48-c6ac-473b-813a-74f10b9a996f'; // Inisialisasi token
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
       // $pengguna = Pengguna::query()->limit(1)->first();
       $pengguna= Pengguna::where('slug', 'hm-2')->first();
            $this->post('api/pengguna/'.$pengguna->slug , [
                "nama" => 'hm', 
              // 'gambar' => new UploadedFile(resource_path('testImg/indomie.jpg'), 'indomie.jpg', null, null, true),
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
       $b =  berita::first();
      //  $this->get('/api/berita/'.$b->kategori_berita->kategori .'/'.$b->slug)->assertStatus(200);
        $this->get('/api/berita/Ekonomi/autem-voluptate-ratione-deserunt-aliquam')->assertStatus(200);
    }

    // public function testCounterNewsToken(){
    //     $this->get('/api/berita/ekonomi/lala' , [
    //         'Authorization' => $this->token
    //     ])->assertStatus(200);
    // }

    public function testKategoriNews(){
        $this->get('/api/berita/pengguna?selectedTopics=true')->assertStatus(200);
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


    public function testSaveNews(){
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->post('/api/pengguna/simpanBerita/lalas-3');
        
        $response->assertStatus(201);
    }

    public function testGetNews(){
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->get('/api/pengguna/simpanBerita');
        $response->assertStatus(200);
    }

    public function testDeleteSaveNews(){
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->delete('/api/pengguna/hapusSimpanBerita/voluptatem-quis-illum-aut-et');
        $response->assertStatus(200);
    }

}
