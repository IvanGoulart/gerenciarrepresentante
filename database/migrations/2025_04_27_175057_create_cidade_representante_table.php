<?php
// database/migrations/2025_04_27_000004_create_cidade_representante_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCidadeRepresentanteTable extends Migration
{
    public function up()
    {
        Schema::create('cidade_representante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidade_id')->constrained()->onDelete('cascade');
            $table->foreignId('representante_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cidade_representante');
    }
}