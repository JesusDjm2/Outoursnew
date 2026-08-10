$items = \App\Models\Itinerary::all();
foreach ($items as $it) {
    echo "id={$it->id} nombre={$it->nombre} costo={$it->costo}\n";
}
echo "TOTAL=" . $items->count() . "\n";
