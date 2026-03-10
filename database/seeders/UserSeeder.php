<?php
// database/seeders/UserSeeder.php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Tambahkan ini
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department_id' => 10, // IT
            'position' => 'System Administrator',
            'phone' => '081234567890',
        ]);

        // Manager Users
        $managers = [
            [
                'name' => 'Manager Produksi',
                'email' => 'manager.produksi@company.com',
                'department_id' => 1,
                'position' => 'Production Manager',
            ],
            [
                'name' => 'Manager QA/QC',
                'email' => 'manager.qaqc@company.com',
                'department_id' => 2,
                'position' => 'QA/QC Manager',
            ],
            [
                'name' => 'Manager Engineering',
                'email' => 'manager.engineering@company.com',
                'department_id' => 3,
                'position' => 'Engineering Manager',
            ],
        ];

        foreach ($managers as $manager) {
            User::create(array_merge($manager, [
                'password' => Hash::make('password'),
                'role' => 'manager',
                'phone' => '081234567891',
            ]));
        }

        // Regular Users
        $users = [
            ['name' => 'Operator Produksi 1', 'email' => 'operator1@company.com', 'department_id' => 1, 'position' => 'Production Operator'],
            ['name' => 'Quality Inspector', 'email' => 'quality@company.com', 'department_id' => 2, 'position' => 'Quality Inspector'],
            ['name' => 'Mechanical Engineer', 'email' => 'engineer1@company.com', 'department_id' => 3, 'position' => 'Mechanical Engineer'],
            ['name' => 'Maintenance Technician', 'email' => 'maintenance1@company.com', 'department_id' => 4, 'position' => 'Maintenance Technician'],
            ['name' => 'HR Staff', 'email' => 'hr1@company.com', 'department_id' => 5, 'position' => 'HR Staff'],
            ['name' => 'Purchasing Officer', 'email' => 'purchasing1@company.com', 'department_id' => 6, 'position' => 'Purchasing Officer'],
        ];

        foreach ($users as $user) {
            User::create(array_merge($user, [
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081234567892',
            ]));
        }
    }
}