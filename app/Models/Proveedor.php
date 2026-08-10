<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'tipo_id',
        'email',
        'telefono',
        'direccion',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoProveedor::class, 'tipo_id');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ProveedorImagen::class)->orderBy('orden');
    }

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class);
    }
}
