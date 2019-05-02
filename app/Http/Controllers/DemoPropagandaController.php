<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\DemoPropaganda;
use Laravel\Lumen\Routing\Controller as BaseController;

class DemoPropagandaController extends Controller
{
  public function list(Request $request){
    $where = [];
    if($request->has('ortsgruppe_id')){
      $where[] = ['ortsgruppe_id','=',$request->ortsgruppe_id];
    }
    if($request->has('demo_id')){
      $where[] = ['demo_id','=',$request->demo_id];
    }
    if($request->has('name')){
      $where[] = ['name','LIKE','%'.$request->name.'%'];
    }
    $demopropaganda = DemoPropaganda::where($where)->get(); 
    return response()->json($demopropaganda);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->validate($request, [
      'name' => 'required',
      'content' => 'required',
      'demo' => 'required',
    ]);
    if($demopropaganda = DemoPropaganda::Create($request->all())){
      $admin = $request->user();
      $demopropaganda->inserter_id=$admin->id;
      
      // ToDo: FatalThrowableError Class 'App\Http\Controllers\Demo' not found
      // $demo = Demo::find($request->demo_id);
      
      // if($demo == null){
      //   return $response->json(['status'=>'fail','msg'=>'invalid demo id '.$request->demo_id]);
      // }
      // if($demo->ortsgruppe_id != $admin->ortsgruppe_id && ! $admin->superadmin){
      //   return $response->json(['status'=>'fail','msg'=>'invalid demo id '.$request->demo_id]);
      // }

      $demopropaganda->save();
      return response()->json(['status' => 'success', 'demopropaganda'=>$demopropaganda]);
    }else{
      return response()->json(['status' => 'fail']);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $demopropaganda = DemoPropaganda::find($id);
    return response()->json($demopropaganda);

  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $demopropaganda = DemoPropaganda::find($id);
    return view('demopropaganda.editdemopropaganda',['demopropaganda' => $demopropaganda]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $demopropaganda = DemoPropaganda::find($id);
    if($demopropaganda->fill($request->all())->save()){
      return response()->json(['status' => 'success']);
    }
    return response()->json(['status' => 'failed']);
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    if(DemoPropaganda::destroy($id)){
      return response()->json(['status' => 'success']);
    }
  }

}
?>
