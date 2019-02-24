<?php

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
  public $inserter;
  public $inserted;
  public $changer;
  public $changed;
  public static $showVars=['id','name','description','ortsgruppe_id','aktiv'];
  public static $importVars=['name','description','ortsgruppe_id','aktiv'];
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
  public $inserter;
  public $inserted;
  public $changer;
  public $changed;
  public static $showVars=['id','name','lat','lng','admin_id','description','twitter','facebook','email','telnr','aktiv'];
  public static $importVars=['name','lat','lng','admin_id','description','twitter','facebook','email','telnr','aktiv'];
}

class Demo{
  public $id;
  public $ort;
  public $zeit;
  public $ortsgruppe;
  public $teilnehmerzahl;
  public $beschreibung;
  public $aktiv;
  public $inserter;
  public $inserted;
  public $changer;
  public $changed;
  public static $showVars=['id','ort','zeit','ortsgruppe_id','teilnehmerzahl','beschreibung', 'aktiv'];
  public static $importVars=['ort','zeit','ortsgruppe_id','teilnehmerzahl','beschreibung', 'aktiv'];
}

class Demopropaganda{
  public $id;
  public $demo;
  public $ortsgruppe;
  public $name;
  public $content;
  public $aktiv;
  public $inserter;
  public $inserted;
  public $changer;
  public $changed;
  public static $showVars=['id','demo_id','ortsgruppe_id','name','content', 'aktiv'];
  public static $importVars=['demo_id','ortsgruppe_id','name','content', 'aktiv'];
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
