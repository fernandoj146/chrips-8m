<?php

namespace App\Http\Controllers;

use App\Models\Meme;
use Illuminate\Http\Request;

class MemeController extends Controller
{
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

        // Create the meme (no user for now - we'll add auth later)
        Meme::create([
            'meme_url' => $validated['meme_url'],
            'explicacion' => $validated['explicacion'],
            'user_id' => null, // We'll add authentication later
            'fecha_subida' => now(),
        ]);

        // Redirect back to the feed
        return redirect('/')->with('success', '¡Tu meme ha sido publicado!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meme $meme)
    {
        // We'll add authorization later
        return view('memes.edit', compact('meme'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meme $meme)
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

        // Update the meme
        $meme->update($validated);

        return redirect('/')->with('success', '¡Meme actualizado correctamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meme $meme)
    {
        $meme->delete();

        return redirect('/')->with('success', '¡Meme eliminado correctamente!');
    }
}
