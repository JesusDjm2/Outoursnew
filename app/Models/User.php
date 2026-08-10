<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'ruc',
        'telefono',
        'direccion',
        'celulares',
        'whatsapp',
        'logo_path',
        'colores',
        'terminos_condiciones_es',
        'terminos_condiciones_en',
        'terminos_condiciones_pt',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'colores' => 'array',
        ];
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'agencia_id');
    }

    public function terminosCondiciones(?string $idioma): ?string
    {
        $campo = match ($idioma) {
            'ingles' => 'terminos_condiciones_en',
            'portugues' => 'terminos_condiciones_pt',
            default => 'terminos_condiciones_es',
        };

        return $this->{$campo};
    }
}
