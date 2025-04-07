<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Patrikjak\Auth\Database\Factories\RoleFactory;
use Patrikjak\Auth\Factories\UserFactory;

/**
 * @property int $id
 * @property string $name
 * @property Collection<User> $users
 */
class Role extends Model
{
    use HasFactory;

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
        'id',
        'name',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(UserFactory::getUserModelClass());
    }

    protected static function newFactory(): Factory
    {
        return RoleFactory::new();
    }
}
