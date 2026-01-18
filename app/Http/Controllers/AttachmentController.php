<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function show(Attachment $attachment)
    {
        // 1. SEGURIDAD: Verificar que el archivo pertenece al usuario actual
        if ($attachment->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este archivo.');
        }

        // 2. Comprobar que el archivo físico existe en el disco privado ('local')
        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'El archivo no se encuentra.');
        }

        // 3. Servir el archivo para que el navegador lo muestre (inline)
        // Usamos el path absoluto del disco local
        return response()->file(Storage::disk('local')->path($attachment->file_path));
    }
}
