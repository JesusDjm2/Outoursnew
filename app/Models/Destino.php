<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destino extends Model
{
    protected $fillable = [
        'nombre',
    ];

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class);
    }
}
