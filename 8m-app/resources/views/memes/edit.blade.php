<x-layout>
    <x-slot:title>
        Editar Meme
    </x-slot:title>

    <div class="max-w-2xl mx-auto px-4">
        <h1 class="text-3xl font-bold mt-8">Editar Meme</h1>

        <div class="bg-white shadow rounded-lg p-6 mt-8">
            <form method="POST" action="/memes/{{ $meme->id }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="meme_url" class="block text-sm font-medium text-gray-700 mb-2">URL del Meme</label>
                    <input
                        type="url"
                        name="meme_url"
                        id="meme_url"
                        placeholder="https://ejemplo.com/meme.jpg"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('meme_url') border-red-500 @enderror"
                        value="{{ old('meme_url', $meme->meme_url) }}"
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
                    >{{ old('explicacion', $meme->explicacion) }}</textarea>
                    @error('explicacion')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between">
                    <a href="/" class="bg-gray-400 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg shadow">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow">
                        Actualizar Meme
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
