<?php
// database/migrations/2024_01_01_000005_create_agendas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->string('topic');
            $table->text('description')->nullable();
            $table->integer('duration')->nullable(); // dalam menit
            $table->integer('order')->default(0);
            $table->string('presenter')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};