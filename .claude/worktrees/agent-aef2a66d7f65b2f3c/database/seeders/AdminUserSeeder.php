<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@admin.com')->first();
        
        if (!$existingAdmin) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@admin.com',
                'password' => Hash::make('Admin@2025'),
                'user_type' => 'admin',
                'website_id' => null,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@admin.com');
            $this->command->info('Password: Admin@2025');
        } else {
            // Update existing user to ensure correct credentials
            $existingAdmin->update([
                'name' => 'Administrator',
                'password' => Hash::make('Admin@2025'),
                'user_type' => 'admin',
                'website_id' => null,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Admin user updated successfully!');
            $this->command->info('Email: admin@admin.com');
            $this->command->info('Password: Admin@2025');
        }
    }
}
