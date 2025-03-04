<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Integration\Http\Controllers\Api;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Hashing\HashManager;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Tests\Integration\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class NewPasswordControllerTest extends TestCase
{
    private HashManager $hashManager;

    /**
     * @param array<string, string> $data
     */
    #[DataProvider('resetDataProvider')]
    public function testResetWithInvalidData(array $data): void
    {
        $this->skipRecaptcha();

        $response = $this->patch(route('api.password.store'), $data);

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testReset(): void
    {
        $this->skipRecaptcha();
        $this->createUser();

        $token = 'token';
        $newPassword = 'New-password123';
        $resetsTable = 'password_reset_tokens';

        $this->databaseManager->table('password_reset_tokens')->insert([
            'email' => self::TESTER_EMAIL,
            'token' => bcrypt($token),
            'created_at' => now(),
        ]);

        $response = $this->patch(route('api.password.store'), [
            'token' => $token,
            'email' => self::TESTER_EMAIL,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing($resetsTable, [
            'email' => self::TESTER_EMAIL,
        ]);

        $user = $this->databaseManager->table('users')
            ->where('email', self::TESTER_EMAIL)
            ->first('password');

        $this->assertTrue($this->hashManager->check($newPassword, $user->password));
    }

    /**
     * @param array<string, string> $data
     */
    #[DataProvider('changeDataProvider')]
    public function testChangeWithInvalidData(array $data): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->patch(route('api.change-password'), $data);

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testChange(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $newPassword = 'New-password123';

        $response = $this->patch(route('api.change-password'), [
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'current_password' => self::TESTER_PASSWORD,
        ]);

        $response->assertStatus(200);

        $user = $this->databaseManager->table('users')
            ->where('email', self::TESTER_EMAIL)
            ->first('password');

        $this->assertTrue($this->hashManager->check($newPassword, $user->password));
    }

    #[DefineEnvironment('disableChangePasswordFeature')]
    public function testPasswordWithDisabledFeature(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);
        
        $this->expectException(RouteNotFoundException::class);

        $this->patch(route('api.change-password'), [
            'password' => 'New-password123',
            'password_confirmation' => 'New-password123',
            'current_password' => self::TESTER_PASSWORD,
        ]);
    }

    /**
     * @return iterable<array<string, string>>
     */
    public static function resetDataProvider(): iterable
    {
        yield 'Missing token' => [[
            'token' => 'invalid-token',
        ]];

        yield 'Invalid email' => [[
            'token' => 'token',
            'email' => 'not_valid.email',
        ]];

        yield 'Invalid password' => [[
            'token' => 'token',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]];
    }

    /**
     * @return iterable<array<string, string>>
     */
    public static function changeDataProvider(): iterable
    {
        yield 'Invalid password' => [[
            'password' => 'short',
            'password_confirmation' => 'short',
            'current_password' => self::TESTER_PASSWORD,
        ]];

        yield 'Invalid current password' => [[
            'password' => 'New-password123',
            'password_confirmation' => 'New-password123',
            'current_password' => 'invalid-password',
        ]];
    }

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->hashManager = $this->app->make(HashManager::class);
    }
}