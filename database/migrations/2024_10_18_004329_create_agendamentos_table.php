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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_contato');
            $table->unsignedBigInteger('id_atendimento');
            $table->unsignedBigInteger('id_categoria');
            $table->date('data_leilao')->nullable();;
            $table->date('data');
            $table->time('hora');
            $table->timestamps();

            $table->foreign('id_cliente')->references('id')->on('clientes');
            $table->foreign('id_contato')->references('id')->on('contatos');
            $table->foreign('id_atendimento')->references('id')->on('atendimentos');
            $table->foreign('id_categoria')->references('id')->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropForeign(['id_cliente']);
            $table->dropForeign(['id_contato']);
            $table->dropForeign(['id_atendimento']);
            $table->dropForeign(['id_categoria']);
        });

        Schema::dropIfExists('agendamentos');
    }
};
