<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Patrikjak\Auth\Database\Factories\RoleFactory;

class Role extends Model
{
    use HasFactory;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class)->chaperone();
    }

    protected static function newFactory(): Factory
    {
        return RoleFactory::new();
    }
}
