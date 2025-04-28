<?php
// database/seeders/CidadeSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cidade;
use Faker\Factory as Faker;

class CidadeSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $cidades = [
            ['nome' => 'São Paulo'],
            ['nome' => 'Rio de Janeiro'],
            ['nome' => 'Belo Horizonte'],
            ['nome' => 'Curitiba'],
            ['nome' => 'Porto Alegre'],
        ];

        foreach ($cidades as $cidade) {
            Cidade::create($cidade);
        }
    }
}