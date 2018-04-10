<?php

namespace App\Http\Controllers;
use App\Lengo;
use Illuminate\Http\Request;
use App\Balance;

class LengoController extends Controller
{
    public function createLengo(Request $request){
      $this->validate($request,[
          'lengo_name'=> 'required',
          'time'=>'required',
          'user_id'=> 'required'
        ]);

          $lengo = new Lengo;
          $lengo->lengo_name = $request->lengo_name;
          $lengo->time = $request->time;
          $lengo->user_id = $request->user_id;
         $lengo->save();

         $balance = new Balance;
         $balance->lengo = $request->lengo_name;
         $balance->user_id = $request->user_id;
         $balance->save();

        $response = [
          'lengo' =>$lengo,
          'code'=>200
        ];
        return response()->json($response,200);
    }

    public function getMalengo($user_id){
      $malengo = Lengo::where('user_id', $user_id)->get();
      $response = [
        'malengo' =>$malengo,
        'code'=>200
      ];
      return response()->json($response,200);
  }

}
