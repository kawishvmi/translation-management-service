<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        foreach (['en', 'fr', 'es'] as $code) {
            Locale::query()->firstOrCreate(['code' => $code]);
        }

        foreach (['mobile', 'desktop', 'web'] as $name) {
            Tag::query()->firstOrCreate(['name' => $name]);
        }
    }
}
