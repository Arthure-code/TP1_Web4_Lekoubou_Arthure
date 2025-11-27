<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\{StoreUserRequest, UpdateUserRequest};
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    //Créer un nouvel utilisateur
    public function creerUtilisateur(StoreUserRequest $request)
    {
        $donnees = $request->validated();
        $donnees['password'] = Hash::make($donnees['password']);
        $utilisateur = User::create($donnees);
        return (new UserResource($utilisateur))->response()->setStatusCode(201);
    }

    //  Mettre à jour un utilisateur existant
    public function mettreAJourUtilisateur(UpdateUserRequest $request, $id)
    {
        $utilisateur = User::find($id);
        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }
        $donnees = $request->validated();
        $donnees['password'] = Hash::make($donnees['password']);
        $utilisateur->update($donnees);
        return new UserResource($utilisateur);
    }

    // Obtenir le langage préféré d’un utilisateur
    public function obtenirLangagePrefere($id)
    {
        $utilisateur = User::with(['critics.film.language'])->find($id);
        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
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
            ], 200);
        }

        arsort($compteur);
        $langagePrefere = array_key_first($compteur);

        return response()->json([
            'user_id' => $utilisateur->id,
            'langage_prefere' => $langagePrefere
        ], 200);
    }
}
