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
        Schema::create('user_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_company')->nullable();
            $table->foreign('id_company')->references('id')->on('companies')->onDelete('cascade')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade')->nullable();
            $table->boolean('is_manager');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_relations');
    }
};
