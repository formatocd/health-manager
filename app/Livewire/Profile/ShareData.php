<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ShareData extends Component
{
    public $username = ''; // CAMBIO: Usamos username en vez de email para el input

    public $search = '';
    public $searchResults = [];

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        // BÚSQUEDA SEGURA: Solo por username (Nick)
        // Opcional: permitir buscar por nombre real ('name') si quieres,
        // pero NUNCA por email.
        $this->searchResults = User::where('id', '!=', auth()->id())
            ->where('username', 'like', '%' . $this->search . '%')
            ->take(5)
            ->get();
    }

    public function selectUser($username)
    {
        $this->username = $username;
        $this->search = '';
        $this->searchResults = [];
    }

    public function addViewer()
    {
        $this->validate([
            'username' => 'required|exists:users,username'
        ], [
            'username.exists' => 'No encontramos ningún usuario con ese Nick.',
        ]);

        $viewer = User::where('username', $this->username)->first();

        if ($viewer->id === auth()->id()) {
            throw ValidationException::withMessages(['username' => 'No puedes compartirte datos a ti mismo.']);
        }

        if (auth()->user()->allowedViewers()->where('viewer_id', $viewer->id)->exists()) {
            throw ValidationException::withMessages(['username' => 'Este usuario ya tiene acceso a tus datos.']);
        }

        auth()->user()->allowedViewers()->attach($viewer->id);

        $this->username = '';
        session()->flash('status', "Acceso concedido a @{$viewer->username}.");
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
