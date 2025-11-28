<?php

namespace Tests\Feature;

use App\Models\Critic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CriticTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteCriticSupprimeCritique(): void
    {
        $critique = Critic::factory()->create();

        $response = $this->delete("/api/critics/{$critique->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('critics', [
            'id' => $critique->id
        ]);
    }

    public function testDeleteCriticRetourne404SiCritiqueInexistante(): void
    {
        $response = $this->delete('/api/critics/99999');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(['message' => 'Critique introuvable']);
    }
}
