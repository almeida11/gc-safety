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
        Schema::create('company_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contratante')->nullable();
            $table->foreign('id_contratante')->references('id')->on('companies')->onDelete('cascade')->nullable();
            $table->unsignedBigInteger('id_contratada')->nullable();
            $table->foreign('id_contratada')->references('id')->on('companies')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_relations');
    }
};
