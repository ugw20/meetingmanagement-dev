<?php
// database/seeders/MeetingSeeder.php
namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\Agenda;
use App\Models\MeetingParticipant;
use App\Models\MeetingFile;
use App\Models\MeetingMinute;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        // Meeting 1: Rapat Harian Produksi
        $meeting1 = Meeting::create([
            'title' => 'Rapat Harian Produksi Shift Pagi',
            'description' => 'Koordinasi produksi harian untuk shift pagi, review target dan kendala',
            'meeting_type_id' => 1,
            'organizer_id' => 2, // Manager Produksi
            'department_id' => 1, // Produksi
            'start_time' => Carbon::now()->subDays(2)->setTime(8, 0),
            'end_time' => Carbon::now()->subDays(2)->setTime(9, 0),
            'location' => 'Ruang Meeting Produksi',
            'status' => 'completed',
            'is_online' => false,
        ]);

        // Participants untuk meeting 1
        $participants1 = [3, 4, 5]; // Operator dan Quality
        foreach ($participants1 as $participantId) {
            MeetingParticipant::create([
                'meeting_id' => $meeting1->id,
                'user_id' => $participantId,
                'role' => 'participant',
                'is_required' => true,
                'attended' => true,
            ]);
        }

        // Agendas untuk meeting 1
        $agendas1 = [
            ['topic' => 'Review Produksi Kemarin', 'description' => 'Pencapaian target produksi hari sebelumnya', 'duration' => 15, 'presenter' => 'Manager Produksi'],
            ['topic' => 'Kendala Produksi', 'description' => 'Identifikasi masalah dan solusi', 'duration' => 25, 'presenter' => 'Team Leader'],
            ['topic' => 'Target Hari Ini', 'description' => 'Penetapan target produksi hari ini', 'duration' => 20, 'presenter' => 'Manager Produksi'],
        ];

        foreach ($agendas1 as $index => $agenda) {
            Agenda::create([
                'meeting_id' => $meeting1->id,
                'topic' => $agenda['topic'],
                'description' => $agenda['description'],
                'duration' => $agenda['duration'],
                'order' => $index,
                'presenter' => $agenda['presenter'],
            ]);
        }

        // Meeting Minutes untuk meeting 1
        MeetingMinute::create([
            'meeting_id' => $meeting1->id,
            'minute_taker_id' => 2,
            'content' => "Rapat produksi harian berjalan dengan baik. Beberapa kendala mesin telah diidentifikasi dan sedang dalam proses perbaikan. Target produksi hari ini ditetapkan sebesar 500 unit.",
            'decisions' => ['Perbaikan mesin A oleh maintenance', 'Penambahan overtime untuk mencapai target'],
            'is_finalized' => true,
            'finalized_at' => Carbon::now()->subDays(2)->setTime(10, 0),
        ]);

        // Meeting 2: Rapat Quality Review
        $meeting2 = Meeting::create([
            'title' => 'Quality Review Meeting Mingguan',
            'description' => 'Review kualitas produk dan proses manufacturing minggu ini',
            'meeting_type_id' => 2,
            'organizer_id' => 3, // Manager QA/QC
            'department_id' => 2, // QA/QC
            'start_time' => Carbon::now()->subDays(1)->setTime(10, 0),
            'end_time' => Carbon::now()->subDays(1)->setTime(11, 30),
            'location' => 'Ruang Quality Control',
            'status' => 'completed',
            'is_online' => false,
        ]);

        // Participants untuk meeting 2
        $participants2 = [2, 4, 5, 6]; // Manager Produksi, Operator, Engineer
        foreach ($participants2 as $participantId) {
            MeetingParticipant::create([
                'meeting_id' => $meeting2->id,
                'user_id' => $participantId,
                'role' => 'participant',
                'is_required' => true,
                'attended' => true,
            ]);
        }

        // Meeting 3: Rapat Engineering (Akan Datang)
        $meeting3 = Meeting::create([
            'title' => 'Rapat Koordinasi Project Mesin Baru',
            'description' => 'Koordinasi instalasi dan commissioning mesin produksi baru',
            'meeting_type_id' => 3,
            'organizer_id' => 6, // Engineer
            'department_id' => 3, // Engineering
            'start_time' => Carbon::now()->addDays(2)->setTime(14, 0),
            'end_time' => Carbon::now()->addDays(2)->setTime(16, 0),
            'location' => 'Ruang Engineering',
            'status' => 'scheduled',
            'is_online' => true,
            'meeting_link' => 'https://meet.google.com/abc-def-ghi',
        ]);

        // Participants untuk meeting 3
        $participants3 = [2, 3, 7, 8]; // Manager Produksi, QA/QC, Maintenance
        foreach ($participants3 as $participantId) {
            MeetingParticipant::create([
                'meeting_id' => $meeting3->id,
                'user_id' => $participantId,
                'role' => 'participant',
                'is_required' => true,
                'attended' => false,
            ]);
        }

        // Meeting Files contoh
        MeetingFile::create([
            'meeting_id' => $meeting1->id,
            'uploaded_by' => 2,
            'file_name' => 'laporan_produksi.pdf',
            'file_path' => 'meeting_files/1/laporan_produksi.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 1024000,
            'description' => 'Laporan produksi mingguan',
        ]);

        echo "Meetings dummy data created successfully!\n";
    }
}