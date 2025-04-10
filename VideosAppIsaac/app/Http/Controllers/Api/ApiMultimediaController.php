<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\Multimedia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiMultimediaController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $files = Multimedia::all();

        // Afegim URL completa
        $files->each(function ($file) {
            $file->file_path = asset($file->file_path);
        });

        return response()->json($files);
    }

    public function myFiles()
    {
        $files = Auth::user()->multimedia()->get();

        // Afegim URL completa també per a la vista dels arxius propis
        $files->each(function ($file) {
            $file->file_path = asset($file->file_path);
        });

        return response()->json($files);
    }

    public function store(Request $request)
    {
        try {
            Log::info('🔍 Dades rebudes per a pujar:', [
                'title' => $request->input('title'),
                'type' => $request->input('type'),
                'file_info' => $request->file('file'),
                '_FILES' => $_FILES
            ]);

            if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
                Log::warning('❌ El fitxer no s\'ha rebut o no és vàlid.');
                return response()->json(['message' => 'Fitxer invàlid o no rebut'], 422);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'type'  => 'required|in:video,image',
                'file'  => 'required|file|mimetypes:video/mp4,image/jpeg,image/png|max:51200', // 50MB
            ]);

            $path = $request->file('file')->store('multimedia', 'public');

            $multimedia = Multimedia::create([
                'title'     => $request->input('title'),
                'type'      => $request->input('type'),
                'file_path' => '/storage/' . $path,
                'user_id'   => Auth::id(),
            ]);

            // 🪄 Actualitzem el file_path amb URL absoluta abans d'enviar-lo
            $multimedia->file_path = asset($multimedia->file_path);

            Log::info('✅ Fitxer pujat correctament: ' . $path);

            return response()->json($multimedia, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('⚠️ Error de validació:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('❌ Error pujant fitxer: ' . $e->getMessage());
            return response()->json(['message' => 'Error al pujar el fitxer'], 500);
        }
    }

    public function show($id)
    {
        $file = Multimedia::findOrFail($id);
        $file->file_path = asset($file->file_path);
        return response()->json($file);
    }

    public function update(Request $request, $id)
    {
        $file = Multimedia::findOrFail($id);
        $this->authorize('update', $file);
        $file->update($request->only(['title', 'type']));
        return response()->json($file);
    }

    public function destroy($id)
    {
        $file = Multimedia::findOrFail($id);
        $this->authorize('delete', $file);

        $relativePath = str_replace('/storage/', 'public/', $file->file_path);
        if (Storage::exists($relativePath)) {
            Storage::delete($relativePath);
        }

        $file->delete();

        return response()->json(['message' => 'Fitxer eliminat correctament.']);
    }
}
