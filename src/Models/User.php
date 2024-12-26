<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Patrikjak\Auth\Database\Factories\UserFactory;
use Patrikjak\Auth\Notifications\ResetPassword;
use SensitiveParameter;

/**
 * @property string $id
 * @property ?string $google_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property int $role_id
 * @property CarbonInterface $created_at
 * @property CarbonInterface $updated_at
 * @property Role $role
 */
class User extends Authenticatable
{
    use HasUuids;
    use HasFactory;
    use Notifiable;

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'google_id',
        'role_id',
        'name',
        'email',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role_id' => RoleType::USER->value,
        'google_id' => null,
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @param string $token
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function sendPasswordResetNotification(#[SensitiveParameter] $token): void
    {
        $url = sprintf('%s?email=%s', route('password.reset', ['token' => $token]), urlencode($this->email));

        $this->notify(new ResetPassword($url));
    }

    public function hasRole(RoleType $role): bool
    {
        $usersRoleId = $this->role->id;

        return $usersRoleId === $role->value || $usersRoleId === RoleType::SUPERADMIN->value;
    }

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
