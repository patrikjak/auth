# Upgrade Guide

## Upgrading to custom-roles branch (next major)

This release replaces the hardcoded `RoleType` enum with a fully database-driven role system. Roles are now identified by a UUID primary key and a slug string.

### Breaking changes

#### `RoleType` enum removed

`Patrikjak\Auth\Models\RoleType` no longer exists. Any reference to it must be replaced.

**Before:**
```php
use Patrikjak\Auth\Models\RoleType;

VerifyRole::withRole(RoleType::ADMIN)
$user->hasRole(RoleType::ADMIN)
```

**After:**
```php
VerifyRole::withRole('admin')
$user->hasRole('admin')
```

#### `VerifyRole::withRole` accepts a slug string

The `withRole` method now takes a role slug instead of a `RoleType` enum case.

#### `User::hasRole` accepts a slug string

Same change — pass the role slug as a string.

#### `InviteService::sendInvite` requires a role ID

The `$roleId` parameter is now required and typed `string` (UUID). Passing `null` is no longer accepted.

**Before:**
```php
$inviteService->sendInvite($email);
$inviteService->sendInvite($email, 2); // int
```

**After:**
```php
$inviteService->sendInvite($email, $roleId); // UUID string, required
```

#### `UserRepository::saveRegisterInviteToken` requires a role ID

Same as above — `$roleId` is now `string` and required.

#### `RegisterInvite` value object — `$roleId` is now `string`

`Patrikjak\Auth\ValueObjects\RegisterInvite::$roleId` changed from `?int` to `string`.

#### `UserService::createUserAndLoginViaInvitation` requires a role ID

`$roleId` is now `string` and required (no default).

#### `RoleRepository` interface changes

- `create` return type changed from `Role` to `void`
- New methods: `firstOrCreate`, `findBySlug`, `findById`

If you have a custom `RoleRepository` implementation, update it to implement the new interface.

#### `pjauth:send-invite` command renamed and role required

The command was renamed from `send:register-invite` to `pjauth:send-invite`. Role is now required — if `--role` is not passed, the command prompts interactively.

**Before:**
```bash
php artisan send:register-invite user@example.com --role=2
```

**After:**
```bash
php artisan pjauth:send-invite user@example.com --role=<uuid>
```

#### `pjauth:sync-roles` command renamed

The command class was renamed from `SeedUserRoles` to `SyncRolesCommand`. The artisan signature (`pjauth:sync-roles`) is unchanged.

### Database migration

Run the included upgrade migration to convert the `roles` table from an integer primary key to UUID, and update `role_id` foreign keys in `users` and `register_invites`:

```bash
php artisan vendor:publish --tag="pjauth-migrations" --force
php artisan migrate
```

The migration is safe to re-run — it checks for the presence of the `slug` column before doing any work.

After migrating, run `pjauth:sync-roles` to ensure all default roles are present:

```bash
php artisan pjauth:sync-roles
```

### Config changes

Two new keys are required in `config/pjauth.php`. Re-publish the config:

```bash
php artisan vendor:publish --tag="pjauth-config" --force
```

New keys:

```php
/**
 * Default role slug assigned to new users on registration
 */
'default_role_slug' => 'admin',

/**
 * Default roles synced by the pjauth:sync-roles command
 */
'default_roles' => [
    ['slug' => 'superadmin', 'name' => 'Superadmin', 'is_superadmin' => true],
    ['slug' => 'admin',      'name' => 'Admin',      'is_superadmin' => false],
],
```
