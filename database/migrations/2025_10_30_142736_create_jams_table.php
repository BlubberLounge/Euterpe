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
        Schema::create('jams', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('created_by')
                ->constrained('users')
                ->onUpdate('cascade');

            $table->string('name');
            $table->string('join_code', 8)->unique();
            $table->boolean('is_active')
                ->default(true);

            $table->unsignedBigInteger('current_track_id')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            // composite unique key
            $table->unique(['name', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jams');
    }
};
