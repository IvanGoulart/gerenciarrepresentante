<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Cidade;
use Faker\Factory as Faker;

class ClienteSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $cidades = Cidade::all()->pluck('id')->toArray();

        // Verifica se há cidades disponíveis
        if (empty($cidades)) {
            throw new \Exception('Nenhuma cidade encontrada. Por favor, popule a tabela cidades antes de executar o ClienteSeeder.');
        }

        for ($i = 0; $i < 20; $i++) {
            Cliente::create([
                'nome' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'cidade_id' => $faker->randomElement($cidades),
            ]);
        }
    }
}
?>