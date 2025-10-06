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
            $table->id(); // id int [PK]
            $table->string('name');
            $table->string('paternal_surname');
            $table->string('maternal_surname');
            $table->string('email')->unique();
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('status')->default(true);
            $table->string('gender')->nullable();
            $table->timestamps(); // created_at & updated_at
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
