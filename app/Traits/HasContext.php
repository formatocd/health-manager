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
        // 1. ¿Hay alguien seleccionado en la sesión?
        $targetId = session('viewing_user_id');

        // 2. Si no hay nadie seleccionado, soy yo mismo.
        if (!$targetId) {
            return auth()->id();
        }

        // 3. SEGURIDAD: ¿Realmente tengo permiso para ver a esa persona?
        // (Evita que alguien inyecte un ID en la sesión manualmente)
        if (!auth()->user()->canView($targetId)) {
            // Si no tengo permiso, reseteamos a mí mismo por seguridad
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
