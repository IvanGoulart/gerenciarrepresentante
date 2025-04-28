<?php
// database/seeders/CidadeRepresentanteSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cidade;
use App\Models\Representante;
use Faker\Factory as Faker;

class CidadeRepresentanteSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $cidades = Cidade::all();
        $representantes = Representante::all();

        foreach ($representantes as $representante) {
            // Cada representante estará em 1 a 3 cidades aleatoriamente
            $cidadesAleatorias = $cidades->random($faker->numberBetween(1, 3));
            $representante->cidades()->attach($cidadesAleatorias->pluck('id'));
        }
    }
}