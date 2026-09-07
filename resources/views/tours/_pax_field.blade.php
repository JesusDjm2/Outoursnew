@php($extra = $extra ?? [])
@php($options = $options ?? [])
<div class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-1.5 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/30 dark:border-slate-700 dark:bg-slate-800">
    <span class="shrink-0 whitespace-nowrap text-xs text-gray-400 dark:text-slate-500">{{ $label }}</span>
    @if($type === 'select')
        <select name="{{ $name }}"
            @foreach($extra as $attr => $attrValue)
                {{ $attr }}="{{ $attrValue }}"
            @endforeach
            class="w-full min-w-0 bg-transparent text-sm text-gray-800 outline-none dark:text-slate-100 dark:[color-scheme:dark]">
            <option value="">Selecciona...</option>
            @foreach($options as $option)
                <option value="{{ $option }}" @selected($value === $option)>{{ $option }}</option>
            @endforeach
        </select>
    @else
        <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"
            @foreach($extra as $attr => $attrValue)
                {{ $attr }}="{{ $attrValue }}"
            @endforeach
            class="w-full min-w-0 bg-transparent text-sm text-gray-800 outline-none dark:text-slate-100">
    @endif
</div>
