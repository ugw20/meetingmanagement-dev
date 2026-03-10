<?php
// database/migrations/2024_01_01_000000_add_timer_columns_to_agendas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('presenter');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->text('notes')->nullable()->after('completed_at');
        });
    }

    public function down()
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'completed_at', 'notes']);
        });
    }
};