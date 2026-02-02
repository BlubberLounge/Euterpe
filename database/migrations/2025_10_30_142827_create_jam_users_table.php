<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Jam;
use App\Enums\JamRole;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jam_users', function (Blueprint $table)
        {
            $table->id();

            $table->foreignIdFor(Jam::class)
                ->constrained('jams')
                ->onUpdate('cascade');
            $table->foreignIdFor(User::class)
                ->constrained('users')
                ->onUpdate('cascade');
            $table->enum('role', array_column(JamRole::cases(), 'value'));
            $table->timestamp('joined_at')
                ->nullable();
            $table->timestamp('left_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_users');
    }
};
