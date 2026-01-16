<x-layout>
    <x-slot:title>
        Home Feed
    </x-slot:title>

    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-3xl font-bold mt-8">Últimos Memes</h1>

        <!-- Meme Form -->
        <div class="bg-white shadow rounded-lg p-6 mt-8">
            <form method="POST" action="/memes">
                @csrf
                <div class="mb-4">
                    <label for="meme_url" class="block text-sm font-medium text-gray-700 mb-2">URL del Meme</label>
                    <input
                        type="url"
                        name="meme_url"
                        id="meme_url"
                        placeholder="https://ejemplo.com/meme.jpg"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('meme_url') border-red-500 @enderror"
                        value="{{ old('meme_url') }}"
                        required
                    />
                    @error('meme_url')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="explicacion" class="block text-sm font-medium text-gray-700 mb-2">Explicación</label>
                    <textarea
                        name="explicacion"
                        id="explicacion"
                        placeholder="Explica por qué este meme es relevante para el 8M..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('explicacion') border-red-500 @enderror"
                        rows="4"
                        maxlength="1000"
                        required
                    >{{ old('explicacion') }}</textarea>
                    @error('explicacion')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow">
                        Publicar Meme
                    </button>
                </div>
            </form>
        </div>

        <!-- Feed -->
        <div class="space-y-8 mt-8 flex flex-col items-center">
            @forelse ($memes as $meme)
                <x-meme :meme="$meme" />
            @empty
                <div class="hero py-12">
                    <div class="hero-content text-center">
                        <div>
                            <svg class="mx-auto h-12 w-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                            <p class="mt-4 text-base-content/60">¡No hay memes todavía! Sé el primero en subir uno.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>
