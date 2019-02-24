<?php

function filterVisible($data, $showVars){
  return array_filter($data, function($k) use ($showVars){ return in_array($k, $showVars); }, ARRAY_FILTER_USE_KEY); }

class User{
  public $id;
  public $name;
  public $description;
  public $passwort;
  public $email;
  public $ortsgruppe;
  public $linktoken='';
  public $superadmin=0;
  public $aktiv=0;
  public static $showVars=['name','description','ortsgruppe_id','aktiv'];

  public static function visible($data){
    return filterVisible($data, self::$showVars);
  }
}

class Ortsgruppe { 
  public $id;
  public $name;
  public $lat;
  public $lng;
  public $admin;
  public $description;
  public $twitter;
  public $facebook;
  public $email;
  public $telnr;
  public $aktiv;
}

class Demo{
  public $id;
  public $ort;
  public $zeit;
  public $teilnehmerzahl;
  public $beschreibung;
}

class Demopropaganda{
  public $id;
  public $demo;
  public $ortsgruppe;
  public $content;
}

class Logging{
  public $id;
  public $admin;
  public $zeit;
  public $action;
  public $wert;
  public $primary;
}
?>
