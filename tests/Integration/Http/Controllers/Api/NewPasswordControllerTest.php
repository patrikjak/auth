<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Integration\Http\Controllers\Api;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Hashing\HashManager;
use Patrikjak\Auth\Tests\Integration\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class NewPasswordControllerTest extends TestCase
{
    private HashManager $hashManager;

    private DatabaseManager $databaseManager;

    /**
     * @param array<string, string> $data
     */
    #[DataProvider('dataProvider')]
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
     * @return iterable<array<string, string>>
     */
    public static function dataProvider(): iterable
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
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->hashManager = $this->app->make(HashManager::class);
        $this->databaseManager = $this->app->make(DatabaseManager::class);
    }
}