<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ShareData extends Component
{
    public $email = '';

    public function addViewer()
    {
        // 1. Validar que es un email y existe en la BD
        $this->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // 2. Buscar al usuario
        $viewer = User::where('email', $this->email)->first();

        // 3. Validaciones lógicas
        if ($viewer->id === auth()->id()) {
            throw ValidationException::withMessages(['email' => 'No puedes compartirte datos a ti mismo.']);
        }

        // Comprobar si ya tiene permiso
        if (auth()->user()->allowedViewers()->where('viewer_id', $viewer->id)->exists()) {
            throw ValidationException::withMessages(['email' => 'Este usuario ya tiene acceso a tus datos.']);
        }

        // 4. Crear el permiso (Attach)
        auth()->user()->allowedViewers()->attach($viewer->id);

        // 5. Limpiar y avisar
        $this->email = '';
        session()->flash('status', "Ahora {$viewer->name} puede ver tus datos.");
    }

    public function removeViewer($id)
    {
        // Revocar permiso (Detach)
        auth()->user()->allowedViewers()->detach($id);
    }

    public function render()
    {
        return view('livewire.profile.share-data', [
            'viewers' => auth()->user()->allowedViewers
        ]);
    }
}
