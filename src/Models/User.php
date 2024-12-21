<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Patrikjak\Auth\Database\Factories\UserFactory;

/**
 * @property string $id
 * @property ?string $google_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property int $role_id
 */
class User extends Authenticatable
{
    use HasUuids;
    use HasFactory;

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

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
