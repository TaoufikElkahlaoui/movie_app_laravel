<?php

Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');
Route::post('/verify_code', 'AuthController@verifyCode');

//genre routes
Route::any('/genres', 'GenreController@index');

//movie routes
Route::any('/movies/{movie}/images', 'MovieController@images');
Route::any('/movies/{movie}/actors', 'MovieController@actors');
Route::any('/movies/{movie}/related_movies', 'MovieController@relatedMovies');
Route::any('/movies', 'MovieController@index');

Route::middleware('auth:sanctum')->group(function () {

    //movie routes
    Route::any('/movies/toggle_movie', 'MovieController@toggleFavorite');

    //user route
    Route::any('/user', 'AuthController@user');
});