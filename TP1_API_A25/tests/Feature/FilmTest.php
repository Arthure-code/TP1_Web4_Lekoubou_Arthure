<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Actor, Critic, Film};
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase;

    public function testGetFilmsRetourneListePaginee(): void
    {
        Film::factory(25)->create();

        $response = $this->get('/api/films');

        $response->assertStatus(Response::HTTP_OK);
        $films_array = $response->decodeResponseJson();

        $this->assertEquals(20, count($films_array['data']));

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'release_year', 'length', 'rating']
            ],
            'links',
            'meta'
        ]);
    }

    public function testGetFilmActeursRetourneListeActeurs(): void
    {

        $film = Film::factory()->create();
        $acteurs = Actor::factory(3)->create();

        $film->actors()->attach($acteurs->pluck('id'));
        $response = $this->get("/api/films/{$film->id}/actors");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'film_id',
            'acteurs' => [
                '*' => ['id', 'first_name', 'last_name', 'birthdate']
            ]
        ]);

        $data = $response->decodeResponseJson();
        $this->assertEquals(3, count($data['acteurs']));
    }

    public function testGetFilmActeursRetourne404SiFilmInexistant(): void
    {
        $response = $this->get('/api/films/99999/actors');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(['message' => 'Film introuvable']);
    }

    public function testGetFilmCritiquesRetourneFilmEtCritiques(): void
    {
        $film = Film::factory()->create();
        $critiques = Critic::factory(3)->create(['film_id' => $film->id]);

        $response = $this->get("/api/films/{$film->id}/critiques");

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'film' => ['id', 'title'],
            'critiques' => [
                '*' => ['id', 'score', 'comment', 'film_id', 'user_id']
            ]
        ]);

        $data = $response->decodeResponseJson();
        $this->assertEquals(3, count($data['critiques']));

        $response->assertJsonFragment([
            'score' => $critiques[0]->score,
            'film_id' => $film->id
        ]);
    }


    public function testGetFilmCritiquesRetourne404SiFilmInexistant(): void
    {
        $response = $this->get('/api/films/99999/critiques');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(['message' => 'Film introuvable']);
    }

    public function testGetFilmMoyenneRetourneScoreMoyen(): void
    {
        $film = Film::factory()->create();

        Critic::factory()->create(['film_id' => $film->id, 'score' => 4.0]);
        Critic::factory()->create(['film_id' => $film->id, 'score' => 2.0]);

        $response = $this->get("/api/films/{$film->id}/moyenne-critiques");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'film_id' => $film->id,
            'moyenne_score' => 3.0
        ]);
    }

    public function testGetFilmMoyenneRetourne404SiFilmInexistant(): void
    {
        $response = $this->get('/api/films/99999/moyenne-critiques');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(['message' => 'Film introuvable']);
    }

    public function testRechercheFilmsAvecKeywordEtMaxLength(): void
    {
        Film::factory()->create(['title' => 'Cat Movie', 'length' => 85, 'rating' => 'PG']);
        Film::factory()->create(['title' => 'Dog Movie', 'length' => 120, 'rating' => 'PG']);

        $response = $this->get('/api/films/recherche?keyword=cat&maxLength=90');

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->decodeResponseJson();
        $this->assertEquals(1, count($data['data']));

        $response->assertJsonFragment(['title' => 'Cat Movie']);
        $response->assertJsonMissing(['title' => 'Dog Movie']);
    }


    public function testRechercheFilmsSansCritereRetourneTousLesFilms(): void
    {
        Film::factory()->create(['title' => 'Film A']);
        Film::factory()->create(['title' => 'Film B']);

        $response = $this->get('/api/films/recherche');

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->decodeResponseJson();
        $this->assertEquals(2, count($data['data']));
    }
}
