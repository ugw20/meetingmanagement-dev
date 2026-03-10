<?php
// database/seeders/DepartmentSeeder.php
namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Produksi', 'description' => 'Departemen Produksi'],
            ['name' => 'QA/QC', 'description' => 'Quality Assurance & Quality Control'],
            ['name' => 'Engineering', 'description' => 'Departemen Engineering'],
            ['name' => 'Maintenance', 'description' => 'Departemen Maintenance'],
            ['name' => 'HRD', 'description' => 'Human Resources Development'],
            ['name' => 'Purchasing', 'description' => 'Departemen Purchasing'],
            ['name' => 'Logistik', 'description' => 'Departemen Logistik dan Gudang'],
            ['name' => 'Marketing', 'description' => 'Departemen Pemasaran dan Penjualan'],
            ['name' => 'Finance', 'description' => 'Departemen Keuangan'],
            ['name' => 'IT', 'description' => 'Departemen Teknologi Informasi'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}