<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Integration\Http\Controllers\Api\RegisterController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Events\RegisteredViaInviteEvent;
use Patrikjak\Auth\Listeners\DeleteRegisterInviteListener;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Tests\Integration\TestCase;
use Patrikjak\Utils\Common\Http\Middlewares\VerifyRecaptcha;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class InvitationStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param array<string, string> $data
     */
    #[DataProvider('dataProvider')]
    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testRegister(array $data, int $status): void
    {
        $this->withoutMiddleware(VerifyRecaptcha::class);
        $response = $this->post(route('api.register.invitation'), $data);

        $response->assertStatus($status);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    #[DefineEnvironment('disableRegisterViaInvitationFeature')]
    public function testRegisterWithDisabledRegister(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->post(route('api.register.invitation'));
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testRegisterWithInvalidRecaptcha(): void
    {
        $response = $this->post(route('api.register.invitation'));

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testRegisterWithExistingEmail(): void
    {
        $this->withoutMiddleware(VerifyRecaptcha::class);
        $email = 'tester@example.com';

        User::factory()->create([
            'email' => $email,
        ]);

        $response = $this->post(route('api.register.invitation'), [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'Password123',
        ]);

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testRegisterWithInviteNotFound(): void
    {
        $this->withoutMiddleware(VerifyRecaptcha::class);

        $response = $this->post(route('api.register.invitation'), [
            'name' => 'John Doe',
            'email' => 'random@email.com',
            'password' => 'Password123',
        ]);

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testRegisterWithInvalidToken(): void
    {
        $this->withoutMiddleware(VerifyRecaptcha::class);
        $this->insertInvite();

        $response = $this->post(route('api.register.invitation'), [
            'name' => 'John Doe',
            'email' => self::TESTER_EMAIL,
            'password' => 'Password123',
            'token' => 'invalid_token',
        ]);

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testSuccessfulEventDispatchedAfterRegister(): void
    {
        Event::fake();

        $response = $this->registerViaInvite();
        $response->assertStatus(200);

        Event::assertDispatched(
            RegisteredViaInviteEvent::class,
            static fn (RegisteredViaInviteEvent $event) => $event->user->email === self::TESTER_EMAIL,
        );

        Event::assertListening(
            RegisteredViaInviteEvent::class,
            DeleteRegisterInviteListener::class,
        );
    }

    #[DefineEnvironment('enableRegisterViaInvitationFeature')]
    public function testTokenIsDeletedAfterRegister(): void
    {
        $response = $this->registerViaInvite();
        $response->assertStatus(200);

        $this->assertDatabaseMissing('register_invites', ['email' => self::TESTER_EMAIL]);
    }

    /**
     * @return array<string, array<string, int|string>>
     */
    public static function dataProvider(): iterable
    {
        yield 'Unsuccessful - empty data' => [
            'data' => [],
            'status' => 422,
        ];

        yield 'Unsuccessful - wrong password - empty' => [
            'data' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => '',
            ],
            'status' => 422,
        ];

        yield 'Unsuccessful - wrong email - invalid' => [
            'data' => [
                'name' => 'John Doe',
                'email' => 'john.doe',
                'password' => 'password',
            ],
            'status' => 422,
        ];
    }

    private function registerViaInvite(): TestResponse
    {
        $token = 'token';

        $this->withoutMiddleware(VerifyRecaptcha::class);
        $this->insertInvite(token: $token);

        return $this->post(route('api.register.invitation'), [
            'name' => 'John Doe',
            'email' => self::TESTER_EMAIL,
            'password' => 'Password123',
            'token' => $token,
        ]);
    }

    private function insertInvite(string $email = self::TESTER_EMAIL, string $token = 'token'): void
    {
        $this->databaseManager->table('register_invites')->insert([
            'email' => $email,
            'token' => $token,
        ]);
    }
}