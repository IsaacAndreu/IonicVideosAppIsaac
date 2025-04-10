<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Team;

class VideosController extends Controller
{
    /**
     * Mostra un vídeo específic.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $video = Video::findOrFail($id);

        return view('videos.show', compact('video'));
    }

    /**
     * Filtra vídeos que han estat testejats per un usuari específic.
     *
     * @param int $userId
     * @return View
     */
    public function testedBy(int $userId): View
    {
        $videos = Video::whereHas('tests', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return view('videos.testedBy', compact('videos'));
    }
    /**
     * Relació amb el model Team.
     *
     * @return BelongsTo<Team, TeamInvitation>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

}
