<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:create {--email=admin@exputra.com} {--name=Admin} {--password=}';

    /**
     * The console command description.
     */
    protected $description = 'Create an admin user for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $name = $this->option('name');
        $password = $this->option('password');

        // Check if role column exists
        if (!$this->checkRoleColumn()) {
            $this->error('Role column does not exist in users table.');
            $this->info('Please run the SQL query to add role column first:');
            $this->info('ALTER TABLE users ADD COLUMN role ENUM(\'admin\', \'client\', \'staff\') NOT NULL DEFAULT \'client\';');
            return 1;
        }

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            if ($existingUser->role === 'admin') {
                $this->info("Admin user with email {$email} already exists.");
                return 0;
            } else {
                // Promote existing user to admin
                $existingUser->update(['role' => 'admin']);
                $this->info("User {$email} has been promoted to admin.");
                return 0;
            }
        }

        // Get password if not provided
        if (!$password) {
            $password = $this->secret('Enter password for admin user');
            if (!$password) {
                $this->error('Password is required.');
                return 1;
            }
        }

        // Confirm password
        $confirmPassword = $this->secret('Confirm password');
        if ($password !== $confirmPassword) {
            $this->error('Passwords do not match.');
            return 1;
        }

        try {
            // Create admin user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            $this->info("Admin user created successfully!");
            $this->table(
                ['ID', 'Name', 'Email', 'Role'],
                [[$user->id, $user->name, $user->email, $user->role]]
            );

            $this->info("You can now login at: " . url('/login'));
            
        } catch (\Exception $e) {
            $this->error("Failed to create admin user: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Check if role column exists in users table
     */
    private function checkRoleColumn(): bool
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM users LIKE 'role'");
            return !empty($columns);
        } catch (\Exception $e) {
            return false;
        }
    }
}
