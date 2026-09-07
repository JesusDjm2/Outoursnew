<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportToursUsdSeeder extends Seeder
{
    /**
     * Importa el catálogo `tours_usd` de baseprueba.sql como Actividades (itineraries).
     * Origen: id, tour, distr, preg (costo), ppromo (costo_promo).
     */
    public function run(): void
    {
        $rows = [
            [308, 'TE | City tour Cusco 1er turno', 'Pers.', 26.00, 13.00],
            [309, 'TE | City tour Cusco 2do turno', 'Pers.', 26.00, 13.00],
            [310, 'TE | City tour Cusco 3er turno', 'Pers.', 26.00, 13.00],
            [311, 'TE | Tour Valle sur', 'Pers.', 22.00, 19.00],
            [312, 'TE | Tour Maras moray', 'Pers.', 23.00, 20.00],
            [313, 'TE | Tour Valle sagrado + Almuerzo buffet mejorado', 'Pers.', 33.00, 29.00],
            [314, 'TE | Tour Valle sagrado CONEXIÓN + Almuerzo buffet mejorado', 'Pers.', 33.00, 29.00],
            [315, 'TE | Tour Valle sagrado + Almuerzo buffet', 'Pers.', 29.00, 25.00],
            [316, 'TE | Tour Valle sagrado CONEXIÓN + Almuerzo buffet', 'Pers.', 29.00, 25.00],
            [317, 'TE | Tour Valle sagrado con Maras moray', 'Pers.', 46.00, 41.00],
            [318, 'TE | Tour Valle sagrado con Maras moray CONEXIÓN', 'Pers.', 46.00, 41.00],
            [319, 'TE | Tour Humantay mejorado', 'Pers.', 39.00, 35.00],
            [320, 'TE | Tour Humantay Premium', 'Pers.', 54.00, 48.00],
            [321, 'TE | Tour  Vinicunca mejorado', 'Pers.', 42.00, 38.00],
            [322, 'TE | Tour  Vinicunca premium', 'Pers.', 57.00, 51.00],
            [323, 'TE | Tour Palccoyo', 'Pers.', 48.00, 43.00],
            [324, 'TE | Tour Queswachaca', 'Pers.', 45.00, 40.00],
            [325, 'TE | Tour Waqrapukara', 'Pers.', 48.00, 43.00],
            [326, 'TE | Tour 7 Lagunas Ausangate y baños termales', 'Pers.', 43.00, 39.00],
            [327, 'TE | Cuatrimotos Maras-moray (simple) 1er turno', 'Simple', 43.00, 39.00],
            [328, 'TE | Cuatrimotos Maras-moray (doble) 1er turno', 'Doble', 65.00, 59.00],
            [329, 'TE | Cuatrimotos Maras-laguna Huaypo (simple) 1er turno', 'Simple', 48.00, 43.00],
            [330, 'TE | Cuatrimotos Maras-laguna Huaypo (doble) 1er turno', 'Doble', 74.00, 67.00],
            [331, 'TE | Cuatrimotos Laguna Huaypo Piuray (simple) 1er turno', 'Simple', 48.00, 43.00],
            [332, 'TE | Cuatrimotos Laguna Huaypo Piuray (doble) 1er turno', 'Doble', 62.00, 56.00],
            [333, 'TE | Cuatrimotos Maras-moray (simple) 2do turno', 'Simple', 43.00, 39.00],
            [334, 'TE | Cuatrimotos Maras-moray (doble) 2do turno', 'Doble', 65.00, 59.00],
            [335, 'TE | Cuatrimotos Maras-laguna Huaypo (simple) 2do turno', 'Simple', 48.00, 43.00],
            [336, 'TE | Cuatrimotos Maras-laguna Huaypo (doble) 2do turno', 'Doble', 74.00, 67.00],
            [337, 'TE | Cuatrimotos Laguna Huaypo Piuray (simple) 2do turno', 'Simple', 48.00, 43.00],
            [338, 'TE | Cuatrimotos Laguna Huaypo Piuray (doble) 2do turno', 'Doble', 62.00, 56.00],
            [339, 'TE | Cuatrimotos Vinicunca (simple)', 'Simple', 99.00, 88.00],
            [340, 'TE | Cuatrimotos Vinicunca (doble)', 'Doble', 159.00, 139.00],
            [341, 'TE | Skybike medio', 'Pers.', 35.00, 31.00],
            [342, 'TE | Skybike extremo', 'Pers.', 40.00, 36.00],
            [343, 'TE | Traslado ingreso Cusco (max. 4 pax)', 'Auto priv.', 14.00, 14.00],
            [344, 'TE | Traslado salida Cusco (max. 4 pax)', 'Auto priv.', 14.00, 14.00],
            [345, 'TE | Traslado ingreso Cusco (max. 5 pax)', 'Auto priv.', 19.00, 19.00],
            [346, 'TE | Traslado salida Cusco (max. 5 pax)', 'Auto priv.', 19.00, 19.00],
            [347, 'TE | Traslado ingreso Cusco (max. 15 pax)', 'Auto priv.', 27.00, 27.00],
            [348, 'TE | Traslado salida Cusco (max. 15 pax)', 'Auto priv.', 27.00, 27.00],
            [349, 'TEP | Traslado ingreso Cusco (max. 4 pax)', 'Auto priv.', 12.00, 0.00],
            [350, 'TEP | Traslado salida Cusco (max. 4 pax)', 'Auto priv.', 12.00, 0.00],
            [351, 'TEP | Traslado ingreso Cusco (max. 5 pax)', 'Auto priv.', 14.00, 0.00],
            [352, 'TEP | Traslado salida Cusco (max. 5 pax)', 'Auto priv.', 14.00, 0.00],
            [353, 'TEP | Traslado ingreso Cusco (max. 15 pax)', 'Auto priv.', 22.00, 0.00],
            [354, 'TEP | Traslado salida Cusco (max. 15 pax)', 'Auto priv.', 22.00, 0.00],
            [355, 'TE | City tour Lima 1er turno', 'Pers.', 52.00, 26.00],
            [356, 'TE | City tour Lima 2do turno', 'Pers.', 52.00, 26.00],
            [357, 'TE | Ica, paracas, huacachina', 'Pers.', 72.00, 64.00],
            [358, 'TE | Ica, paracas, huacachina (Solo español)', 'Pers.', 60.00, 54.00],
            [359, 'TE | Ruta del Sol', 'Pers.', 75.00, 75.00],
            [360, 'TE | TE | Uros y Taquile', 'Pers.', 45.00, 39.00],
            [361, 'TE | Bus Cusco - Puno', 'Pers.', 27.00, 24.00],
            [362, 'TE | Bus Puno - Cusco', 'Pers.', 27.00, 24.00],
            [363, 'TE | Paracas Nazca 2 Días', 'Pers.', 390.00, 369.00],
            [364, 'TE | Cusco místico', 'Pers.', 28.00, 27.00],
            [365, 'TE | Traslado ingreso Lima (max. 2 pax)', 'Mov priv.', 27.00, 27.00],
            [366, 'TE | Traslado salida Lima (max. 2 pax)', 'Mov priv.', 27.00, 27.00],
            [367, 'TE | Traslado ingreso Lima (max. 4 pax)', 'Mov priv.', 31.00, 31.00],
            [368, 'TE | Traslado salida Lima (max. 4 pax)', 'Mov priv.', 31.00, 31.00],
            [369, 'TE | Traslado ingreso Lima (max. 9 pax)', 'Mov priv.', 45.00, 45.00],
            [370, 'TE | Traslado salida Lima (max. 9 pax)', 'Mov priv.', 45.00, 45.00],
            [371, 'TE | Traslado ingreso Lima (max. 15 pax)', 'Mov priv.', 98.00, 98.00],
            [372, 'TE | Traslado salida Lima (max. 15 pax)', 'Mov priv.', 98.00, 98.00],
            [373, 'TE | Traslado ingreso Lima (max. 18 pax)', 'Mov priv.', 112.00, 112.00],
            [374, 'TE | Traslado salida Lima (max. 18 pax)', 'Mov priv.', 112.00, 112.00],
            [375, 'TETT | Machu Picchu full day (tren turístico) Adulto Extranjero', 'Pers.', 296.00, 267.00],
            [376, 'TETT | Machu Picchu full day (tren turístico) 12-17 años Extranjero', 'Pers.', 270.00, 243.00],
            [377, 'TETT | CONEXIÓN Machu Picchu full day (tren turístico) Adulto Extranjero', 'Pers.', 310.00, 279.00],
            [378, 'TETT | CONEXIÓN Machu Picchu full day (tren turístico) 12-17 años Extranjero', 'Pers.', 283.00, 255.00],
            [379, 'TETT | Machu Picchu 2D/1N (tren turístico) Adulto Extranjero', 'Pers.', 316.00, 285.00],
            [380, 'TETT | Machu Picchu 2D/1N (tren turístico) 12-17 años Extranjero', 'Pers.', 289.00, 260.00],
            [381, 'TENT | Machu Picchu full day (No incluye tren) Adulto Extranjero', 'Pers.', 147.00, 132.00],
            [382, 'TENT | Machu Picchu full day (No incluye tren) 12-17 años Extranjero', 'Pers.', 120.00, 108.00],
            [383, 'TENT | Machu Picchu full day (No incluye tren) 3-11 años Extranjero', 'Pers.', 104.00, 94.00],
            [384, 'TENT | CONEXIÓN Machu Picchu full day (No incluye tren) Adulto Extranjero', 'Pers.', 160.00, 144.00],
            [385, 'TENT | CONEXIÓN Machu Picchu full day (No incluye tren) 12-17 años Extranjero', 'Pers.', 133.00, 120.00],
            [386, 'TENT | CONEXIÓN Machu Picchu full day (No incluye tren) 3-11 años Extranjero', 'Pers.', 118.00, 106.00],
            [387, 'TENT | Machu Picchu 2D/1N (No incluye tren) Adulto Extranjero', 'Pers.', 167.00, 150.00],
            [388, 'TENT | Machu Picchu 2D/1N (No incluye tren) 12-17 años Extranjero', 'Pers.', 139.00, 125.00],
            [389, 'TENT | Machu Picchu 2D/1N (No incluye tren) 3-11 años Extranjero', 'Pers.', 123.00, 111.00],
            [390, 'TE | Machu Picchu (by car + consettur) Adulto Extranjero', 'Pers.', 189.00, 170.00],
            [391, 'TE | Machu Picchu (by car + consettur) Adulto peruano', 'Pers.', 149.00, 134.00],
            [392, 'TE | Machu Picchu (by car + consettur) 3-17 años peruanos', 'Pers.', 129.00, 116.00],
            [393, 'TE | Ingreso Machupicchu Adulto extranjero', 'Pers.', 46.00, 46.00],
            [394, 'TE | Ingreso Machupicchu 3-17 años extranjero', 'Pers.', 22.00, 22.00],
            [395, 'TE | Ingreso Waynapicchu  Adulto extranjero', 'Pers.', 61.00, 61.00],
            [396, 'TE | Ingreso Waynapicchu 3-17 años extranjero', 'Pers.', 36.00, 36.00],
            [397, 'TED | Descuento Ingreso Machupicchu Adulto extranjero', 'Pers.', -45.00, -45.00],
            [398, 'TED | Descuento Ingreso Machupicchu 3-17 años extranjero', 'Pers.', -20.00, -20.00],
            [399, 'TE | Guiado en Wayna Picchu privado (max 6 pax)', 'Priv.', 89.00, 89.00],
            [400, 'TE | Guiado en Machu Picchu compartido', 'Pers.', 12.00, 12.00],
            [401, 'TE | Adicional guiado en Machu Picchu privado (2 pers.)', 'Priv.', 43.00, 43.00],
            [402, 'TE | Adicional guiado en Machu Picchu privado (3 pers.)', 'Priv.', 37.00, 37.00],
            [403, 'TE | Adicional guiado en Machu Picchu privado (4 pers.)', 'Priv.', 31.00, 31.00],
            [404, 'TE | Adicional guiado en Machu Picchu privado (5 pers.)', 'Priv.', 25.00, 25.00],
            [405, 'TE | Adicional guiado en Machu Picchu privado (6 pers.)', 'Priv.', 19.00, 19.00],
            [406, 'TE | Boleto Turístico General Adulto extranjero', 'Pers.', 39.00, 39.00],
            [407, 'TE | Boleto Turístico General 10-17 años extranjero', 'Pers.', 21.00, 21.00],
            [408, 'TE | Boleto Turístico Parcial Adulto extranjero', 'Pers.', 21.00, 21.00],
            [409, 'TN | Boleto Turístico General Adulto peruano', 'Pers.', 21.00, 21.00],
            [410, 'TN | Boleto Turístico general 10 - 17 años peruano', 'Pers.', 12.00, 12.00],
            [411, 'TN | Boleto Turístico Parcial Adulto peruano', 'Pers.', 12.00, 12.00],
        ];

        $now = now();
        $inserts = [];

        foreach ($rows as [$id, $tour, $distr, $preg, $ppromo]) {
            $nombre = trim($tour);
            if ($distr) {
                $nombre .= ' — ' . trim($distr);
            }

            $inserts[] = [
                'nombre' => $nombre,
                'costo' => $preg,
                'costo_promo' => $ppromo,
                'destino_id' => null,
                'categoria_id' => null,
                'descripcion' => null,
                'incluye' => null,
                'no_incluye' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($inserts, 50) as $chunk) {
            DB::table('itineraries')->insert($chunk);
        }

        $this->command?->info(count($inserts) . ' actividades importadas desde tours_usd.');
    }
}
