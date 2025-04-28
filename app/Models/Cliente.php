<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clientes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nome', 'email', 'cidade_id'];

    /**
     * Get the cidade that the cliente belongs to.
     */
    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }

    /**
     * Get the representantes associated with the cliente.
     */
    public function representantes()
    {
        return $this->belongsToMany(Representante::class, 'representantes_clientes', 'cliente_id', 'representante_id');
    }
}
?>
