<?php

namespace Tests\Feature\Api;

use App\Enums\GroupTypeEnum;
use App\Http\Controllers\Api\V1\AuthController;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function createUser(string $role = 'user'): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    protected function accessToken(User $user, string $deviceName = 'Test device'): string
    {
        return $user->createToken($deviceName, [AuthController::ABILITY_ACCESS], now()->addDay())->plainTextToken;
    }

    /**
     * Send requests with bearer token. Guards are reset, so each request authenticates by its own token.
     */
    protected function withBearer(string $token): static
    {
        $this->app['auth']->forgetGuards();

        return $this->withToken($token);
    }

    protected function rootGroup(): Group
    {
        return Group::where('type', GroupTypeEnum::Root->value)->firstOrFail();
    }
}
