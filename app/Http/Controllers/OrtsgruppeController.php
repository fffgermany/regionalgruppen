<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Ortsgruppe;
use Laravel\Lumen\Routing\Controller as BaseController;

class OrtsgruppeController extends Controller
{
  /*
  public function __construct()
  {
    $this->middleware('auth:api');
  }
   */

  public function list(Request $request){
    $where = [];
    if($request->name){
      $where[] = ['name','LIKE','%'.$request->name.'%'];
    }
    if($request->area && $request->area->latmax && $request->area->latmin && $request->area->lngmax && $request->area->lngmin){
      $where[] = ['lat','<=', $request->area->latmax];
      $where[] = ['lat','>=', $request->area->latmin];
      $where[] = ['lng','<=', $request->area->lngmax];
      $where[] = ['lng','>=', $request->area->lngmin];
    }
    $og = Ortsgruppe::where($where)->get(); 
    return response()->json($og);
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
      'description' => 'required',
    ]);
    if($og = Ortsgruppe::Create($request->all())){
      $admin = $request->user();
      //$og->admin()->save($admin);
      $og->admin_id=$admin->id;
      $og->inserter_id=$admin->id;
      $og->save();
      return response()->json(['status' => 'success', 'og'=>$og, 'admin'=>$admin]);
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
    $og = Ortsgruppe::where('id', $id)->get();
    return response()->json($og);

  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $og = Ortsgruppe::where('id', $id)->get();
    return view('ortsgruppe.editog',['ortsgruppe' => $og]);
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
      'name' => 'filled',
      'description' => 'filled',
    ]);
    $og = Ortsgruppe::find($id);
    if($og->fill($request->all())->save()){
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
    if(Ortsgruppe::destroy($id)){
      return response()->json(['status' => 'success']);
    }
  }

}
?>
