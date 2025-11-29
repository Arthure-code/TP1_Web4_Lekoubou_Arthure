<?php

namespace App\Http\Controllers;


use App\Http\Requests\{StoreUserRequest, UpdateUserRequest};
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    /**
    * @OA\Post(
    *   path="/api/users",
    *   tags={"Users"},
    *   summary="Creates a user",
    *   @OA\Response(
    *       response=201,
    *       description="Created"
    *   ),
    *   @OA\RequestBody(
    *       @OA\MediaType(
    *           mediaType="application/json",
    *           @OA\Schema(
    *               @OA\Property(property="login", type="string"),
    *               @OA\Property(property="password", type="string"),
    *               @OA\Property(property="email", type="string"),
    *               @OA\Property(property="first_name", type="string"),
    *               @OA\Property(property="last_name", type="string")
    *           )
    *       )
    *   )
    * )
    */
    public function creerUtilisateur(StoreUserRequest $request)
    {
        $donnees = $request->validated();
        $donnees['password'] = Hash::make($donnees['password']);
        $utilisateur = User::create($donnees);
        return (new UserResource($utilisateur))->response()->setStatusCode(Response::HTTP_CREATED);
    }


        /**
    * @OA\Put(
    *   path="/api/users/{id}",
    *   tags={"Users"},
    *   summary="Updates a user",
    *   @OA\Response(response=200, description="OK"),
    *   @OA\Parameter(name="id", in="path", required=true, description="user id"),
    *   @OA\RequestBody(
    *       @OA\MediaType(
    *           mediaType="application/json",
    *           @OA\Schema(
    *               @OA\Property(property="login", type="string"),
    *               @OA\Property(property="password", type="string"),
    *               @OA\Property(property="email", type="string"),
    *               @OA\Property(property="first_name", type="string"),
    *               @OA\Property(property="last_name", type="string")
    *           )
    *       )
    *   )
    * )
    */
    public function mettreAJourUtilisateur(UpdateUserRequest $request, $id)
    {
        $utilisateur = User::find($id);
        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur introuvable'], Response::HTTP_NOT_FOUND);
        }
        $donnees = $request->validated();
        $donnees['password'] = Hash::make($donnees['password']);
        $utilisateur->update($donnees);
        return new UserResource($utilisateur);
    }


    /**
    * @OA\Get(
    *   path="/api/users/{id}/preferred-language",
    *   tags={"Users"},
    *   summary="Gets preferred language of a user",
    *   @OA\Response(response=200, description="OK"),
    *   @OA\Parameter(name="id", in="path", required=true, description="user id")
    * )
    */
    public function obtenirLangagePrefere($id)
    {
        $utilisateur = User::with(['critics.film.language'])->find($id);
        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur introuvable'], Response::HTTP_NOT_FOUND);
        }

        $compteur = [];
        foreach ($utilisateur->critics as $critique) {
            $langue = optional($critique->film->language)->name;
            if (!$langue) continue;
            $compteur[$langue] = ($compteur[$langue] ?? 0) + 1;
        }

        if (empty($compteur)) {
            return response()->json([
                'user_id' => $utilisateur->id,
                'langage_prefere' => null
            ], Response::HTTP_OK);
        }

        arsort($compteur);
        $langage_prefere = array_key_first($compteur);

        return response()->json([
            'user_id' => $utilisateur->id,
            'langage_prefere' => $langage_prefere
        ], Response::HTTP_OK);
    }
}
