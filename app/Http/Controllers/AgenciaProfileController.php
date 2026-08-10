<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgenciaProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('agencia.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ruc' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'celulares' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'colores' => 'nullable|array|max:3',
            'colores.*' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'terminos_condiciones_es' => 'nullable|string',
            'terminos_condiciones_en' => 'nullable|string',
            'terminos_condiciones_pt' => 'nullable|string',
        ]);

        $data = [
            'name' => $validated['name'],
            'ruc' => $validated['ruc'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'celulares' => $validated['celulares'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'colores' => array_values(array_filter($validated['colores'] ?? [])),
            'terminos_condiciones_es' => $validated['terminos_condiciones_es'] ?? null,
            'terminos_condiciones_en' => $validated['terminos_condiciones_en'] ?? null,
            'terminos_condiciones_pt' => $validated['terminos_condiciones_pt'] ?? null,
        ];

        if ($request->hasFile('logo')) {
            if ($user->logo_path) {
                Storage::disk('public')->delete($user->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('users/logos', 'public');
        }

        $user->update($data);

        return redirect()->route('agencia.profile.edit')->with('success', 'Perfil de agencia actualizado.');
    }
}
