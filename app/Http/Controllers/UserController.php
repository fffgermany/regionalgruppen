<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;
use Laravel\Lumen\Routing\Controller as BaseController;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
      'description' => 'required',
      'thesecret'=>'required'
    ]);

    if($request->safety != env('thesecret')){
      return response()->json(['status' => 'fail',response=>"nicht gut!"]);
    }
    sleep(2);

    $user = User::Create($request->all());
    if($request->has('user') && $request->user()->superadmin){
      $user->aktiv=true;
    }
    else{
      $user->aktiv=false;
    }
    $user->password=Hash::make(Str::random(32));
    $user->linktoken=Hash::make(Str::random(32));

    $user->save();
    sendMail($user->email, $user->name, $user->id, $user->linktoken);

    return response()->json(['status'=>'success','linktoken'->$token, 'email'=>$request->input['email']]);
  }


public function sendMail($email, $name, $id, $token){


$mail=new PHPMailer(true);
  $mail->SMTPDebug = 2;  
    $mail->isSMTP();                                      // Set mailer to use SMTP                                                               
    $mail->Host = env('SMTP_SERVER');  // Specify main and backup SMTP servers                                                                      
    $mail->SMTPAuth = true;                               // Enable SMTP authentication                                                           
    $mail->Username = env('SMTP_USER'); // SMTP username                                                       
    $mail->Password = env('SMTP_PASS');
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted                                           
    $mail->Port = env('SMTP_PORT');                                    // TCP port to connect to                                                               

   //Recipients
    $mail->setFrom(env('SMTP_FROM'));
    $mail->addAddress($email,$name);     // Add a recipient                                                              
    $mail->Subject = 'Regionalgruppen Registrierung';
    $mail->Body    = "Hallo,\n\nauf der Webseite von FFF hat jemand - hoffentlich du selbst! - deine E-Mail Adresse registriert.\n\n"
      . "Wenn du das nicht warst, bitten wir um Entschuldigung. Wenn du es warst, dann ist hier dein Registrierungs-Link: \n"
      . urlencode($_SERVER['SERVER_NAME']."/public/user/$id/activate?email=$email&name=$name&linktoken=$token") . "\n\n"
      . " liebe Grüße, das FFF-Team\n";
    $mail->send();



  }

  public function activateUser(Request $request){
    $this->validate($request, [
      'email' => 'required|email',
      'name' => 'required',
      'linktoken'=>'required'
    ]);
    $user = User::where([
      ['email', "=",$request->input('email')],
      ['name', "=",$request->input('name')],
      ['linktoken', "=",$request->input('linktoken')],
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
  
  public function showRegPage(Request $request){
    return response("<!doctype html> <html> <head> </head> <body><form method=\"POST\" action=\"/public/user\">Name: <input type=\"text\" name=\"name\"/><br/>E-Mail:<input type=\"text\" name=\"email\"/><br/>Beschreibung:<input type=\"text\" name=\"description\"/><br/>Secret: <input type=\"text\" name=\"thesecret\"/><br/><button type=\"submit\"name=\"OK\">ok</button></form></body></html>");
  }


}
?>
