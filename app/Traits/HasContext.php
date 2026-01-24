<?php

namespace App\Traits;

use App\Models\User;

trait HasContext
{
    /**
     * Devuelve el ID del usuario que debemos consultar.
     * Puede ser el mío (auth) o el de un paciente seleccionado en sesión.
     */
    public function getTargetUserId()
    {
        $targetId = session('viewing_user_id');

        if (!$targetId) {
            return auth()->id();
        }

        if (!auth()->user()->canView($targetId)) {
            session()->forget('viewing_user_id');
            return auth()->id();
        }

        return $targetId;
    }

    /**
     * Devuelve el MODELO completo del usuario actual (para pintar su nombre, avatar, etc).
     */
    public function getTargetUserProperty()
    {
        return User::find($this->getTargetUserId());
    }

    /**
     * ¿Estoy en modo "Solo Lectura"?
     * True si estoy viendo a otro. False si soy yo.
     */
    public function getIsReadOnlyProperty()
    {
        return $this->getTargetUserId() !== auth()->id();
    }
}
