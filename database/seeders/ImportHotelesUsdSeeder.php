<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportHotelesUsdSeeder extends Seeder
{
    /**
     * Importa el catálogo `hoteles_usd` de baseprueba.sql como Hoteles + Habitaciones.
     * Origen: id, aloj ("CODIGO | Ciudad - Nombre (Tipo)" o "... - Tipo"), distr, preg, ppromo.
     * numero_habitacion no viene en el origen (es un catálogo de tipos, no habitaciones físicas),
     * se deja como "S/N" para que el equipo lo complete si corresponde.
     */
    public function run(): void
    {
        $rows = [
            [145, 'TE | Cusco - Casa conquista (Simple)', 38.00, 29.00],
            [146, 'TE | Cusco - Casa conquista (Doble)', 45.00, 39.00],
            [147, 'TE | Cusco - Casa conquista (Matrimonial)', 45.00, 39.00],
            [148, 'TE | Cusco - Casa conquista (Matrimonial + adicional)', 67.00, 58.00],
            [149, 'TE | Cusco - Casa conquista (Triple)', 67.00, 58.00],
            [150, 'TE | Cusco - Casa conquista (Cuadruple)', 75.00, 65.00],
            [151, 'TEA | Cusco - Hotel Boutique Ankawa (Simple)', 51.00, 46.00],
            [152, 'TEA | Cusco - Hotel Boutique Ankawa (Matrimonial)', 66.00, 59.00],
            [153, 'TEA | Cusco - Hotel Boutique Ankawa (Doble)', 66.00, 59.00],
            [154, 'TEA | Cusco - Hotel Boutique Ankawa (Triple)', 88.00, 78.00],
            [155, 'TEA | Cusco - Hotel Boutique Ankawa (Cuadruple)', 105.00, 95.00],
            [156, "TE | Cusco - Hostal Puma walker's - Individual", 34.00, 29.00],
            [157, "TE | Cusco - Hostal Puma walker's - Matrimonial", 41.00, 35.00],
            [158, "TE | Cusco - Hostal Puma walker's - Doble", 41.00, 35.00],
            [159, "TE | Cusco - Hostal Puma walker's - Matrimonial + adicional", 52.00, 45.00],
            [160, "TE | Cusco - Hostal Puma walker's - Triple", 60.00, 52.00],
            [161, "TE | Cusco - Hostal Puma walker's - Cuadruple", 72.00, 62.00],
            [162, 'TEA | Cusco - Hotel Casa Montes Central ** - Simple', 60.00, 52.00],
            [163, 'TEA | Cusco - Hotel Casa Montes Central ** - Doble', 81.00, 70.00],
            [164, 'TEA | Cusco - Hotel Casa Montes Central ** - Matrimonial Queen Size', 81.00, 70.00],
            [165, 'TEA | Cusco - Hotel Casa Montes Central ** - Matrimonial King Size', 86.00, 74.00],
            [166, 'TEA | Cusco - Hotel Casa Montes Central ** - Matrinonial King + balcon', 94.00, 81.00],
            [167, 'TEA | Cusco - Hotel Casa Montes Central ** - Triple', 99.00, 85.00],
            [168, 'TEA | Cusco - Hotel Casa Montes San Blas ** - Simple', 63.00, 54.00],
            [169, 'TEA | Cusco - Hotel Casa Montes San Blas ** - Doble', 84.00, 72.00],
            [170, 'TEA | Cusco - Hotel Casa Montes San Blas ** - Matrimonial 2 plazas', 77.00, 66.00],
            [171, 'TEA | Cusco - Hotel Casa Montes San Blas ** - Matrimonial Queen Size', 85.00, 73.00],
            [172, 'TEA | Cusco - Hotel Casa Montes San Blas ** - Matrimonial King Size', 92.00, 79.00],
            [173, 'TEA | Cusco - Hotel Casa Montes San Blas ** - Matrinonial King + balcon', 97.00, 84.00],
            [174, 'TEA | Cusco - Hotel Casa Montes San Blas ** - Triple', 101.00, 87.00],
            [175, 'TE | Lima - Hotel Arawi *** (individual)', 73.00, 63.00],
            [176, 'TE  | Lima - Hotel Arawi *** (matrimonial)', 81.00, 70.00],
            [177, 'TE  | Lima - Hotel Arawi *** (doble)', 81.00, 70.00],
            [178, 'TE  | Lima - Hotel Arawi *** (triple)', 114.00, 98.00],
            [179, 'TE  | Lima - Hotel Arawi *** (adicional)', 34.00, 29.00],
            [180, 'TE  | Lima - Hostal Carlos Tenaud  **  (matrimonial)', 41.00, 35.00],
            [181, 'TE  | Lima - Hostal Carlos Tenaud  ** (matrimonial ejecutiva)', 45.00, 39.00],
            [182, 'TE  | Lima - Hostal Carlos Tenaud  ** (matrimonial suite)', 50.00, 43.00],
            [183, 'TE  | Lima - Hostal Carlos Tenaud  **  (doble matrimonial)', 64.00, 55.00],
            [184, 'TE | Puno - Hostal San Antonio (Simple)', 27.00, 23.00],
            [185, 'TE | Puno - Hostal San Antonio (Doble)', 37.00, 32.00],
            [186, 'TE | Puno - Hostal San Antonio (Matrimonial)', 37.00, 32.00],
            [187, 'TE | Puno - Hostal San Antonio (Triple)', 52.00, 45.00],
            [188, 'TE | Puno - Hotel Sol plaza ***  (Individual)', 50.00, 43.00],
            [189, 'TE | Puno - Hotel Sol plaza ***  (Doble)', 67.00, 58.00],
            [190, 'TE | Puno - Hotel Sol plaza ***  (Matrimonio)', 67.00, 58.00],
            [191, 'TE | Puno - Hotel Sol plaza ***  (Matrimonial Suite)', 122.00, 105.00],
            [192, 'TE | Puno - Hotel Sol plaza ***  (Triple)', 102.00, 88.00],
        ];

        $now = now();
        $hotelesPorClave = [];

        foreach ($rows as [$id, $aloj, $preg, $ppromo]) {
            [$codigo, $resto] = array_pad(explode('|', $aloj, 2), 2, '');
            $codigo = trim($codigo);
            $resto = preg_replace('/\s+/', ' ', trim($resto));

            if (preg_match('/^(.*)\(([^)]+)\)$/', $resto, $m)) {
                $nombreHotel = trim($m[1]);
                $tipoHabitacion = trim($m[2]);
            } else {
                $pos = strrpos($resto, ' - ');
                $nombreHotel = $pos !== false ? trim(substr($resto, 0, $pos)) : $resto;
                $tipoHabitacion = $pos !== false ? trim(substr($resto, $pos + 3)) : null;
            }

            $destinoNombre = null;
            if (preg_match('/^(Cusco|Lima|Puno)\s*-\s*(.+)$/iu', $nombreHotel, $cm)) {
                $destinoNombre = $cm[1];
                $nombreHotel = trim($cm[2]);
            }

            $clave = $codigo . '|' . $destinoNombre . '|' . $nombreHotel;
            if (!isset($hotelesPorClave[$clave])) {
                $hotelesPorClave[$clave] = [
                    'codigo' => $codigo,
                    'destino_nombre' => $destinoNombre,
                    'nombre' => $nombreHotel,
                    'habitaciones' => [],
                ];
            }

            $hotelesPorClave[$clave]['habitaciones'][] = [
                'nombre' => $tipoHabitacion ?? 'Estándar',
                'cantidad_personas' => $this->inferirCantidadPersonas($tipoHabitacion ?? ''),
                'numero_habitacion' => 'S/N',
                'precio_regular' => $preg,
                'precio_promo' => $ppromo,
            ];
        }

        $destinoIds = [];
        foreach (array_unique(array_column($hotelesPorClave, 'destino_nombre')) as $destinoNombre) {
            if (!$destinoNombre) {
                continue;
            }
            $destino = DB::table('destinos')->whereRaw('LOWER(nombre) = ?', [mb_strtolower($destinoNombre)])->first();
            if (!$destino) {
                $id = DB::table('destinos')->insertGetId([
                    'nombre' => $destinoNombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $destinoIds[$destinoNombre] = $id;
            } else {
                $destinoIds[$destinoNombre] = $destino->id;
            }
        }

        $totalHoteles = 0;
        $totalHabitaciones = 0;

        foreach ($hotelesPorClave as $hotel) {
            $hotelId = DB::table('hotels')->insertGetId([
                'destino_id' => $hotel['destino_nombre'] ? $destinoIds[$hotel['destino_nombre']] : null,
                'codigo' => $hotel['codigo'],
                'nombre' => $hotel['nombre'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $totalHoteles++;

            $habitaciones = array_map(function ($hab) use ($hotelId, $now) {
                return array_merge($hab, [
                    'hotel_id' => $hotelId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }, $hotel['habitaciones']);

            DB::table('rooms')->insert($habitaciones);
            $totalHabitaciones += count($habitaciones);
        }

        $this->command?->info("{$totalHoteles} hoteles y {$totalHabitaciones} tipos de habitación importados desde hoteles_usd.");
    }

    private function inferirCantidadPersonas(string $tipo): int
    {
        $t = mb_strtolower($tipo);
        $base = 2;

        if (str_contains($t, 'cuadruple') || str_contains($t, 'cuádruple')) {
            $base = 4;
        } elseif (str_contains($t, 'triple')) {
            $base = 3;
        } elseif (str_contains($t, 'simple') || str_contains($t, 'individual')) {
            $base = 1;
        } elseif (str_contains($t, 'matri') || str_contains($t, 'doble')) {
            $base = 2;
        }

        if (str_contains($t, 'adicional')) {
            $base += 1;
        }

        return $base;
    }
}
