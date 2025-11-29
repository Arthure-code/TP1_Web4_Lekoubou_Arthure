<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Critic, Film, Language, User};
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testPostUserCreeNouvelUtilisateur(): void
    {
        $language = Language::factory()->create(['name' => 'English']);

        $donnees = [
            'login' => 'testuser',
            'password' => 'password123',
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $response = $this->postJson('/api/users', $donnees);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'login' => 'testuser',
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $this->assertDatabaseHas('users', [
            'login' => 'testuser',
            'email' => 'test@example.com'
        ]);
    }

    public function testPostUserRetourne422SiChampManquant(): void
    {
        $donnees = [
            'password' => 'password123',
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];

        $response = $this->postJson('/api/users', $donnees);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['login']);
    }

    public function testPostUserRetourne422SiEmailDuplique(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $donnees = [
            'login' => 'newuser',
            'password' => 'password123',
            'email' => 'existing@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Doe'
        ];

        $response = $this->postJson('/api/users', $donnees);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['email']);
    }

    public function testPutUserMetAJourUtilisateur(): void
{
    $langue = Language::factory()->create(['name' => 'English']);
    $user = User::factory()->create([
        'login' => 'oldlogin',
        'email' => 'old@example.com',
    ]);

    $donnees = [
        'login' => 'newlogin',
        'password' => 'newpassword123',
        'email' => 'new@example.com',
        'first_name' => 'Updated',
        'last_name' => 'Name',
    ];

    $response = $this->putJson("/api/users/{$user->id}", $donnees);

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonFragment([
        'login' => 'newlogin',
        'email' => 'new@example.com',
        'first_name' => 'Updated',
        'last_name' => 'Name'
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'login' => 'newlogin',
        'email' => 'new@example.com'
    ]);
}

   public function testPutUserRetourne404SiUtilisateurInexistant(): void
{
    $donnees = [
        'login' => 'test',
        'password' => 'password123',
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',

    ];

    $response = $this->putJson('/api/users/99999', $donnees);

    $response->assertStatus(Response::HTTP_NOT_FOUND);
    $response->assertJson(['message' => 'Utilisateur introuvable']);
}

    public function testPutUserRetourne422SiDonneesInvalides(): void
    {
        $user = User::factory()->create();

        $donnees = [
            'login' => 'test',
            'password' => 'pass',
            'email' => 'invalid-email',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $donnees);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['email']);
    }

    public function testGetUserPreferredLanguageRetourneLangue(): void
    {

        $user = User::factory()->create();

        $langEn = Language::factory()->create(['name' => 'English']);
        $langFr = Language::factory()->create(['name' => 'French']);

        $filmEn = Film::factory()->create(['language_id' => $langEn->id]);
        $filmFr = Film::factory()->create(['language_id' => $langFr->id]);

        Critic::factory()->create(['user_id' => $user->id, 'film_id' => $filmEn->id, 'score' => 4]);
        Critic::factory()->create(['user_id' => $user->id, 'film_id' => $filmEn->id, 'score' => 5]);
        Critic::factory()->create(['user_id' => $user->id, 'film_id' => $filmFr->id, 'score' => 3]);

        $response = $this->get("/api/users/{$user->id}/preferred-language");

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'user_id' => $user->id,
            'langage_prefere' => 'English'
        ]);
    }

    public function testGetUserPreferredLanguageRetourneNullSiPasDeCritiques(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/api/users/{$user->id}/preferred-language");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'user_id' => $user->id,
            'langage_prefere' => null
        ]);
    }

    public function testGetUserPreferredLanguageRetourne404SiUserInexistant(): void
    {
        $response = $this->get('/api/users/99999/preferred-language');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(['message' => 'Utilisateur introuvable']);
    }

    public function testPutUserRetourne404SiUtilisateurInexistantAvecBodyValide(): void
    {

        $langue = \App\Models\Language::factory()->create();

        $donnees = [
            'login' => 'nouveau',
            'password' => 'password123',
            'email' => 'nouveau@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
        ];

        $response = $this->putJson('/api/users/99999', $donnees);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(['message' => 'Utilisateur introuvable']);
    }

    public function testPutUserRetourne422SiUtilisateurInexistantAvecBodyInvalide(): void
    {
        $donnees = [
        'login' => '',
        'email' => 'invalid-email',
        'password' => 'pw',
        'first_name' => '',
        'last_name' => ''
        ];

        $response = $this->putJson('/api/users/99999', $donnees);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
       $response->assertJsonValidationErrors(['login', 'email', 'password', 'first_name', 'last_name']);
    }
}
