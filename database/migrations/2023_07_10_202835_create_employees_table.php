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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf');
            $table->date('admission');
            $table->unsignedBigInteger('id_responsibility')->nullable();
            $table->foreign('id_responsibility')->references('id')->on('responsibilities')->onDelete('cascade')->nullable();
            $table->unsignedBigInteger('id_sector')->nullable();
            $table->foreign('id_sector')->references('id')->on('sectors')->onDelete('cascade')->nullable();
            $table->unsignedBigInteger('id_company')->nullable();
            $table->foreign('id_company')->references('id')->on('companies')->onDelete('cascade')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
