# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Package Overview

`patrikjak/auth` is a Laravel Composer package (not a standalone app) providing authentication features: login, register, password reset, social login (Google via Socialite), role-based access control, and register-via-invitation. It depends on `patrikjak/utils`.

Tested via Orchestra Testbench against a MariaDB container.

## Releasing

`public/assets/` is committed to the repo — it is what gets published to consumer apps via `vendor:publish --tag="pjauth-assets"`. **Build assets before tagging a release whenever JS or CSS has changed.**

```bash
npm run build
# or via Docker node service if available:
docker compose run --rm node npm run build
```

Vite outputs to `public/assets/` with stable filenames (`main.js`, `app.css`, locale JS files) — no manifest, no hashing.

## Commands

All commands run via Docker (`cli` service = PHP 8.5, `db` service = MariaDB 10):

```bash
# Start containers
docker compose up -d

# Run all tests
docker compose run --rm cli vendor/bin/phpunit

# Run a specific test class or method
docker compose run --rm cli vendor/bin/phpunit --filter TestClassName
docker compose run --rm cli vendor/bin/phpunit --filter testMethodName

# Run only Integration or Unit suite
docker compose run --rm cli vendor/bin/phpunit --testsuite Integration
docker compose run --rm cli vendor/bin/phpunit --testsuite Unit

# Update snapshots after intentional HTML output changes
docker compose run --rm cli vendor/bin/phpunit -d --update-snapshots

# Linting
docker compose run --rm cli vendor/bin/phpcs --standard=ruleset.xml
docker compose run --rm cli vendor/bin/phpcbf --standard=ruleset.xml

# Static analysis (PHPStan level 6 + Larastan)
docker compose run --rm cli php -d memory_limit=2G vendor/bin/phpstan analyse
```

## Architecture

### Request flow
Web routes (`routes/web.php`) render Blade views via thin view controllers (`Http/Controllers/`). Form submissions hit API routes (`routes/api.php`) handled by `Http/Controllers/Api/` controllers, which call `Services/` and return JSON. All routes are feature-flagged via `config/pjauth.php`.

### Layers
- **`Services/UserService`** — all business logic (create user, login, password reset, invite flow, change password)
- **`Services/SocialAuthService`** — Google Socialite flow
- **`Repositories/Contracts/`** — `UserRepository`, `RoleRepository`, and `RegisterInviteRepository` contracts
- **`Repositories/Implementations/`** — `EloquentUserRepository`, `EloquentRoleRepository`, `EloquentRegisterInviteRepository`; `UserRepository` binding is swappable via `config/pjauth.repositories.user`
- **`Factories/RoleFactory` / `Factories/UserFactory`** — resolve the active model class from config (supports custom models that extend the built-in ones)
- **`Models/User`** — UUID primary key, `BelongsTo Role`, `hasRole(RoleType)` checks including superadmin bypass
- **`Models/RoleType`** — `int`-backed enum: `SUPERADMIN=1`, `ADMIN=2`, `USER=3`

### Config (`config/pjauth.php`)
Feature flags (`features.*`), recaptcha, social login, repository/model overrides, and redirect paths are all in `pjauth` config. Routes are conditionally registered based on these flags.

**Social login:** `pjauth.social_login.google.enabled` gates routes and the Google button in views. Google OAuth credentials (`client_id`, `client_secret`, `redirect`) must be configured in `config/services.php` under the `google` key — this is where Laravel Socialite reads them. The `pjauth` config holds only the `enabled` flag.

### Events & Notifications
- `RegisteredViaInviteEvent` → `DeleteRegisterInviteListener` (cleans up invite token)
- `RegisterInviteNotification` — mailable notification for invite emails
- `ResetPassword` — custom password reset notification building the reset URL

### Middleware
`VerifyRole` checks the authenticated user's role against a required `RoleType`. Use `VerifyRole::withRole(RoleType::ADMIN)` to generate the middleware string.

## Testing

Tests use MariaDB (not SQLite) — always `docker compose up -d` before running tests.

Base `TestCase` (`tests/TestCase.php`) extends Orchestra Testbench, loads both `AuthServiceProvider` and `UtilsServiceProvider`, runs migrations from `database/migrations/`, and sanitises CSRF tokens in HTML snapshots.

**Test traits (in `tests/Traits/`):**
- `ConfigSetter` — helpers to enable/disable individual features and set config values via `defineEnvironment`
- `UserCreator` — creates test users via `UserFactory` with named builder methods
- `TestingData` — constants for test user credentials (e.g. `TESTER_EMAIL`, `TESTER_PASSWORD`)
- `SocialiteMocker` (in `tests/Mocks/`) — mocks the Socialite facade for Google login tests

Snapshot files live in `tests/Integration/Http/Controllers/__snapshots__/`.

Feature flags are toggled per test using `defineEnvironment` + `ConfigSetter` methods (e.g. `disableRecaptcha`, `enableRegisterViaInvitationFeature`). Call `$this->skipRecaptcha()` in integration tests that don't need to exercise recaptcha middleware.

## Key Conventions

- Repository contracts are in `Repositories/Contracts/`; Eloquent implementations are in `Repositories/Implementations/` with an `Eloquent` prefix
- Model class resolution always goes through `Factories/RoleFactory::getRoleModelClass()` and `Factories/UserFactory::getUserModelClass()` — never hard-code the class name in services or repositories
- PHPStan baseline suppressions are in `phpstan-baseline.neon`; inline `phpcsSuppress` is used only for Eloquent properties that can't satisfy native type hints