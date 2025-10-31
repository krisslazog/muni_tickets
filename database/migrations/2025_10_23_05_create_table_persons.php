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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->string('first_name');
            $table->string('last_name_paternal');
            $table->string('last_name_maternal');

            //Documentos
            $table->string('document_type'); // Ej: DNI, CE, PAS
            $table->string('document_number');

            $table->enum('gender', ['M', 'F', 'O'])->nullable(); // M: Masculino, F: Femenino, O: Otro
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();

            $table->string('email')->nullable()->unique();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->boolean('status')->default(true); // true = activo, false = inactivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
