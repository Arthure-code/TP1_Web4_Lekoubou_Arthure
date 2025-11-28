<?php

namespace App\Http\Controllers;
use App\Models\Critic;
use Symfony\Component\HttpFoundation\Response;

class CriticController extends Controller
{
    public function supprimerCritique($id)
    {
        $critique = Critic::find($id);
        if (!$critique) {
            return response()->json(['message' => 'Critique introuvable'], Response::HTTP_NOT_FOUND);
        }
        $critique->delete();
        return response()->noContent();
    }
}
