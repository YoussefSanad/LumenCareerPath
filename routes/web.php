<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// API route group
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');

    $router->group(['middleware' => 'auth'], function () use ($router) {


    });

    $router->get('/', function () use ($router) { return $router->app->version(); });

    //Job Posts APIs
    $router->get('job_posts', 'JobPostController@index');
    $router->get('job_posts/{jobPostId}', 'JobPostController@show');
    $router->post('job_posts', 'JobPostController@store');
    $router->put('job_posts/{jobPostId}', 'JobPostController@update');
    $router->delete('job_posts/{jobPostId}', 'JobPostController@destroy');

    //Applications APIs
    $router->get('applications', 'ApplicationController@index');
    $router->get('applications/{applicationId}', 'ApplicationController@show');
    $router->post('applications', 'ApplicationController@store');
    $router->delete('applications/{applicationId}', 'ApplicationController@destroy');

});


