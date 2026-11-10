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
Schema::create('live_sessions', function (Blueprint $table) {
        $table->id();
        $table->uuid('session_id')->unique();
        $table->integer('age');
        $table->string('niveau');
        $table->string('sujet');
        $table->enum('status', ['pending', 'active', 'ended'])->default('pending');
        $table->string('peer_id_student')->nullable();
        $table->string('peer_id_teacher')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_sessions');
    }
};
