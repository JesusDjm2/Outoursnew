<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoProveedor extends Model
{
    protected $table = 'tipo_proveedores';

    protected $fillable = [
        'nombre',
    ];

    public function proveedores(): HasMany
    {
        return $this->hasMany(Proveedor::class, 'tipo_id');
    }
}
