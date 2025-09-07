<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Options can be passed: --name=, --email=, --password=
     */
    protected $signature = 'user:create-admin
                            {--name= : The name of the admin user}
                            {--email= : The email of the admin user}
                            {--password= : The password of the admin user}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new administrator user';

    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('Name');
        $email = $this->option('email') ?? $this->ask('Email');
        $password = $this->option('password');

        if (! $password) {
            $password = $this->secret('Password');
            $confirm = $this->secret('Confirm Password');

            if ($password !== $confirm) {
                $this->error('Passwords do not match.');

                return self::FAILURE;
            }
        }

        // Validate inputs
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $user->assignRole(Role::Administrator->value);

        $this->info("Administrator user [{$user->email}] created successfully.");

        return self::SUCCESS;
    }
}
