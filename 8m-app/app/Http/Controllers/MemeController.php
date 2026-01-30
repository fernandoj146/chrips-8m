<?php

namespace App\Http\Controllers;

use App\Models\Meme;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MemeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $memes = Meme::with('user')
            ->latest('fecha_subida')
            ->take(50)
            ->get();

        return view('feed', ['memes' => $memes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'meme_texto' => 'required|string|max:1000',
            'explicacion' => 'required|string|max:1000',
        ], [
            'meme_texto.required' => '¡Por favor, escribe el texto del meme!',
            'meme_texto.max' => 'El texto del meme debe tener máximo 1000 caracteres.',
            'explicacion.required' => '¡Por favor, escribe una explicación para el meme!',
            'explicacion.max' => 'La explicación debe tener máximo 1000 caracteres.',
        ]);

        auth()->user()->memes()->create([
            'meme_texto' => $validated['meme_texto'],
            'explicacion' => $validated['explicacion'],
            'fecha_subida' => now(),
        ]);

        return redirect('/')->with('success', '¡Tu meme ha sido publicado!');
    }

    public function edit(Meme $meme)
    {
        $this->authorize('update', $meme);

        return view('memes.edit', compact('meme'));
    }

    public function update(Request $request, Meme $meme)
    {
        $this->authorize('update', $meme);

        $validated = $request->validate([
            'meme_texto' => 'required|string|max:1000',
            'explicacion' => 'required|string|max:1000',
        ], [
            'meme_texto.required' => '¡Por favor, escribe el texto del meme!',
            'meme_texto.max' => 'El texto del meme debe tener máximo 1000 caracteres.',
            'explicacion.required' => '¡Por favor, escribe una explicación para el meme!',
            'explicacion.max' => 'La explicación debe tener máximo 1000 caracteres.',
        ]);

        // Update the meme
        $meme->update($validated);

        return redirect('/')->with('success', '¡Meme actualizado correctamente!');
    }

    public function destroy(Meme $meme)
    {
        $this->authorize('delete', $meme);

        $meme->delete();

        return redirect('/')->with('success', '¡Meme eliminado correctamente!');
    }
}
