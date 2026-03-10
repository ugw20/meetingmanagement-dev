<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_agendas_table_add_missing_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAgendasTableAddMissingFields extends Migration
{
    public function up()
    {
        Schema::table('agendas', function (Blueprint $table) {
            // Cek dulu field yang sudah ada
            if (!Schema::hasColumn('agendas', 'description')) {
                $table->text('description')->nullable()->after('topic');
            }
            
            if (!Schema::hasColumn('agendas', 'duration')) {
                $table->integer('duration')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('agendas', 'order')) {
                $table->integer('order')->default(0)->after('duration');
            }
            
            if (!Schema::hasColumn('agendas', 'presenter')) {
                $table->string('presenter')->nullable()->after('order');
            }
            
            if (!Schema::hasColumn('agendas', 'notes')) {
                $table->text('notes')->nullable()->after('presenter');
            }
            
            if (!Schema::hasColumn('agendas', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('agendas', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }
        });
    }

    public function down()
    {
        Schema::table('agendas', function (Blueprint $table) {
            // Rollback opsional
        });
    }
}