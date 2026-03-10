<?php
// database/migrations/2024_01_01_000001_add_timer_columns_to_meetings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('status');
            $table->timestamp('ended_at')->nullable()->after('started_at');
            $table->unsignedBigInteger('current_agenda_id')->nullable()->after('ended_at');
            
            $table->foreign('current_agenda_id')->references('id')->on('agendas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['current_agenda_id']);
            $table->dropColumn(['started_at', 'ended_at', 'current_agenda_id']);
        });
    }
};