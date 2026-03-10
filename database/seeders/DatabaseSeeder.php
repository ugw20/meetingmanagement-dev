<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            MeetingTypeSeeder::class,
            UserSeeder::class,
            MeetingSeeder::class,
            ActionItemSeeder::class,
        ]);
    }
}