<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminCommand extends Command
{
    protected $signature = 'portfolio:admin
                            {--email= : Admin email address}
                            {--password= : New password (min 12 characters)}
                            {--name= : Display name (kept when omitted on an existing user)}';

    protected $description = 'Create or reset the admin account (interactive, or use --email/--password)';

    public function handle(): int
    {
        $email = $this->option('email') ?: $this->ask('Admin email', config('portfolio.admin.email') ?? '');
        $password = $this->option('password');

        $validator = Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email']]
        );

        if ($validator->fails()) {
            $this->error('Please provide a valid email address (use --email="you@example.com").');

            return self::FAILURE;
        }

        if ($password === null || $password === false) {
            $password = $this->secret('New password (min 12 characters, input hidden)');
        }

        if (! is_string($password) || strlen($password) < 12) {
            $this->error('The password must be at least 12 characters. Nothing was changed.');

            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => $email]);

        if ($this->option('name')) {
            $user->name = $this->option('name');
        } elseif (! $user->exists) {
            $user->name = 'Md Naime';
        }

        $user->is_admin = true;
        $user->password = Hash::make($password);
        $user->save();

        $this->info($user->wasRecentlyCreated
            ? "Admin account created for {$email}."
            : "Password reset for existing admin {$email}. Existing name and content were kept.");

        return self::SUCCESS;
    }
}
