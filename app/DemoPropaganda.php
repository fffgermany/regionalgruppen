<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class DemoPropaganda extends Model 
{


  protected $table="demopropaganda";

  protected $fillable = ['name','content', 'demo', 'ortsgruppe_id'];

  protected $hidden = ['inserter','changer'];

  public function admin(){
    return $this->hasOne("\App\User");
  }

  public function demos()
  {
    return $this->hasMany('App\Demo');
  }
}
