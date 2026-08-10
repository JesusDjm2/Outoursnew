<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProveedorImagen extends Model
{
    protected $table = 'proveedor_imagenes';

    protected $fillable = [
        'proveedor_id',
        'path',
        'orden',
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }
}
