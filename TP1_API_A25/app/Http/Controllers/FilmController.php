<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use App\Http\Resources\{ActorResource, CriticResource, FilmResource};
use Symfony\Component\HttpFoundation\Response;

class FilmController extends Controller
{

    /**
    * @OA\Get(
    *   path="/api/films",
    *   tags={"Films"},
    *   summary="Gets list of films",
    *   @OA\Response(
    *       response=200,
    *       description="OK"
    *   )
    * )
    */
    public function obtenirTousLesFilms()
    {
        return FilmResource::collection(Film::paginate(20));
    }


     /**
    * @OA\Get(
    *   path="/api/films/{id}/actors",
    *   tags={"Films"},
    *   summary="Gets actors of a film",
    *   @OA\Response(response=200, description="OK"),
    *   @OA\Parameter(name="id", in="path", required=true, description="film id")
    * )
    */
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

/**
    * @OA\Get(
    *   path="/api/films/{id}/critiques",
    *   tags={"Films"},
    *   summary="Gets a film with its critics",
    *   @OA\Response(
    *       response=200,
    *       description="OK"
    *   ),
    *   @OA\Parameter(
    *       description="film id",
    *       in="path",
    *       name="id",
    *       required=true
    *   )
    * )
    */
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


        /**
    * @OA\Get(
    *   path="/api/films/{id}/moyenne-critiques",
    *   tags={"Films"},
    *   summary="Gets average score of a film",
    *   @OA\Response(response=200, description="OK"),
    *   @OA\Parameter(name="id", in="path", required=true, description="film id")
    * )
    */
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


    /**
    * @OA\Get(
    *   path="/api/films/recherche",
    *   tags={"Films"},
    *   summary="Search films with criteria",
    *   @OA\Response(response=200, description="OK"),
    *   @OA\Parameter(name="keyword", in="query", description="Filter by title keyword"),
    *   @OA\Parameter(name="rating", in="query", description="Filter by rating"),
    *   @OA\Parameter(name="minLength", in="query", description="Minimum length"),
    *   @OA\Parameter(name="maxLength", in="query", description="Maximum length")
    * )
    */
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
