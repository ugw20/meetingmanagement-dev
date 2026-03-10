<?php
// database/seeders/ActionItemSeeder.php
namespace Database\Seeders;

use App\Models\ActionItem;
use App\Models\Meeting;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActionItemSeeder extends Seeder
{
    public function run(): void
    {
        $meetings = Meeting::all();

        foreach ($meetings as $meeting) {
            // Buat beberapa action items untuk setiap meeting
            $actions = [
                [
                    'title' => 'Perbaikan mesin produksi A',
                    'description' => 'Lakukan perbaikan pada mesin A yang mengalami gangguan',
                    'assigned_to' => 7, // Maintenance staff
                    'department_id' => 4, // Maintenance
                    'due_date' => Carbon::now()->addDays(3),
                    'status' => 'in_progress',
                    'priority' => 3,
                ],
                [
                    'title' => 'Update SOP quality control',
                    'description' => 'Revisi standard operating procedure untuk quality control',
                    'assigned_to' => 4, // Quality Inspector
                    'department_id' => 2, // QA/QC
                    'due_date' => Carbon::now()->addDays(7),
                    'status' => 'pending',
                    'priority' => 2,
                ],
                [
                    'title' => 'Training operator baru',
                    'description' => 'Melakukan training untuk operator baru pada mesin B',
                    'assigned_to' => 2, // Manager Produksi
                    'department_id' => 1, // Produksi
                    'due_date' => Carbon::now()->addDays(5),
                    'status' => 'completed',
                    'priority' => 2,
                    'completion_notes' => 'Training telah dilaksanakan pada 15 Januari 2024',
                    'completed_at' => Carbon::now()->subDays(1),
                ],
            ];

            foreach ($actions as $action) {
                ActionItem::create(array_merge($action, [
                    'meeting_id' => $meeting->id,
                ]));
            }
        }

        echo "Action items dummy data created successfully!\n";
    }
}