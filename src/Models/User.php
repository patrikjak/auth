<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Patrikjak\Auth\Database\Factories\UserFactory;
use Patrikjak\Auth\Exceptions\ModelIsIncompatibleException;
use Patrikjak\Auth\Factories\RoleFactory;
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
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $table = 'users';

    /**
     * @var list<string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
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
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    /**
     * @var array<string, mixed>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $attributes = [
        'role_id' => RoleType::USER->value,
        'google_id' => null,
    ];

    /**
     * @throws ModelIsIncompatibleException
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(RoleFactory::getRoleModelClass());
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
