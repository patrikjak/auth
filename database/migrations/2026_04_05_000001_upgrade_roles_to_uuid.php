<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (!$this->needsUpgrade()) {
            return;
        }

        $this->upgradeRolesTable();
        $this->upgradeUsersRoleId();
        $this->upgradeRegisterInvitesRoleId();
    }

    public function down(): void
    {
    }

    private function needsUpgrade(): bool
    {
        return Schema::hasTable('roles')
            && !Schema::hasColumn('roles', 'slug');
    }

    private function upgradeRolesTable(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
            $table->boolean('is_superadmin')->default(false)->after('slug');
            $table->string('new_uuid', 36)->nullable()->after('is_superadmin');
        });

        $defaultRoles = config('pjauth.default_roles', [
            [
                'slug' => 'superadmin',
                'name' => 'Superadmin',
                'is_superadmin' => true,
            ],
            [
                'slug' => 'admin',
                'name' => 'Admin',
                'is_superadmin' => false,
            ],
        ]);

        $roles = DB::table('roles')->orderBy('id')->get();

        $oldIdToUuid = [];
        $defaultByName = array_column($defaultRoles, null, 'name');

        foreach ($roles as $role) {
            $uuid = (string) Str::uuid();
            $defaultRole = $defaultByName[$role->name] ?? null;

            DB::table('roles')->where('id', $role->id)->update([
                'slug' => $defaultRole['slug'] ?? Str::slug($role->name),
                'is_superadmin' => $defaultRole['is_superadmin'] ?? false,
                'new_uuid' => $uuid,
            ]);

            $oldIdToUuid[$role->id] = $uuid;
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('new_role_uuid', 36)->nullable()->after('role_id');
            });

            foreach ($oldIdToUuid as $oldId => $uuid) {
                DB::table('users')->where('role_id', $oldId)->update(['new_role_uuid' => $uuid]);
            }
        }

        if (Schema::hasTable('register_invites') && Schema::hasColumn('register_invites', 'role_id')) {
            Schema::table('register_invites', function (Blueprint $table) {
                $table->string('new_role_uuid', 36)->nullable()->after('role_id');
            });

            foreach ($oldIdToUuid as $oldId => $uuid) {
                DB::table('register_invites')->where('role_id', $oldId)->update(['new_role_uuid' => $uuid]);
            }
        }

        if (DB::getDriverName() === 'sqlite') {
            $this->swapRolesPrimaryKeySqlite();
        } else {
            $this->swapRolesPrimaryKey($oldIdToUuid);
        }
    }

    /**
     * @param array<int|string, string> $oldIdToUuid
     */
    private function swapRolesPrimaryKey(array $oldIdToUuid): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedTinyInteger('old_id')->nullable()->after('id');
        });

        foreach ($oldIdToUuid as $oldInt => $uuid) {
            DB::table('roles')->where('new_uuid', $uuid)->update(['old_id' => $oldInt]);
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->dropPrimary();
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('new_uuid', 'id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->string('id', 36)->primary()->change();
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * SQLite cannot drop a primary key column in-place, so we recreate the table.
     */
    private function swapRolesPrimaryKeySqlite(): void
    {
        DB::statement('CREATE TABLE roles_new (
            id VARCHAR(36) NOT NULL PRIMARY KEY,
            old_id TINYINT UNSIGNED NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            is_superadmin TINYINT(1) NOT NULL DEFAULT 0,
            name VARCHAR(255) NOT NULL
        )');

        DB::statement('INSERT INTO roles_new (id, old_id, slug, is_superadmin, name)
            SELECT new_uuid, id, slug, is_superadmin, name FROM roles');

        DB::statement('DROP TABLE roles');
        DB::statement('ALTER TABLE roles_new RENAME TO roles');
    }

    private function upgradeUsersRoleId(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'new_role_uuid')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('new_role_uuid', 'role_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role_id', 36)->nullable(false)->change();
        });
    }

    private function upgradeRegisterInvitesRoleId(): void
    {
        if (!Schema::hasTable('register_invites') || !Schema::hasColumn('register_invites', 'new_role_uuid')) {
            return;
        }

        Schema::table('register_invites', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });

        Schema::table('register_invites', function (Blueprint $table) {
            $table->renameColumn('new_role_uuid', 'role_id');
        });

        Schema::table('register_invites', function (Blueprint $table) {
            $table->string('role_id', 36)->nullable()->change();
        });
    }
};
