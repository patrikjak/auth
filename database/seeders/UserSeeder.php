<?php

namespace Patrikjak\Auth\Database\Seeders;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Str;

final class UserSeeder extends Seeder
{
    public function __construct(
        private readonly DatabaseManager $databaseManager,
        private readonly Str $str,
        private readonly HashManager $hashManager,
    ) {
    }

    public function run(): void
    {
        $this->databaseManager->table('users')->insert([
            'name' => $this->str::random(10),
            'email' => $this->str::random(10) . '@example.com',
            'password' => $this->hashManager->make('password'),
        ]);
    }
}
