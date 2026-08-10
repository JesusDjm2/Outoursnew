$it = \App\Models\Itinerary::where('nombre', 'Itinerario Solo Destino PW')->first();
if ($it) {
    echo "id={$it->id} destino_id={$it->destino_id} categoria_id={$it->categoria_id} subcategoria_id={$it->subcategoria_id} costo={$it->costo}\n";
    echo "destino_nombre=" . ($it->destino->nombre ?? 'NULL') . "\n";
} else {
    echo "NOT FOUND\n";
}
