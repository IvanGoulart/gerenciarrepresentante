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

        $estadoAcre = \DB::table('estados')->where('nome', 'Acre')->value('id');
        $estadoMinas = \DB::table('estados')->where('nome', 'Minas Gerais')->value('id');
        $estadoRio = \DB::table('estados')->where('nome', 'Rio de Janeiro')->value('id');

        $cidades = [
            ['nome' => 'Rio Branco', 'estado_id' => $estadoAcre],
            ['nome' => 'Belo Horizonte', 'estado_id' => $estadoMinas],
            ['nome' => 'Rio de Janeiro', 'estado_id' => $estadoRio],
        ];

        foreach ($cidades as $cidade) {
            Cidade::create($cidade);
        }
    }
}