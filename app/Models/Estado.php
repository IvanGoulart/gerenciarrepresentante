<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nome', 'uf'];

    public function cidades()
    {
        return $this->hasMany(Cidade::class, 'estado_id');
    }
}
?>