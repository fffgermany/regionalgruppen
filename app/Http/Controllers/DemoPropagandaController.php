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
    if($request->ortsgruppe_id){
      $where[] = ['ortsgruppe_id','=',$request->ortsgruppe_id];
    }
    if($request->demo_id){
      $where[] = ['demo_id','=',$request->demo_id];
    }
    if($request->name){
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
      'demo_id' => 'required',
    ]);
    if($demopropaganda = DemoPropaganda::Create($request->all())){
      $admin = $request->user();
      $demopropaganda->inserter_id=$admin->id;

      if(! $request->user()->superadmin){
        if( $demopropaganda->ortsgruppe_id != $admin->ortsgruppe()->id){
          return response()->json(['status' => 'fail', 'msg'=>'falsche Ortsgruppe, admin hat Rechte für ' . $admin->ortsgruppe->name]);
        }
      }

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
    $demopropaganda = DemoPropaganda::where('id', $id)->get();
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
    $demopropaganda = DemoPropaganda::where('id', $id)->get();
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
    $this->validate($request, [
      'name' => 'required',
      'ort' => 'required',
      'zeit' => 'required',
    ]);
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
