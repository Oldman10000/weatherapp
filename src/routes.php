<?php

Route::any('/weather', 'Oldman10000\WeatherApp\WeatherController@index');
Route::any('/weather/{id}', 'Oldman10000\WeatherApp\WeatherController@show');
