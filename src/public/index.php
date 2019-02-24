<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require 'config.inc';


$api = new \Slim\App(['settings'=>$config]);

$container = $api->getContainer();

$container['logger'] = function($c) {
  $logger = new \Monolog\Logger('my_logger');
  $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
  $logger->pushHandler($file_handler);
  return $logger;
};

R::setup( 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['dbname'], $config['db']['user'], $config['db']['pass']);
R::freeze( TRUE );

define('USER','user');
define('ORTSGRUPPE','ortsgruppe');

$api->get('/user', function (Request $request, Response $response, array $args){
  $data = R::findAll(USER);
  echo json_encode(R::exportAll($data, FALSE, User::$showVars));
});
$api->get('/user/{id}', function (Request $request, Response $response, array $args){
  $data = R::load(USER, $args['id']);
  $show = User::visible($data->export());
  echo json_encode($show);
});

$api->put('/user', function (Request $request, Response $response){
  $content = json_decode($request->getBody(),true);
  $existing=R::findAll(USER,'email=? and ortsgruppe_id=?', [$content['email'],$content['ortsgruppe_id']]);
  if(count($existing) > 0){
    return $response->withStatus(400)->write("Duplikat mit User " . array_shift($existing)->id . "\n");
  }
  $insert=R::dispense(USER);
  $insert->import($content);
  R::store($insert);
});

$api->post('/user', function (Request $request, Response $response){
  $body=$request->getBody();
  $userData = json_decode($body,true);
  $existingUser=R::findAll(USER,'email=? and ortsgruppe_id=?', [$userData['email'],$userData['ortsgruppe_id']]);
  if(count($existingUser) > 0){
    return $response->withStatus(400)->write("Duplikat mit User " . array_shift($existingUser)->id . "\n");
  }
  $user=R::dispense(USER);
  $user->import($userData);
  R::store($user);

});

$api->get('/ortsgruppe', function (Request $request, Response $response, array $args){
  $data = R::findAll(ORTSGRUPPE);
  echo json_encode(R::exportAll($data));
});

$api->get('/ortsgruppe/{og}/user', function (Request $request, Response $response, array $args){
  $og = $args['og'];
  $data=R::findAll(USER,'ortsgruppe_id=?', [$og]);
  echo json_encode($data);
});

$api->get('/test', function(){
  echo "wenn ich nischt mache, gehts!";
});

$api->run();
?>
