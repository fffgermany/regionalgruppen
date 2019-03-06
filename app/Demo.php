<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Demo extends Model 
{
  protected $table="demo";

  protected $fillable = ['ortsgruppe_id','ort','name','lat','lng','zeit','teilnehmerzahl','description','aktiv', 'inserter_id','changer_id'];

  protected $hidden = [];

  public function ortsgruppe(){
    return $this->hasOne("\App\Ortsgruppe");
  }

  public function demopropagandas()
  {
    return $this->hasMany('App\DemoPropaganda');
  }
}
