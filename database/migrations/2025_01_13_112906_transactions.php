<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para la tabla 'transactions'.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Para identificar la transacción (por ejemplo "TX12345")
            $table->string('transaction_number')->unique()->require();

            // Relación con la cuenta
            $table->unsignedBigInteger('account_id');

            // Monto de la transacción
            $table->decimal('amount', 12, 2);

            // Tipo de transacción (deposit, withdraw, etc.)
            $table->string('type')->default('deposit');

            $table->timestamps();

            // Llave foránea
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Revierte la migración (drop de la tabla).
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
