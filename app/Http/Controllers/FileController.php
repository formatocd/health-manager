<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function show($id)
    {
        // 1. Buscar el adjunto
        $attachment = Attachment::findOrFail($id);

        // 2. SEGURIDAD: Verificar que el archivo pertenece al usuario actual
        if ($attachment->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este archivo.');
        }

        // 3. Verificar que existe en el disco
        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404);
        }

        // 4. Servir el archivo (El navegador sabrá si mostrarlo o descargarlo según el tipo)
        return Storage::disk('local')->response($attachment->file_path);
    }
}
