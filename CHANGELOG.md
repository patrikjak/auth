# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.5.0] - 2026-04-08

### Added

- **Database-driven roles** — roles are now stored in the database with a UUID primary key, a `slug`, a `name`, and an `is_superadmin` flag; the hardcoded `RoleType` enum is gone
- **`pjauth:sync-roles` command** — seeds the superadmin role into the database; idempotent (safe to run multiple times); consuming apps are responsible for creating any additional roles
- **`pjauth:send-invite` command** — sends a registration invite to an email address; accepts `--role` with a role UUID, or prompts interactively with available roles listed
- **Role assignment in invitations** — invited users are assigned the role specified at invite time; `InviteService::sendInvite` now requires a role UUID
- **`RoleRepository`** — new repository interface and implementation with `create`, `firstOrCreate`, `getAll`, `findBySlug`, and `findById` methods
- **`UnauthenticatedException`** (HTTP 401) — thrown by `VerifyRole` middleware when the request has no authenticated user
- **`RoleNotFoundException`** (HTTP 404) — thrown by `InviteService` and `UserService` when a required role cannot be found

### Changed

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

## [1.4.1] - 2025-03-11
