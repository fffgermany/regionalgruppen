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
define('DEMO','demo');
define('DEMOPROPAGANDA','demopropaganda');

// who is logged in?
function getAdmin(){
  return ['id'=>42];
}

public function isAdmin(){
  // todo auth
  return true;
}

public void setInserted($data){
  $data->$inserter=getAdmin();
  $data->$inserted=date('Y-m-d H:i:s');
}
public void setChanged($data){
  $data->$changer=getAdmin();
  $data->$changed=date('Y-m-d H:i:s');
}

$api->get('/user', function (Request $request, Response $response, array $args){
  $data = R::findAll(USER);
  echo json_encode(R::exportAll($data, FALSE, User::$showVars));
});
$api->get('/user/{id}', function (Request $request, Response $response, array $args){
  $data = R::load(USER, $args['id']);
  $show = $data->export(FALSE, FALSE, FALSE, User::$showVars);
  echo json_encode($show);
});

$api->put('/user', function (Request $request, Response $response){
  $content = json_decode($request->getBody(),true);
  $existing=R::findAll(USER,'email=? and ortsgruppe_id=?', [$content['email'],$content['ortsgruppe_id']]);
  if(count($existing) > 0){
    return $response->withStatus(400)->write("Duplikat mit User " . array_shift($existing)->id . "\n");
  }
  $insert=R::dispense(USER);
  $insert->import($content, User::$importVars);
  $insert->aktiv=0;
  setInserted($insert);
  R::store($insert);
});

$api->post('/user', function (Request $request, Response $response){
  if(! isAdmin()){
    return $response->withStatus(403)->write("kein login!\n");
  }
  $body=$request->getBody();
  $data = json_decode($body,true);
  $update=null;
  if($data['id']){
    $update = R::load(USER, $data['id']);
    setChanged($data);
  }
  if($update==null){
    $update = R::dispense(USER);
    setInserted($data);
  }
  $update->import($data);
  R::store($update);
});

$api->get('/ortsgruppe/{og}/user', function (Request $request, Response $response, array $args){
  $og = $args['og'];
  $data=R::findAll(USER,'ortsgruppe_id=?', [$og]);
  echo json_encode(R::exportAll($data, FALSE, User::$showVars));
});

$api->get('/ortsgruppe', function (Request $request, Response $response, array $args){
  $data = R::findAll(ORTSGRUPPE);
  echo json_encode(R::exportAll($data, FALSE, Ortsgruppe::$showVars));
});

$api->get('/ortsgruppe/{id}', function (Request $request, Response $response, array $args){
  $data = R::load(ORTSGRUPPE, $args['id']);
  $show = $data->export(FALSE, FALSE, FALSE, Ortsgruppe::$showVars);
  echo json_encode($show);
});

$api->put('/ortsgruppe', function (Request $request, Response $response){
  $content = json_decode($request->getBody(),true);
  $existing=R::findAll(ORTSGRUPPE,'email=? and ortsgruppe_id=?', [$content['email'],$content['ortsgruppe_id']]);
  if(count($existing) > 0){
    return $response->withStatus(400)->write("Duplikat mit Ortsgruppe " . array_shift($existing)->id . "\n");
  }
  $insert=R::dispense(ORTSGRUPPE);
  $insert->import($content, Ortsgruppe::$importVars);
  $insert->aktiv=0;
  setInserted($insert);
  R::store($insert);
});

$api->post('/ortsgruppe', function (Request $request, Response $response){
  if(! isAdmin()){
    return $response->withStatus(403)->write("kein login!\n");
  }
  $body=$request->getBody();
  $data = json_decode($body,true);
  $update=null;
  if($data['id']){
    $update = R::load(ORTSGRUPPE, $data['id']);
    setChanged($data);
  }
  if($update==null){
    $update = R::dispense(ORTSGRUPPE);
    setInserted($data);
  }
  $update->import($data);
  R::store($update);
});

$api->get('/demo', function (Request $request, Response $response, array $args){
  $data = R::findAll(DEMO);
  echo json_encode(R::exportAll($data, FALSE, Demo::$showVars));
});

$api->get('/demo/{id}', function (Request $request, Response $response, array $args){
  $data = R::load(ORTSGRUPPE, $args['id']);
  $show = $data->export(FALSE, FALSE, FALSE, Demo::$showVars);
  echo json_encode($show);
});

$api->put('/demo', function (Request $request, Response $response){
  $content = json_decode($request->getBody(),true);
  $existing=R::findAll(DEMO,'ort=? and ortsgruppe_id=? and zeit between ? - interval 2 hour and ? + interval 2 hour', [$content['ort'],$content['ortsgruppe_id'], $content['zeit'], $content['zeit']]);
  if(count($existing) > 0){
    return $response->withStatus(400)->write("Duplikat mit Demo " . array_shift($existing)->id . "\n");
  }
  $insert=R::dispense(DEMO);
  $insert->import($content, Demo::$importVars);
  if(! isAdmin()){
    $insert->aktiv=0;
  }
  setInserted($insert);
  R::store($insert);
});

$api->post('/demo', function (Request $request, Response $response){
  if(! isAdmin()){
    return $response->withStatus(403)->write("kein login!\n");
  }
  $body=$request->getBody();
  $data = json_decode($body,true);
  $update=null;
  if($data['id']){
    $update = R::load(DEMO, $data['id']);
    setChanged($data);
  }
  if($update==null){
    $update = R::dispense(DEMO);
    setInserted($data);
  }
  $update->import($data);
  R::store($update);
});

$api->get('/demopropaganda', function (Request $request, Response $response, array $args){
  $data = R::findAll(DEMOPROPAGANDA);
  echo json_encode(R::exportAll($data, FALSE, Demopropaganda::$showVars));
});

$api->get('/demopropaganda/{id}', function (Request $request, Response $response, array $args){
  $data = R::load(ORTSGRUPPE, $args['id']);
  $show = $data->export(FALSE, FALSE, FALSE, Demopropaganda::$showVars);
  echo json_encode($show);
});

$api->put('/demopropaganda', function (Request $request, Response $response){
  $content = json_decode($request->getBody(),true);
  $existing=R::findAll(DEMOPROPAGANDA,'ort=? and ortsgruppe_id=? and zeit between ? - interval 2 hour and ? + interval 2 hour', [$content['ort'],$content['ortsgruppe_id'], $content['zeit'], $content['zeit']]);
  if(count($existing) > 0){
    return $response->withStatus(400)->write("Duplikat mit Demopropaganda " . array_shift($existing)->id . "\n");
  }
  $insert=R::dispense(DEMOPROPAGANDA);
  $insert->import($content, Demopropaganda::$importVars);
  if(! isAdmin()){
    $insert->aktiv=0;
  }
  setInserted($insert);
  R::store($insert);
});

$api->post('/demopropaganda', function (Request $request, Response $response){
  if(! isAdmin()){
    return $response->withStatus(403)->write("kein login!\n");
  }
  $body=$request->getBody();
  $data = json_decode($body,true);
  $update=null;
  if($data['id']){
    $update = R::load(DEMOPROPAGANDA, $data['id']);
    setChanged($data);
  }
  if($update==null){
    $update = R::dispense(DEMOPROPAGANDA);
    setInserted($data);
  }
  $update->import($data);
  R::store($update);
});







$api->get('/test', function(){
  echo "wenn ich nischt mache, gehts!";
});

$api->run();
?>
