<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Resources\{ActorResource, CriticResource, FilmResource};
use App\Models\Film;

class FilmController extends Controller
{
    //Obtenir la liste de tous les films
    public function obtenirTousLesFilms()
    {
        return FilmResource::collection(Film::paginate(20));
    }

    //Obtenir les acteurs d’un film spécifique
    public function obtenirActeursDuFilm($id)
    {
        $film = Film::find($id);
        if (!$film) {
            return response()->json(['message' => 'Film introuvable'], 404);
        }
        return ActorResource::collection($film->actors);
    }
    //Obtenir un film avec ses critiques
    public function obtenirFilmAvecCritiques($id)
    {
        $film = Film::with('critics')->find($id);
        if (!$film) {
            return response()->json(['message' => 'Film introuvable'], 404);
        }
        return response()->json([
            'film' => new FilmResource($film),
            'critiques' => CriticResource::collection($film->critics)
        ], 200);
    }

    //Obtenir la moyenne des critiques pour un film
    public function obtenirMoyenneCritiques($id)
    {
        $film = Film::find($id);
        if (!$film) {
            return response()->json(['message' => 'Film introuvable'], 404);
        }
        $moyenne = $film->critics()->avg('score') ?? 0.0;
        return response()->json([
            'film_id' => $film->id,
            'moyenne_score' => round($moyenne, 1)
        ], 200);
    }

    // Rechercher des films avec critères
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
