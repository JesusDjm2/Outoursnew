@php($tabs = [
    'destinos' => ['route' => 'destinos.index', 'label' => 'Destinos'],
    'categorias' => ['route' => 'categorias.index', 'label' => 'Categorías'],
    'subcategorias' => ['route' => 'subcategorias.index', 'label' => 'Subcategorías'],
])
<div class="mb-6 flex gap-2 border-b border-gray-200 dark:border-slate-800">
    @foreach($tabs as $key => $tab)
        <a href="{{ route($tab['route']) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition {{ $active === $key ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
