# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed

### Security

## [1.5.0] - 2026-04-06

### Added

- **Custom roles** — roles are now fully database-driven with a `slug`, `name`, and `is_superadmin` flag; any number of roles can be defined and seeded via the `pjauth.default_roles` config key
- **`pjauth:sync-roles` command** — syncs the default roles defined in `pjauth.default_roles` into the database; idempotent (safe to run multiple times)
- **`pjauth:send-invite` command** — sends a registration invite to an email address with an optional `--role` option to pre-assign a role to the invited user
- **Role assignment in invitations** — invited users can be assigned a specific role at invite time; if no role is specified the default role from `pjauth.default_role_slug` is used on registration
- **`RoleRepository`** — new repository with `create`, `firstOrCreate`, `getAll`, `findBySlug`, and `findById` methods; bound via `RoleRepository` interface
- **`UnauthenticatedException`** (HTTP 401) — thrown by `VerifyRole` middleware when the request has no authenticated user
- **`RoleNotFoundException`** (HTTP 404) — thrown by `InviteService` when a given role ID does not exist

### Changed

- `Role` model primary key changed from `unsignedTinyInteger` to UUID; existing databases are migrated automatically by the `upgrade_roles_to_uuid` migration
- `VerifyRole` middleware now accepts a role **slug** string instead of a `RoleType` integer value; update all `VerifyRole::withRole(...)` calls to pass a slug (e.g. `VerifyRole::withRole('admin')`)
- `VerifyRole::withRole()` signature changed from `withRole(RoleType $role)` to `withRole(string $slug)`
- `pjauth.default_role_slug` config key (new) controls which role slug is assigned to newly registered users; defaults to `'admin'`
- Default roles reduced to `superadmin` (is_superadmin) and `admin`; the `user` role is no longer seeded by default
- All Artisan commands renamed to the `pjauth:` namespace for discoverability: `install:pjauth` → `pjauth:install`, `create:users` → `pjauth:create-users`, `seed:user-roles` → `pjauth:sync-roles`, `send:register-invite` → `pjauth:send-invite`

### Removed

- `RoleType` enum (`Patrikjak\Auth\Models\RoleType`) removed; replace all usages with role slug strings

## [1.4.1] - 2025-03-11
