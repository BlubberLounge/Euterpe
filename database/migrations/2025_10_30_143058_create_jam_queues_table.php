<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jam_queues', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('jam_id')
                ->constrained('jams')
                ->onDelete('cascade');
            $table->foreignId('added_by_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Track metadata from Spotify
            $table->string('spotify_uri', 100);
            $table->string('track_name', 255);
            $table->string('artist_name', 255);
            $table->string('album_name', 255)->nullable();
            $table->string('album_image_url', 500)->nullable();
            $table->integer('duration_ms')->nullable();

            // Queue management
            $table->unsignedInteger('position')->default(0);
            $table->timestamp('played_at')->nullable();
            $table->boolean('is_playing')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['jam_id', 'position']);
            $table->index(['jam_id', 'played_at']);
        });

        Schema::table('jams', function (Blueprint $table) {
            $table->foreign('current_track_id')
                ->references('id')
                ->on('jam_queues')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_queues');
    }
};
