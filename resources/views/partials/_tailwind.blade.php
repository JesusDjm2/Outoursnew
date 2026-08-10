<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
                },
                colors: {
                    brand: '#e80c13',
                    'brand-dark': '#b8090f',
                },
            },
        },
    };
</script>
<style type="text/tailwindcss">
@layer components {
    /* Tom Select — modo oscuro */
    .dark .ts-control {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }
    .dark .ts-control input {
        color: #f1f5f9 !important;
    }
    .dark .ts-control input::placeholder {
        color: #64748b !important;
    }
    .dark .ts-wrapper.single .ts-control {
        background-color: #1e293b !important;
    }
    .dark .ts-control .item,
    .dark .ts-wrapper.multi .ts-control > div {
        background-color: #334155 !important;
        color: #f1f5f9 !important;
        border-color: #475569 !important;
    }
    .dark .ts-wrapper.multi .ts-control > div.active {
        background-color: #475569 !important;
        color: #f1f5f9 !important;
        border-color: #64748b !important;
    }
    .dark .item .remove {
        border-left-color: #475569 !important;
        border-right-color: #475569 !important;
    }
    .dark .ts-dropdown {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }
    .dark .ts-dropdown .option {
        color: #f1f5f9 !important;
    }
    .dark .ts-dropdown .active {
        background-color: #334155 !important;
        color: #f1f5f9 !important;
    }
    .dark .ts-dropdown .no-results {
        color: #94a3b8 !important;
    }

    /* Quill — modo oscuro */
    .dark .ql-toolbar.ql-snow {
        background-color: #1e293b;
        border-color: #334155;
    }
    .dark .ql-container.ql-snow {
        background-color: #1e293b;
        border-color: #334155;
        color: #f1f5f9;
    }
    .dark .ql-editor.ql-blank::before {
        color: #64748b;
    }
    .dark .ql-snow .ql-stroke {
        stroke: #cbd5e1;
    }
    .dark .ql-snow .ql-fill {
        fill: #cbd5e1;
    }
    .dark .ql-snow .ql-picker {
        color: #cbd5e1;
    }
    .dark .ql-snow .ql-picker-options {
        background-color: #1e293b;
        border-color: #334155;
    }
}
</style>
