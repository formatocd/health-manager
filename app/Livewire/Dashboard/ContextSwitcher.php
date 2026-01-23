<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;

class ContextSwitcher extends Component
{
    // Cambiar de contexto
    public function switchTo($userId)
    {
        // Si el ID es vacío o es el mío, borramos la sesión (volvemos a "Mi Perfil")
        if (empty($userId) || $userId == auth()->id()) {
            session()->forget('viewing_user_id');
        } else {
            // Verificar seguridad antes de cambiar
            if (auth()->user()->canView($userId)) {
                session(['viewing_user_id' => $userId]);
            }
        }

        // Recargamos la página para que todos los componentes se actualicen
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        // Recuperamos el usuario que estamos viendo actualmente
        $currentId = session('viewing_user_id', auth()->id());
        $currentUser = User::find($currentId);

        // Recuperamos la lista de gente que me ha dado permiso
        $accessibleUsers = auth()->user()->accessibleUsers;

        return view('livewire.dashboard.context-switcher', [
            'currentUser' => $currentUser,
            'accessibleUsers' => $accessibleUsers
        ]);
    }
}
