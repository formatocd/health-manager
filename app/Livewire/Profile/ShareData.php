<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ShareData extends Component
{
    public $email = '';

    // Variables para el buscador predictivo
    public $search = '';
    public $searchResults = [];

    // Se ejecuta automáticamente cuando escribes en el input wire:model.live="search"
    public function updatedSearch()
    {
        // Si hay menos de 2 letras, limpiamos y no buscamos
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        // Buscamos usuarios que coincidan por nombre o email, excluyéndome a mí mismo
        $this->searchResults = User::where('id', '!=', auth()->id())
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->take(5) // Máximo 5 resultados
            ->get();
    }

    // Al hacer clic en un resultado de la lista
    public function selectUser($email)
    {
        $this->email = $email;      // Rellenamos el campo email
        $this->search = '';         // Limpiamos el texto del buscador
        $this->searchResults = [];  // Ocultamos la lista
    }

    public function addViewer()
    {
        // Validaciones con mensajes en español
        $this->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'El campo de correo es obligatorio.',
            'email.email' => 'Introduce un correo válido.',
            'email.exists' => 'No encontramos ningún usuario con ese correo.',
        ]);

        $viewer = User::where('email', $this->email)->first();

        // Evitar compartir con uno mismo
        if ($viewer->id === auth()->id()) {
            throw ValidationException::withMessages(['email' => 'No puedes compartirte datos a ti mismo.']);
        }

        // Evitar duplicados
        if (auth()->user()->allowedViewers()->where('viewer_id', $viewer->id)->exists()) {
            throw ValidationException::withMessages(['email' => 'Este usuario ya tiene acceso a tus datos.']);
        }

        // Guardar permiso
        auth()->user()->allowedViewers()->attach($viewer->id);

        // Limpiar formulario y avisar
        $this->email = '';
        session()->flash('status', "Acceso concedido a {$viewer->name}.");
    }

    public function removeViewer($id)
    {
        auth()->user()->allowedViewers()->detach($id);
    }

    public function render()
    {
        return view('livewire.profile.share-data', [
            'viewers' => auth()->user()->allowedViewers
        ]);
    }
}
