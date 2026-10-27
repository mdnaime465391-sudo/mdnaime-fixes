<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

/**
 * Creates the single admin account. Never deletes an existing user.
 *
 * Reads ADMIN_EMAIL / ADMIN_PASSWORD through config('portfolio.admin.*').
 * In production the password is mandatory and must be at least 12 characters.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('portfolio.admin.email');
        $envPassword = config('portfolio.admin.password');
        $isProduction = app()->environment('production');

        if (! $email) {
            if ($isProduction) {
                throw new RuntimeException(
                    'ADMIN_EMAIL is not set. Add ADMIN_EMAIL to your .env file before seeding production.'
                );
            }

            $email = 'mdnaime4657@gmail.com';
            $this->command?->warn('ADMIN_EMAIL is not set — falling back to '.$email.' (non-production only).');
        }

        if ($envPassword !== null && strlen($envPassword) < 12) {
            if ($isProduction) {
                throw new RuntimeException(
                    'ADMIN_PASSWORD is shorter than 12 characters. Use a strong password (min 12 chars) when seeding production.'
                );
            }

            $this->command?->warn('ADMIN_PASSWORD is shorter than 12 characters — using it anyway (non-production only).');
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $isNew = ! $user->exists;

        $password = $envPassword;

        if ($password === null) {
            if ($isNew) {
                if ($isProduction) {
                    throw new RuntimeException(
                        'ADMIN_PASSWORD is not set. Add a strong ADMIN_PASSWORD (min 12 chars) to your .env file before seeding production. Remove the variable after the first seed.'
                    );
                }

                $password = 'ChangeMe!2026';
                $this->command?->warn('ADMIN_PASSWORD is not set — assigning a development-only default password. Never run this default on a public server.');
            }
            // Existing user without ADMIN_PASSWORD in .env: leave the current password untouched.
        }

        $user->name = $user->name ?: 'Md Naime';
        $user->is_admin = true;

        if ($password !== null) {
            $user->password = Hash::make($password);
        }

        $user->save();

        $this->command?->info($isNew ? "Admin account created: {$email}" : "Admin account ready (existing user kept): {$email}");
    }
}
