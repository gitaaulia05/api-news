<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
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
            "email" => "hmf@gmail.com",
            "password" => "test"

        ])->assertStatus(200)->assertJson([
            "data" =>[
                "nama" => "hmf",
                "email" => "hmf@gmail.com",
            ]
        ]);
    }
}
