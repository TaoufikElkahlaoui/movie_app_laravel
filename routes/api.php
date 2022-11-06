<?php

Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');
Route::post('/verify_code', 'AuthController@verifyCode');

//genre routes
Route::get('/genres', 'GenreController@index');

//movie routes
Route::get('/movies/{movie}/images', 'MovieController@images');
Route::get('/movies/{movie}/actors', 'MovieController@actors');
Route::get('/movies/{movie}/related_movies', 'MovieController@relatedMovies');
Route::get('/movies', 'MovieController@index');

Route::middleware('auth:sanctum')->group(function () {

    //movie routes
    Route::any('/movies/toggle_movie', 'MovieController@toggleFavorite');

    //user route
    Route::any('/user', 'AuthController@user');
});