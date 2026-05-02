<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Patrikjak\Auth\Database\Factories\RoleFactory;
use Patrikjak\Auth\Factories\UserFactory;

/**
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property bool $is_superadmin
 * @property Collection<int, User> $users
 */
class Role extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var bool
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    public $timestamps = false;

    /**
     * @var list<string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $fillable = [
        'slug',
        'name',
        'is_superadmin',
    ];

    /**
     * @var array<string, string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $casts = [
        'is_superadmin' => 'boolean',
    ];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(UserFactory::getUserModelClass());
    }

    protected static function newFactory(): Factory
    {
        return RoleFactory::new();
    }
}
