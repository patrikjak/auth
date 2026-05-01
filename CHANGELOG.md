# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-05-01

### Added

- **Database-driven roles** — roles are now stored in the database with a UUID primary key, a `slug`, a `name`, and an `is_superadmin` flag; the hardcoded `RoleType` enum is gone
- **`pjauth:sync-roles` command** — seeds the superadmin role into the database; idempotent (safe to run multiple times); consuming apps are responsible for creating any additional roles
- **`pjauth:send-invite` command** — sends a registration invite to an email address; accepts `--role` with a role UUID, or prompts interactively with the default role pre-selected
- **Role assignment in invitations** — invited users are assigned the role specified at invite time; if no role is given, `InviteService::sendInvite` falls back to the role matching `pjauth.default_role_slug`
- **`RoleRepository`** — new repository interface and implementation with `create`, `firstOrCreate`, `getAll`, `findBySlug`, and `findById` methods
- **`UnauthenticatedException`** (HTTP 401) — thrown by `VerifyRole` middleware when the request has no authenticated user
- **`RoleNotFoundException`** (HTTP 404) — thrown by `InviteService` and `UserService` when a required role cannot be found

### Changed

- Repository contracts moved to `Repositories/Contracts/` (namespace `Patrikjak\Auth\Repositories\Contracts\UserRepository`, `…\RoleRepository`, `…\RegisterInviteRepository`); previously `Repositories/Interfaces/`
- Repository implementations moved to `Repositories/Implementations/` (namespace `Patrikjak\Auth\Repositories\Implementations\EloquentUserRepository`, `…\EloquentRoleRepository`, `…\EloquentRegisterInviteRepository`); update any `pjauth.repositories.user` config overrides to reference the new class name
- Invite-domain methods (`getRegisterInviteToken`, `getRegisterInvite`, `saveRegisterInviteToken`, `deleteRegisterInvite`) removed from `UserRepository`; extracted into a dedicated `Patrikjak\Auth\Repositories\Contracts\RegisterInviteRepository` contract and `EloquentRegisterInviteRepository` implementation
- `Role` model primary key changed from `unsignedTinyInteger` to UUID; existing databases are migrated automatically by the included `upgrade_roles_to_uuid` migration
- `VerifyRole` middleware now accepts a role **slug** string instead of a `RoleType` integer; update all `VerifyRole::withRole(...)` calls (e.g. `VerifyRole::withRole('admin')`)
- `VerifyRole::withRole()` signature changed from `withRole(RoleType $role)` to `withRole(string $slug)`
- `pjauth.default_role_slug` config key controls which role slug is assigned to newly self-registered users; defaults to `'superadmin'`
- `pjauth:sync-roles` only seeds the `superadmin` role — all other roles are the consuming app's responsibility
- All Artisan commands renamed to the `pjauth:` namespace: `install:pjauth` → `pjauth:install`, `create:users` → `pjauth:create-users`, `seed:user-roles` → `pjauth:sync-roles`, `send:register-invite` → `pjauth:send-invite`
- `pjauth:create-users` now prompts for a role **slug** instead of a role integer ID

### Removed

- `RoleType` enum (`Patrikjak\Auth\Models\RoleType`) removed; replace all usages with role slug strings
- `User::hasRole(RoleType)` removed; check roles via `$user->role->slug` or the `is_superadmin` flag directly
- `seed:user-roles` command (and the `SeedUserRoles` class) removed; replaced by `pjauth:sync-roles`

### Fixed

- `InstallCommand` used `exec('rm -rf ...')` to delete migration files; replaced with `unlink()`
- `InviteRegisterRequest` did not validate the `token` field; it is now `required` with a translated error message
- Rate limiter was never cleared on successful login, causing legitimate subsequent logins to be rate-limited; `RateLimiter::clear()` is now called after a successful authentication
- `ChangePasswordRequest::getUserId()` could return `null` silently; now throws `UnauthenticatedException` when no authenticated user is present
- README documented incorrect field names for the change-password endpoint (`old_password` → `current_password`, `validate_old_password` → `validate_current_password`)
- `#[SensitiveParameter]` attribute added to all password parameters in `UserService` and `UserRepository` to prevent passwords appearing in stack traces

## [1.4.1] - 2025-03-11
