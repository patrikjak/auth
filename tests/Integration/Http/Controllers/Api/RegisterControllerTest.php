<?php

declare(strict_types = 1);

namespace Integration\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Tests\Integration\TestCase;
use Patrikjak\Utils\Common\Http\Middlewares\VerifyRecaptcha;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param array<string, string> $data
     * @dataProvider dataProvider
     */
    public function testRegister(array $data, int $status): void
    {
        $this->withoutMiddleware(VerifyRecaptcha::class);
        $response = $this->post(route('api.register'), $data);

        $response->assertStatus($status);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    #[DefineEnvironment('disableRegisterFeature')]
    public function testRegisterWithDisabledRegister(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->post(route('api.register'));
    }

    public function testRegisterWithInvalidRecaptcha(): void
    {
        $response = $this->post(route('api.register'));

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testRegisterWithExistingEmail(): void
    {
        $this->withoutMiddleware(VerifyRecaptcha::class);
        $email = 'tester@example.com';

        User::factory()->create([
            'email' => $email,
        ]);

        $response = $this->post(route('api.register'), [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'Password123',
        ]);

        $response->assertStatus(422);
        $this->assertMatchesJsonSnapshot($response->getContent());
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

        yield 'Successful' => [
            'data' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => 'Password123',
            ],
            'status' => 200,
        ];
    }
}