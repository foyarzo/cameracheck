<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function submit(Request $request)
    {
        // Valida los datos
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photos' => 'required',
        ]);

        // Decodificar fotos en base64
        $photos = json_decode($request->input('photos'), true);
        foreach ($photos as $index => $photo) {
            $photo = base64_decode(str_replace('data:image/png;base64,', '', $photo));
            file_put_contents(public_path("uploads/photo_{$index}.png"), $photo);
        }

        return redirect()->back()->with('success', 'Formulario enviado correctamente.');
    }
}
