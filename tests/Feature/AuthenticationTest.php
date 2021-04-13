<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_a_user_can_login()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt($password = 'secret')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token', 'token_type', 'expires_in'
            ]);
    }

    /** @test */
    public function test_a_user_can_register_an_account()
    {
        $this->postJson('/api/auth/register', $this->data())
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token', 'token_type', 'expires_in'
            ]);

        $this->assertCount(1, User::all());
    }

    /** @test */
    public function test_fields_are_required_for_registration()
    {
        collect(['name', 'email', 'password', 'password_confirmation'])->each(function ($field) {
            $response = $this->postJson('/api/auth/register', array_merge($this->data(), [$field => '']));

            $response->assertStatus(422)
                ->assertJsonStructure(['errors']);

            $this->assertCount(0, User::all());
        });
    }

    /** @test */
    public function test_password_field_must_be_at_least_characters()
    {
        $response = $this->postJson('/api/auth/register', array_merge($this->data(), ['password' => 'test']));

        $response->assertStatus(422)
            ->assertJsonStructure(['errors']);

        $this->assertCount(0, User::all());
    }

    /** @test */
    public function test_an_authenticate_user_can_logout()
    {
        $user = factory(User::class)->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'message' => 'Successfully logged out'
            ]);
    }

    /** @test */
    public function test_can_get_authenticated_user_data_from_me_endpoint()
    {
        $user = factory(User::class)->create();

        $token = JWTAuth::fromUser($user);

        $reponse = $this->getJson('/api/auth/me', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $reponse->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    private function data()
    {
        return [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => $password = 'secret',
            'password_confirmation' => $password,
        ];
    }
}
