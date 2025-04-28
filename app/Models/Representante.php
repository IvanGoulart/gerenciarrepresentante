<?php
// app/Models/Representante.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Representante extends Model
{
    protected $table = 'representantes';
    protected $fillable = ['nome', 'cidade_id'];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }

    // app/Models/Representante.php
    public function cidades()
    {
        return $this->belongsToMany(Cidade::class, 'cidade_representante');
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'representantes_clientes', 'representante_id', 'cliente_id');
    }
}