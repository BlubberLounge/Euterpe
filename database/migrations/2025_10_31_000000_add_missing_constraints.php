<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add unique constraint on jam_users to prevent duplicate memberships
        Schema::table('jam_users', function (Blueprint $table) {
            $table->unique(['jam_id', 'user_id']);
        });

        // Add foreign key for jams.current_track_id with set null on delete
        Schema::table('jams', function (Blueprint $table) {
            $table->foreign('current_track_id')
                ->references('id')
                ->on('jam_queues')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('jam_users', function (Blueprint $table) {
            $table->dropUnique(['jam_id', 'user_id']);
        });

        Schema::table('jams', function (Blueprint $table) {
            $table->dropForeign(['current_track_id']);
        });
    }
};
