<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@philsca.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'ICS',
                'middle_name' => null,
                'password' => Hash::make('admin123'),
            ]
        );

        $admin->assignRole('admin');

        // Create Professor
        $prof = User::updateOrCreate(
            ['email' => 'a.alojado@philsca.com'],
            [
                'first_name' => 'Asst. Prof. Aron',
                'last_name' => 'Alojado',
                'middle_name' => 'J.',
                'password' => Hash::make('alojado123'),
            ]
        );

        $prof->assignRole('professor');

        // Create Student
        $student = User::updateOrCreate(
            ['email' => 'y.cabalquinto@philsca.com'],
            [
                'first_name' => 'Ymmanuela Bo',
                'last_name' => 'Cabalquinto',
                'middle_name' => 'Esparrago',
                'year_level' => '2nd Year', // Add year level for student
                'password' => Hash::make('cabalquinto123'),
            ]
        );

        $student->assignRole('student');

        $this->command->info('✅ Admin, Professor, and Student accounts created successfully!');
        $this->command->info('');
        $this->command->info('📧 Login Credentials:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Admin:');
        $this->command->info('  Email: admin@philsca.com');
        $this->command->info('  Password: admin123');
        $this->command->info('  Name: ' . $admin->name);
        $this->command->info('');
        $this->command->info('Professor:');
        $this->command->info('  Email: a.alojado@philsca.com');
        $this->command->info('  Password: alojado123');
        $this->command->info('  Name: ' . $prof->name);
        $this->command->info('');
        $this->command->info('Student:');
        $this->command->info('  Email: y.cabalquinto@philsca.com');
        $this->command->info('  Password: cabalquinto123');
        $this->command->info('  Name: ' . $student->name);
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
