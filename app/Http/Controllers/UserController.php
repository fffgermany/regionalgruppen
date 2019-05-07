<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\User;
use Laravel\Lumen\Routing\Controller as BaseController;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \Datetime;

class UserController extends Controller
{
  /**
   * check password
   *
   * @return \Illuminate\Http\Response
   */
  public function authenticate(Request $request)
  {
    $this->validate($request, [
	'email' => 'required|email',
	'password' => 'required'
    ]);
    $user = User::where('email', $request->input('email'))->first()->makeVisible('password');
    // ToDo check why Hash check
    if($user && Hash::check($request->input('password'), $user->password) && $user->aktiv > 0 && $user->verified > 0){

      $apikey=Hash::make('chilligras'.Str::random(32));
      User::where('email', $request->input('email'))->update(['apikey' => "$apikey"]);;
      return response()->json(['status' => 'success','apikey' => $apikey]);
    }else{
      return response()->json(['status' => Hash::check($request->input('password'), $user->password)],401);
    }
  }

  /**
   * create new user, not verified and not activated
   * @return \Illuminate\Http\Response
   */

  public function prepareUser(Request $request){
    $this->validate($request, [
	'email' => 'required|email',
	'name' => 'required',
	'password' => 'required',
	'description' => 'required',
	'thesecret'=>'required'
    ]);

    if(! $request->has('thesecret') or $request->thesecret != env('thesecret')){
      return response()->json(['status' => 'fail','response'=>"nicht gut!"]);
    }
    sleep(3);

    $user = User::Create($request->all());
    if($request->has('user') && $request->user()->superadmin){
      $user->aktiv=true;
    }
    else{
      $user->aktiv=false;
    }
    $user->password=Hash::make($request->password);
    $user->linktoken=Hash::make(Str::random(32));
    $token=$user->linktoken;
    $email=$user->email;

    $user->save();
    $this->sendMail($user->email, $user->name, $user->id, $user->linktoken);


    return response()->json(
        ['status'=>'success'
         ,'linktoken'=>$token
         , 'email'=>$email
        ]
       );
  }


  public function sendMail($email, $name, $id, $token){


    $mail=new PHPMailer(true);
    $mail->SMTPDebug = 2;  
    $mail->isSMTP();                                      // Set mailer to use SMTP                                                               
    $mail->Host = env('SMTP_HOST');  // Specify main and backup SMTP servers                                                                      
    Log::debug("host: ".$mail->Host);
    $mail->SMTPAuth = true;                               // Enable SMTP authentication                                                           
    $mail->Username = env('SMTP_USER'); // SMTP username                                                       
    Log::debug($mail->Username);
    $mail->Password = env('SMTP_PASS');
    Log::debug("pw: '" . env('SMTP_PASS') . "'");
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted                                           
    $mail->Port = env('SMTP_PORT');                                    // TCP port to connect to                                                               
    Log::debug($mail->Port);

    //Recipients
    $mail->setFrom(env('SMTP_FROM'));
    Log::debug(env('SMTP_FROM'));
    $mail->addAddress($email,$name);     // Add a recipient                                                              
    Log::debug("mail $email, name $name");
    $mail->Subject = 'Regionalgruppen Registrierung';
    $mail->Body    = "Hallo,\n\nauf der Webseite von FFF hat jemand - hoffentlich du selbst! - deine E-Mail Adresse registriert.\n\n"
      . "Wenn du das nicht warst, bitten wir um Entschuldigung. Wenn du es warst, dann ist hier dein Registrierungs-Link: \n"
      . $_SERVER['SERVER_NAME'].urlencode("/api/action/verify?email=$email&name=$name&linktoken=$token") . "\n\n"
      . " liebe Grue¿½sse, das FFF-Team\n";
    $mail->send();
  }

  public function verifyUser(Request $request){
    $this->validate($request, [
	'email' => 'required|email',
	'name' => 'required',
	'linktoken'=>'required'
    ]);
    // Log::debug("search user '{$request->input('email')}' name '{$request->input('name')}' linktoken '{$request->input('linktoken')}'");
    $alluser = User::where([
	['email', "=",$request->input('email')],
	['name', "=",$request->input('name')],
	['linktoken', "=",$request->input('linktoken')],
    ]);
    // Log::debug("alluser=".print_r($alluser,1));
    $user=$alluser->first();
    if($user->verified){
      return response()->json(['status' => 'fail','response'=>"bereits verifiziert"]);
    }
    if($user->created_at)
    $user->linktoken=Hash::make(Str::random(32));


    $user->save();
    return response()->json(['status'=>'success']);
  }

  public function checkToken(Request $request){
    $this->validate($request, [
	'email' => 'required',
	'linktoken' => 'required',
	'password' => 'required']);

    $checkdate = new DateTime();
    $checkdate->modify('-4 day');
    try{
      $users = User::where([
	  ['email', "=",$request->input('email')],
	  ['linktoken', "=",$request->input('linktoken')],
	  ['created_at', ">",$checkdate],
      ]);
      if(count($users) > 0){
	return response()->json(['status'=>'success']);
      }
    }
    catch(Exception $e){
    }
    return response()->json(['status'=>'fail']);
  }

  public function verify(Request $request){
    $this->validate($request, [
	'email' => 'required',
	'linktoken' => 'required',
	]);

    $checkdate = new DateTime();
    $checkdate->modify('-4 day');
    try{
      $users = User::where([
	  ['email', "=",$request->input('email')],
	  ['linktoken', "=",$request->input('linktoken')],
	  ['created_at', ">",$checkdate],
      ]);

      if(count($users) > 0){
	$user=$users->first();
	if($user){
	  $user->verified=1;
	  $user->save();
	  return response()->json(['status'=>'success']);
	};
      }
    }
    catch(Exception $e){
    }
    return response()->json(['status'=>'success']);
  }

  public function showRegPage(Request $request){
    return response("<!doctype html> <html> <head> </head> <body><form method=\"POST\" action=\"/public/user\">Name: <input type=\"text\" name=\"name\"/><br/>E-Mail:<input type=\"text\" name=\"email\"/><br/>Beschreibung:<input type=\"text\" name=\"description\"/><br/>Password:<input type=\"password\" name=\"password\"/><br/>  <br/>Secret: <input type=\"text\" name=\"thesecret\"/><br/><button type=\"submit\"name=\"OK\">ok</button></form></body></html>");
  }


}
?>
