<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Film, User};
use App\Http\Resources\{ActorResource, CriticResource, FilmResource};
use Symfony\Component\HttpFoundation\Response;

class FilmController extends Controller
{

    public function obtenirTousLesFilms()
    {
        return FilmResource::collection(Film::paginate(20));
    }

    public function obtenirActeursDuFilm($id)
    {
        $film = Film::with('actors')->find($id);
        if (!$film) {
            return response()->json(['message' => 'Film introuvable'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'film_id' => $film->id,
            'acteurs' => ActorResource::collection($film->actors)
        ], Response::HTTP_OK);
    }


    public function obtenirFilmAvecCritiques($id)
    {
        $film = Film::with('critics')->find($id);
        if (!$film) {
            return response()->json(['message' => 'Film introuvable'], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'film' => new FilmResource($film),
            'critiques' => CriticResource::collection($film->critics)
        ], Response::HTTP_OK);
    }

    public function obtenirMoyenneCritiques($id)
    {
        $film = Film::find($id);
        if (!$film) {
            return response()->json(['message' => 'Film introuvable'], Response::HTTP_NOT_FOUND);
        }
        $moyenne = $film->critics()->avg('score');

        $moyenne = $moyenne === null ? 0.0 : (float) $moyenne;
        return response()->json([
            'film_id' => $film->id,
            'moyenne_score' => round($moyenne, 1)
        ], Response::HTTP_OK);
    }

    public function rechercherFilms(Request $request)
    {
        $query = Film::query();

        if ($motCle = $request->get('keyword')) {
            $query->where('title', 'like', "%{$motCle}%");
        }
        if ($note = $request->get('rating')) {
            $query->where('rating', $note);
        }
        if ($min = $request->get('minLength')) {
            $query->where('length', '>=', (int)$min);
        }
        if ($max = $request->get('maxLength')) {
            $query->where('length', '<=', (int)$max);
        }

        return FilmResource::collection($query->paginate(20));
    }
}
