<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** ,
        
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('remembers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id')->unique();
            $table->string('step')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('img')->nullable();
            $table->string('companyname')->nullable();
            $table->string('companyemail')->nullable();
            $table->string('companypassword')->nullable();
            $table->string('companyimg')->nullable();
            $table->string('role')->default('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remembers');
    }
};
