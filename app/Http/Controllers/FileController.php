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

        if ($attachment->user_id !== $currentUser->id && !$currentUser->canView($attachment->user_id)) {
            abort(403, 'No tienes permiso para ver este archivo.');
        }

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->response($attachment->file_path);
    }
}
