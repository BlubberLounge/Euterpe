<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Enums\ExternalServices;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('external_service_users', function (Blueprint $table)
        {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->onUpdate('cascade');
            $table->enum('service', ExternalServices::cases());

            $table->string('name');
            $table->string('email');
            $table->string('image')
                ->nullable();

            $table->string('access_token')
                ->nullable();
            $table->string('refresh_token')
                ->nullable();
            $table->dateTime('token_expires_at');

            $table->timestamps();
            $table->softDeletes();

            // composite unique key
            $table->unique(['service', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_service_users');
    }
};
