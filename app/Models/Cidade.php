<?php
// app/Models/Cidade.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    protected $fillable = ['nome'];

    public function representantes()
    {
        return $this->belongsToMany(Representante::class, 'cidade_representante');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
}