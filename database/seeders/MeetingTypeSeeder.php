<?php
// database/seeders/MeetingTypeSeeder.php
namespace Database\Seeders;

use App\Models\MeetingType;
use Illuminate\Database\Seeder;

class MeetingTypeSeeder extends Seeder
{
    public function run(): void
    {
        $meetingTypes = [
            [
                'name' => 'Rapat Harian Produksi',
                'description' => 'Rapat koordinasi harian departemen produksi',
                'required_fields' => ['production_report', 'quality_issues', 'maintenance_needs']
            ],
            [
                'name' => 'Rapat Quality Review',
                'description' => 'Review kualitas produk dan proses',
                'required_fields' => ['quality_metrics', 'defect_analysis', 'corrective_actions']
            ],
            [
                'name' => 'Rapat Engineering',
                'description' => 'Koordinasi proyek engineering dan maintenance',
                'required_fields' => ['project_updates', 'technical_issues', 'resource_allocation']
            ],
            [
                'name' => 'Rapat Manajemen',
                'description' => 'Rapat strategis manajemen perusahaan',
                'required_fields' => ['performance_review', 'strategic_decisions', 'budget_analysis']
            ],
            [
                'name' => 'Rapat Safety',
                'description' => 'Review keselamatan dan kesehatan kerja',
                'required_fields' => ['safety_incidents', 'risk_assessment', 'safety_improvements']
            ],
        ];

        foreach ($meetingTypes as $type) {
            MeetingType::create($type);
        }
    }
}