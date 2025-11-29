<?php

namespace App\Http\Controllers;
use App\Models\Critic;
use Symfony\Component\HttpFoundation\Response;

class CriticController extends Controller
{

     /**
    * @OA\Delete(
    *   path="/api/critics/{id}",
    *   tags={"Critics"},
    *   summary="Deletes a critic",
    *   @OA\Response(response=204, description="No Content"),
    *   @OA\Parameter(name="id", in="path", required=true, description="critic id")
    * )
    */
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
