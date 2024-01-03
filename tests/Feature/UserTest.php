<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;

class UserTest extends TestCase
{

    // use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_example()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    // public function test_user_login()
    // {
    //     $user = User::factory()->create();

    //     $response = $this->postJson(
    //         '/api/login',
    //         [
    //             'email' => $user->email,
    //             'password' => 'Password'
    //         ]
    //     );

    //     // print_r($response->json());
    //     // die;

    //     $response
    //         ->assertStatus(200)
    //         ->assertJson([
    //             'status' => true,
    //         ]);
    // }

    // public function test_user_login_with_wrong_credentials()
    // {
    //     $user = User::factory()->create();

    //     $response = $this->postJson(
    //         '/api/login',
    //         [
    //             'email' => $user->email,
    //             'password' => 'passwor'
    //         ]
    //     );

    //     $response
    //         ->assertStatus(401)
    //         ->assertJson([
    //             'status' => false,
    //         ]);
    // }

    public function test_education_created()
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $response = $this->postJson('/api/education', [
            "degree" => "Bachelors Degree",
            "course" => "Computer Science",
            "school" => "Lagos State University",
            "from" => "2007",
            "to" => "2012"
        ]);

        $response->assertStatus(200);
    }

    // public function test_user_registration()
    // {

    //     $response = $this->postJson(
    //         '/api/register',
    //         [
    //             'email' => 'testuser@gmail.com',
    //             'confirm_email' => "testuser@gmail.com",
    //             'password' => "password"

    //         ]
    //     );

    //     $response
    //         ->assertStatus(200);
    //     ->assertJson([
    //         'status' => true,
    //     ]);
    // }
}
