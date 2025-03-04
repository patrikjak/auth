<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Services;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\SocialiteManager;
use Patrikjak\Auth\Models\User as UserModel;
use Patrikjak\Auth\Models\UserFactory;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository;

class SocialAuthService
{
    private const int SOCIAL_DRIVER_SEGMENT_INDEX = 2;

    public function __construct(
        private readonly SocialiteManager $socialiteManager,
        private readonly UserService $userService,
        private readonly UserRepository $userRepository,
        private readonly AuthManager $authManager,
    ) {
    }

    public function getDriverFromRequest(Request $request): string
    {
        return $request->segment(self::SOCIAL_DRIVER_SEGMENT_INDEX);
    }

    public function handleSocialUser(Request $request): void
    {
        $driver = $this->getDriverFromRequest($request);
        $socialiteUser = $this->socialiteManager->driver($driver)->user();

        assert($socialiteUser instanceof User);
        $registeredUser = $this->userRepository->getByEmail($socialiteUser->getEmail());

        if ($registeredUser !== null) {
            $this->login($driver, $socialiteUser, $registeredUser);

            return;
        }

        $this->register($socialiteUser);
    }

    public function login(string $driver, User $socialiteUser, UserModel $registeredUser): void
    {
        $property = sprintf('%s_id', $driver);
        $setterMethodName = sprintf('update%sId', ucfirst($driver));

        if ($registeredUser->$property === null) {
            $this->userRepository->$setterMethodName($registeredUser, $socialiteUser->getId());
        }

        $this->authManager->login($registeredUser);
    }

    public function register(User $socialiteUser): void
    {
        $userModel = UserFactory::getUserModel();
        $userModel->name = $socialiteUser->getName();
        $userModel->email = $socialiteUser->getEmail();
        $userModel->password = sprintf('(%s]#-#[%s)', $socialiteUser->getId(), Str::random());
        $userModel->google_id = $socialiteUser->getId();

        $this->userService->createUserAndLogin($userModel);
    }
}