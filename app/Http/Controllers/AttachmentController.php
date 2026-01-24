<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function show(Attachment $attachment)
    {
        if ($attachment->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este archivo.');
        }

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'El archivo no se encuentra.');
        }

        return response()->file(Storage::disk('local')->path($attachment->file_path));
    }
}
