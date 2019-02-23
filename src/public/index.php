<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require 'config.inc';


$api = new \Slim\App(['settings'=>$config]);

$container = $api->getContainer();

$user = new User();

$container['logger'] = function($c) {
  $logger = new \Monolog\Logger('my_logger');
  $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
  $logger->pushHandler($file_handler);
  return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

R::setup( 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['dbname'], $config['db']['user'], $config['db']['pass']);

$api->get('/user', function (Request $request, Response $response, array $args){
  $query = "select u.id, u.name, u.description, u.email, o.name as ortsgruppe, o.id as ortsgruppe_id from user u left join ortsgruppe o on o.id = u.ortsgruppe";
  $stmt = $this->db->prepare($query);
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($data);
});

$api->put('/user', function (Request $request, Response $response){
  $body=$request->getBody();
  $user = json_decode($body,true);
  if($user['ortsgruppe'] && ! is_numeric($user['ortsgruppe'])){
    return $response->withStatus(400)->write("ortsgruppe '" . $user['ortsgruppe'] . "' muss die ID sein!");
  }
  $query = "insert into user (name, description, email, ortsgruppe) values (:name, :description, :email, :ortsgruppe) ";
  $stmt = $this->db->prepare($query);
  $stmt->bindParam(":name",$user['name'], PDO::PARAM_STR);
  $stmt->bindParam(":description",$user['description'], PDO::PARAM_STR);
  $stmt->bindParam(":email",$user['email'], PDO::PARAM_STR);
  $stmt->bindParam(":ortsgruppe",$user['ortsgruppe'], PDO::PARAM_INT);
  $stmt->execute();
});

$api->get('/ortsgruppe', function (Request $request, Response $response, array $args){
  $query = "select * from ortsgruppe ";
  $stmt = $this->db->prepare($query);
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($data);
});

$api->get('/ortsgruppe/{og}/user', function (Request $request, Response $response, array $args){
  $og = $args['og'];
  $query = "select id, name, description, email from user where ortsgruppe = :ortsgruppe";
  $stmt = $this->db->prepare($query);
  $stmt->bindParam(":ortsgruppe",$og, PDO::PARAM_INT);
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
 echo json_encode($data);
});

$api->run();
?>
