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
  $router->post('login/','UserController@authenticate');

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

  $router->get('action/verify','UserController@verify');
});  

$router->group(['prefix' => 'public/', 'middleware'=>[]], function ($router) {
  $router->get('ortsgruppe/', 'OrtsgruppeController@list');
  $router->get('ortsgruppe/{id}/', 'OrtsgruppeController@show');

  $router->get('demo/', 'DemoController@list');
  $router->get('demo/{id}/', 'DemoController@show');

  $router->get('demopropaganda/', 'DemoPropagandaController@list');
  $router->get('demopropaganda/{id}/', 'DemoPropagandaController@show');
  $router->post('user/','UserController@prepareUser');
  $router->get('user/{id}/activate','UserController@activateUser');

});  
$router->group(['prefix' => 'register/', 'middleware'=>[]], function ($router) {
  $router->get('/', 'UserController@showRegPage');
});

$router->get('/{route:.*}/', function ()  {
  return view('app');
});

$router->get('/{route:.*}/', function ()  {
  return view('app');
});