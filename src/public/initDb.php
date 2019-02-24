<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require 'config.inc';


$api = new \Slim\App(['settings'=>$config]);

$container = $api->getContainer();

R::setup( 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['dbname'], $config['db']['user'], $config['db']['pass']);

$user = R::dispense('user');
$og = R::dispense('ortsgruppe');
$demo = new Demo();
$demoPropaganda=new Demopropaganda();
$logging = new Logging();

$og->name="Ortsgruppe";
$og->lat="2910.8799874732";
$og->lng="2910.439329892435";
$og->description=" lorem ipsum";
$og->twitter="rw";
$og->facebook="wer";
$og->email="wert";
$og->telnr="0234";
$og->aktiv=TRUE;

$user->name="paul";
$user->description="asd432";
$user->passwort="d0af90";
$user->email="32214";
$user->ortsgruppe=$og;
$user->linktoken='er3q';
$user->superadmin=FALSE;
$user->aktiv=FALSE;
$userId = R::store($user);

print("user=$userId, og=$ogId\n\n");
$og->admin=$user;
$ogId=R::store($og);
print("user=$userId, og=$ogId\n\n");

?>
