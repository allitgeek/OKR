<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-navy-300 rounded-md font-semibold text-sm text-navy-700 uppercase tracking-widest shadow-sm hover:bg-navy-50 focus:outline-none focus:ring-2 focus:ring-navy-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
