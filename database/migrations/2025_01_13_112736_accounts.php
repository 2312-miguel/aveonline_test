<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para la tabla 'accounts'.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            // Relación con el usuario (un usuario tiene una o varias cuentas)
            $table->unsignedBigInteger('user_id')->constrained()->cascadeOnDelete(); // Relación con la tabla users;
            $table->decimal('balance', 12, 2)->default(0.00); // Saldo
            $table->timestamps();

            // Llave foránea
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Revierte la migración (drop de la tabla).
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};