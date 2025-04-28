<?php
// database/seeders/RepresentanteSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Representante;
use Faker\Factory as Faker;

class RepresentanteSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            Representante::create([
                'nome' => $faker->name,
            ]);
        }
    }
}
