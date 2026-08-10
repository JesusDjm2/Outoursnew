<?php

namespace Database\Seeders;

use App\Models\TipoProveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            'Transporte',
            'Restaurante',
            'Guía turístico',
            'Hospedaje',
            'Entradas / Boletos',
            'Seguros',
            'Otro',
        ];

        foreach ($tipos as $nombre) {
            TipoProveedor::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
