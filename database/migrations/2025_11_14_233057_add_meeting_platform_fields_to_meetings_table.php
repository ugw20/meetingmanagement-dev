<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeetingPlatformFieldsToMeetingsTable extends Migration
{
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('meeting_platform')->nullable()->after('meeting_link');
            $table->string('meeting_id')->nullable()->after('meeting_platform');
            $table->string('meeting_password')->nullable()->after('meeting_id');
        });
    }

    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['meeting_platform', 'meeting_id', 'meeting_password']);
        });
    }
}