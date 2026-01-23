<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function show($id)
    {
        $attachment = Attachment::findOrFail($id);
        $currentUser = auth()->user();

        // REGLA DE SEGURIDAD ACTUALIZADA:
        // 1. Es mío?
        // 2. O... ¿El dueño del archivo me ha dado permiso para verlo?
        if ($attachment->user_id !== $currentUser->id && !$currentUser->canView($attachment->user_id)) {
            abort(403, 'No tienes permiso para ver este archivo.');
        }

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->response($attachment->file_path);
    }
}
