<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;
use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function authenticate(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|email',
      'password' => 'required'
    ]);
    $user = User::where('email', $request->input('email'))->first();
    if($user && Hash::check($request->input('password'), $user->password)){

      User::where('email', $request->input('email'))->update(['apikey' => "$apikey"]);;
      return response()->json(['status' => 'success','apikey' => $apikey]);
    }else{
      return response()->json(['status' => 'fail'],401);
    }
    // */
  }

  public function prepareUser(Request $request){
    $this->validate($request, [
      'email' => 'required|email',
      'name' => 'required',
      'description' => 'required'
    ]);

    $user = User::Create($request->all());
    if( $request->user()->superadmin){
      $user->aktiv=true;
    }
    else{
      $user->aktiv=false;
    }
    $user->password=Hash::make(Str::random(32));
    $token = linktoken=Hash::make(Str::random(32));
    $user->$token;
    $user->save();
    return response()->json(['status'=>'success','linktoken'->$token, 'email'=>$request->input['email']]);
    };
  }

  public function activateUser(Request $request){
    $this->validate($request, [
      'email' => 'required|email',
      'name' => 'required',
    ]);
    $user = User::where([
      ['email', "=",$request->input('email')],
      ['name', "=",$request->input('name')],
    ])->first();
    if( $request->user()->superadmin){
      $user->aktiv=true;
    }
    $user->save();
    return response()->json(['status'=>'success']);
  }

  public function setPassword(Request $request){
    $this->validate($request, [
      'email' => 'required',
      'linktoken' => 'required',
      'password' => 'required']);

      $user = User::where([
        ['email', "=",$request->input('email')],
        ['linktoken', "=",$request->input('linktoken')],
      ])->first();
    if($user){
      $user->password=Hash::make($request->input['password']);
      $user->save();
    };
  }
}
?>
