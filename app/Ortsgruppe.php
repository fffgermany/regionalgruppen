<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Ortsgruppe extends Model 
{


  protected $table="ortsgruppe";

  protected $fillable = ['name','lat','lng','admin_id','description','twitter','facebook','email','telnr','aktiv','inserter_id'];

  protected $hidden = ['inserter','changer'];

  public function admin(){
    return $this->hasOne("\App\User");
  }

  public function demos()
  {
    return $this->hasMany('App\Demo');
  }
}
