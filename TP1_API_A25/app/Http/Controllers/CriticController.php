<?php

namespace App\Http\Controllers;
use App\Models\Critic;

class CriticController extends Controller
{
    public function supprimerCritique($id)
    {
        $critique = Critic::find($id);
        if (!$critique) {
            return response()->json(['message' => 'Critique introuvable'], 404);
        }
        $critique->delete();
        return response()->noContent();
    }
}
