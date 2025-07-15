<x-app-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-700 mb-4">
                    {{ __('Oops! Something went wrong.') }}
                </h2>
                <p class="text-gray-600 mb-6">
                    {{ __('We encountered an error while processing your request.') }}
                </p>
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Return Home') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout> 