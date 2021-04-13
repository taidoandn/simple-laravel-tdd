<?php

namespace Tests;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function apiAs($method, $uri, array $data = [], array $headers = [], $user = null)
    {
        $headers = array_merge(
            ['Authorization' => 'Bearer ' . JWTAuth::fromUser($user ?? factory(User::class)->create())],
            $headers
        );

        return $this->json($method, $uri, $data, $headers);
    }
}
