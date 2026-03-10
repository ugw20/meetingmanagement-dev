<?php
// database/migrations/2024_01_01_000006_create_meeting_participants_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['chairperson', 'participant', 'secretary'])->default('participant');
            $table->boolean('is_required')->default(true);
            $table->boolean('attended')->default(false);
            $table->text('excuse')->nullable(); // alasan tidak hadir
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_participants');
    }
};