<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            EstadosTableSeeder::class,
            CidadeSeeder::class,
            RepresentanteSeeder::class,
            ClienteSeeder::class,
            CidadeRepresentanteSeeder::class,
        ]);
    }
}