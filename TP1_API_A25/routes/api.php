<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{CriticController, FilmController, UserController};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/utilisateur', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/films/recherche', [FilmController::class, 'rechercherFilms']);
Route::get('/films', [FilmController::class, 'obtenirTousLesFilms']);
Route::get('/films/{id}/actors', [FilmController::class, 'obtenirActeursDuFilm']);
Route::get('/films/{id}/critiques', [FilmController::class, 'obtenirFilmAvecCritiques']);
Route::get('/films/{id}/moyenne-critiques', [FilmController::class, 'obtenirMoyenneCritiques']);


Route::post('/users', [UserController::class, 'creerUtilisateur']);
Route::put('/users/{id}', [UserController::class, 'mettreAJourUtilisateur']);
Route::get('/users/{id}/preferred-language', [UserController::class, 'obtenirLangagePrefere']);

Route::delete('/critics/{id}', [CriticController::class, 'supprimerCritique']);


