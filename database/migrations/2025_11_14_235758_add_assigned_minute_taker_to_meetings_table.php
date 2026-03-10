<?php
// database/migrations/YYYY_MM_DD_HHMMSS_add_assigned_minute_taker_to_meetings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignedMinuteTakerToMeetingsTable extends Migration
{
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->foreignId('assigned_minute_taker_id')
                  ->nullable()
                  ->after('organizer_id')
                  ->constrained('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['assigned_minute_taker_id']);
            $table->dropColumn('assigned_minute_taker_id');
        });
    }
}