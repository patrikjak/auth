# CLAUDE.md

## Project Overview

`patrikjak/auth` is a Laravel package (library) that provides authentication functionality for Laravel applications. It includes registration, login, password reset, social login (Google via Socialite), role-based authorization, and invitation-based registration.

- **Language**: PHP 8.4, TypeScript, SCSS
- **Framework**: Laravel 12
- **Package type**: Composer library (not a standalone app)
- **Namespace**: `Patrikjak\Auth`
- **Dependency**: Requires `patrikjak/utils` (^2.3.0)

## Build & Development Commands

### PHP

```bash
# Install dependencies
composer install

# Run tests (requires Docker for MariaDB)
docker compose up -d
docker compose exec cli vendor/bin/phpunit

# Run specific test suite
docker compose exec cli vendor/bin/phpunit --testsuite=Unit
docker compose exec cli vendor/bin/phpunit --testsuite=Integration

# Static analysis (PHPStan level 6)
vendor/bin/phpstan analyse

# Code style check (Slevomat Coding Standard via PHP_CodeSniffer)
vendor/bin/phpcs --standard=ruleset.xml

# Code style fix
vendor/bin/phpcbf --standard=ruleset.xml
```

### Frontend

```bash
npm install
npm run build    # Vite build to public/assets
npm run dev      # Vite dev server
```

## Project Structure

```
src/
  AuthServiceProvider.php       # Main service provider
  Console/Commands/             # Artisan commands (create:users, install:pjauth, seed:user-roles, send:register-invite)
  Events/                       # RegisteredViaInviteEvent
  Exceptions/                   # Custom exceptions
  Factories/                    # UserFactory, RoleFactory (runtime model resolution)
  Http/
    Controllers/                # Web controllers (view rendering)
    Controllers/Api/            # API controllers (form submissions)
    Middlewares/                 # VerifyRole middleware
    Requests/                   # Form request validation classes
  Listeners/                    # DeleteRegisterInviteListener
  Models/                       # User, Role, RoleType enum
  Notifications/                # Mail notifications (ResetPassword, RegisterInvite)
  Repositories/                 # Data access layer with interfaces
  Rules/                        # Custom validation rules (CurrentPassword)
  Services/                     # Business logic (UserService, SocialAuthService)
  View/Layouts/                 # Blade view components
config/                         # pjauth.php (package config), auth.php (Laravel auth config)
database/
  factories/                    # Eloquent model factories for testing
  migrations/                   # roles, users, password_reset_tokens, sessions, register_invites
  seeders/                      # UserSeeder
resources/
  css/                          # SCSS stylesheets
  js/                           # TypeScript entry point
  views/                        # Blade templates (login, register, reset, notifications)
routes/
  api.php                       # API routes (POST login, register, password reset, etc.)
  web.php                       # Web routes (GET pages for login, register, etc.)
tests/
  Integration/                  # Integration tests (database-dependent, HTTP tests)
  Unit/                         # Unit tests (isolated logic)
  Mocks/                        # Mock models for testing
  Traits/                       # Shared test helpers
  TestCase.php                  # Base test case (Orchestra Testbench)
```

## Architecture Patterns

- **Repository pattern**: Interfaces in `Repositories/Interfaces/`, concrete implementations in `Repositories/`. User repository is configurable via `pjauth.repositories.user` config.
- **Service layer**: Business logic lives in `Services/` (UserService, SocialAuthService).
- **Factory pattern**: `Factories/UserFactory` and `Factories/RoleFactory` handle runtime model class resolution (supports custom model subclasses via config).
- **Web + API split**: Web controllers render Blade views; API controllers handle form submissions and return JSON.
- **Feature flags**: Features (register, login, password reset, etc.) are toggled via `config/pjauth.php`.

## Coding Conventions

### PHP Style

- **Strict types**: Every PHP file starts with `declare(strict_types = 1);` (note the spaces around `=`)
- **Final/readonly**: Service classes use `final readonly` where appropriate
- **No comments on obvious code**: Comments are minimal and only where logic isn't self-evident
- **PHPDoc for suppression**: `@phpcsSuppress` annotations are used where native type hints conflict with Laravel's property expectations
- **Type hints**: Full parameter and return type hints on all methods
- **Alphabetical imports**: Enforced by Slevomat coding standard
- **Trailing commas**: Required in arrays and multi-line function parameters
- **Max class length**: 300 lines
- **Constructor property promotion**: Required by coding standard

### Naming Conventions

- **Models**: Singular (`User`, `Role`)
- **Controllers**: Resource-style (`LoginController`, `RegisterController`)
- **Requests**: Descriptive (`LoginRequest`, `RegisterRequest`, `ChangePasswordRequest`)
- **Repositories**: Interface name matches implementation name (`UserRepository` interface + `UserRepository` class in different namespace)
- **Enum**: PascalCase cases (`SUPERADMIN`, `ADMIN`, `USER`)

### Testing

- **Orchestra Testbench**: Used as the base test runner for package testing
- **Snapshot testing**: HTML snapshots via `spatie/phpunit-snapshot-assertions`
- **Integration tests**: Hit the database (MariaDB via Docker), use HTTP test methods
- **Unit tests**: Isolated, no database
- **Test traits**: Shared helpers in `tests/Traits/` (ConfigSetter, TestingData, UserCreator, SocialiteMocker)
- **Locale**: Tests use `'test'` locale

### Frontend

- **Vite**: Build tool with `laravel-vite-plugin`
- **TypeScript**: Frontend scripts
- **SCSS**: Stylesheets
- **i18n-js**: Internationalization in JavaScript

## Configuration

Key config file: `config/pjauth.php`

- `app_name`: Application name for views
- `logo_path`, `icon`: Branding assets
- `recaptcha.enabled`, `recaptcha.site_key`, `recaptcha.secret_key`: reCAPTCHA integration
- `social_login.google.*`: Google OAuth settings
- `features.*`: Toggle register, login, password_reset, change_password, register_via_invitation
- `repositories.user`: Custom user repository class
- `models.role`: Custom role model class
- `redirect_after_login`, `redirect_after_logout`: Post-auth redirect paths
- `user_default_password`: Default password for seeded users

## Database

- **users**: UUID primary key, role_id (foreign), google_id (nullable), name, email (unique), password, remember_token, timestamps
- **roles**: unsignedTinyInteger primary key, name
- **register_invites**: email (primary), token, created_at
- **password_reset_tokens**: email (primary), token, created_at
- **sessions**: Standard Laravel sessions table

## Docker

```bash
docker compose up -d    # Starts PHP CLI + MariaDB containers
```

- `auth-cli`: PHP 8.4 CLI with Xdebug (develop, debug, coverage modes)
- `auth-test-db`: MariaDB 10 (user: `user`, password: `password`, database: `testing`)
