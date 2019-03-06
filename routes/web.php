<?php

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

$router->group(['prefix' => 'api/', 'middleware'=>'auth'], function ($router) {
  $router->get('login/','UserController@authenticate');

  $router->post('ortsgruppe/','OrtsgruppeController@store');
  $router->get('ortsgruppe/', 'OrtsgruppeController@list');
  $router->get('ortsgruppe/{id}/', 'OrtsgruppeController@show');
  $router->put('ortsgruppe/{id}/', 'OrtsgruppeController@update');
  $router->delete('ortsgruppe/{id}/', 'OrtsgruppeController@destroy');

  $router->post('demo/','DemoController@store');
  $router->get('demo/', 'DemoController@list');
  $router->get('demo/{id}/', 'DemoController@show');
  $router->put('demo/{id}/', 'DemoController@update');
  $router->delete('demo/{id}/', 'DemoController@destroy');

  $router->post('demopropaganda/','DemoPropagandaController@store');
  $router->get('demopropaganda/', 'DemoPropagandaController@list');
  $router->get('demopropaganda/{id}/', 'DemoPropagandaController@show');
  $router->put('demopropaganda/{id}/', 'DemoPropagandaController@update');
  $router->delete('demopropaganda/{id}/', 'DemoPropagandaController@destroy');
});  

$router->group(['prefix' => 'public/', 'middleware'=>[]], function ($router) {
  $router->get('ortsgruppe/', 'OrtsgruppeController@list');
  $router->get('ortsgruppe/{id}/', 'OrtsgruppeController@show');

  $router->get('demo/', 'DemoController@list');
  $router->get('demo/{id}/', 'DemoController@show');

  $router->get('demopropaganda/', 'DemoPropagandaController@list');
  $router->get('demopropaganda/{id}/', 'DemoPropagandaController@show');
});  
$router->get('/', function () use ($router) {
  return $router->app->version();
});
