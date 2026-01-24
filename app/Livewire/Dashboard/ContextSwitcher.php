<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;

class ContextSwitcher extends Component
{
    // Cambiar de contexto
    public function switchTo($userId)
    {
        if (empty($userId) || $userId == auth()->id()) {
            session()->forget('viewing_user_id');
        } else {
            if (auth()->user()->canView($userId)) {
                session(['viewing_user_id' => $userId]);
            }
        }

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        $currentId = session('viewing_user_id', auth()->id());
        $currentUser = User::find($currentId);

        $accessibleUsers = auth()->user()->accessibleUsers;

        return view('livewire.dashboard.context-switcher', [
            'currentUser' => $currentUser,
            'accessibleUsers' => $accessibleUsers
        ]);
    }
}
