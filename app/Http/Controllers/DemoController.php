<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Demo;
use Laravel\Lumen\Routing\Controller as BaseController;

class DemoController extends Controller
{
  public function list(Request $request){
    $where = [];
    if($request->has('ortsgruppe_id')){
      $where[] = ['ortsgruppe_id','=',$request->ortsgruppe_id];
    }
    if($request->has('name')){
      $where[] = ['name','LIKE','%'.$request->name.'%'];
    }
    if($request->has('zeit') && $request->zeit->von && $request->zeit->bis){
      $where[] = ['zeit','>=', $request->zeit->von];
      $where[] = ['zeit','<=', $request->zeit->bis];
    }
    if($request->has('area') && $request->area->latmax && $request->area->latmin && $request->area->lngmax && $request->area->lngmin){
      $where[] = ['lat','<=', $request->area->latmax];
      $where[] = ['lat','>=', $request->area->latmin];
      $where[] = ['lng','<=', $request->area->lngmax];
      $where[] = ['lng','>=', $request->area->lngmin];
    }
    $demo = Demo::where($where)->get(); 
    return response()->json($demo);
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
      'ort' => 'required',
      'zeit' => 'required',
    ]);
    if($demo = Demo::Create($request->all())){
      $admin = $request->user();
      $demo->admin_id=$admin->id;
      $demo->inserter_id=$admin->id;
      if(! $request->user()->superadmin || ! $demo->ortsgruppe_id){
        $demo->ortsgruppe_id=$admin->ortsgruppe()->id;
      }
      $demo->save();
      return response()->json(['status' => 'success', 'demo'=>$demo]);
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
    $demo = Demo::where('id', $id)->get();
    return response()->json($demo);

  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $demo = Demo::where('id', $id)->get();
    return view('demo.editdemo',['demo' => $demo]);
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
      'ort' => 'required',
      'zeit' => 'required',
    ]);
    $demo = Demo::find($id);
    if($demo->fill($request->all())->save()){
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
    if(Demo::destroy($id)){
      return response()->json(['status' => 'success']);
    }
  }

}
?>
