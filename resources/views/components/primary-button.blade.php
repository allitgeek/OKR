<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-navy-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-navy-500 focus:bg-navy-700 active:bg-navy-800 focus:outline-none focus:ring-2 focus:ring-navy-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md']) }}>
    {{ $slot }}
</button>
