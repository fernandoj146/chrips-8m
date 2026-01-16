<?php

namespace App\Http\Controllers;

use App\Models\Meme;
use App\Models\User;
use Illuminate\Http\Request;

class MemeController extends Controller
{
    // TODO: Agregar trait AuthorizesRequests aquí (ver guía paso 7)

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
            'meme_url' => 'required|url|max:500',
            'explicacion' => 'required|string|max:1000',
        ], [
            'meme_url.required' => '¡Por favor, proporciona la URL del meme!',
            'meme_url.url' => 'La URL del meme no es válida.',
            'explicacion.required' => '¡Por favor, escribe una explicación para el meme!',
            'explicacion.max' => 'La explicación debe tener máximo 1000 caracteres.',
        ]);

        // TODO: Cambiar User::first() por auth()->user() (ver guía paso 6)
        User::first()->memes()->create([
            'meme_url' => $validated['meme_url'],
            'explicacion' => $validated['explicacion'],
            'fecha_subida' => now(),
        ]);

        return redirect('/')->with('success', '¡Tu meme ha sido publicado!');
    }

    public function edit(Meme $meme)
    {
        // TODO: Agregar autorización aquí (ver guía paso 7)
        // $this->authorize('update', $meme);

        return view('memes.edit', compact('meme'));
    }

    public function update(Request $request, Meme $meme)
    {
        // TODO: Agregar autorización aquí (ver guía paso 7)
        // $this->authorize('update', $meme);

        $validated = $request->validate([
            'meme_url' => 'required|url|max:500',
            'explicacion' => 'required|string|max:1000',
        ], [
            'meme_url.required' => '¡Por favor, proporciona la URL del meme!',
            'meme_url.url' => 'La URL del meme no es válida.',
            'explicacion.required' => '¡Por favor, escribe una explicación para el meme!',
            'explicacion.max' => 'La explicación debe tener máximo 1000 caracteres.',
        ]);

        // Update the meme
        $meme->update($validated);

        return redirect('/')->with('success', '¡Meme actualizado correctamente!');
    }

    public function destroy(Meme $meme)
    {
        // TODO: Agregar autorización aquí (ver guía paso 7)
        // $this->authorize('delete', $meme);

        $meme->delete();

        return redirect('/')->with('success', '¡Meme eliminado correctamente!');
    }
}
