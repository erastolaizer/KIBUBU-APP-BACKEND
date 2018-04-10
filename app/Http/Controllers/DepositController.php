<?php

namespace App\Http\Controllers;
use App\Deposit;
use Illuminate\Http\Request;
use App\Balance;
use App\Lengo;
use App\MPesa;
use App\TigoPesa;
use App\AirtelMoney;
use Carbon\Carbon;
use App\User ;

class DepositController extends Controller
{
  public function Deposit(Request $request){
    $this->validate($request,[
        'lengo'=> 'required',
        'amount'=>'required',
        'phone_number'=>'required',
        'service_provider'=>'required',
        'password'=>'required',
        'user_id'=> 'required'
      ]);
  $lengo =  $request->lengo ;
  $amount =  $request->amount;
  $phone_number = $request->phone_number ;
  $service_provider = $request->service_provider ;
  $password = $request->password ;
  $user_id = $request->user_id ;

      if ($request->service_provider == 'M-PESA') {
        $pesa = MPesa::where('phone_number', $request->phone_number)->first();
        if (!$pesa) {
          $response = [
            'message' =>"phone number not found",
            'code'=>404
          ];
          return response()->json($response,404);
        } else {
          return $this->checkBalance($pesa,$lengo,$amount,$phone_number,$service_provider,$password,$user_id);
        }
      }

      if ($request->service_provider == 'TIGO-PESA') {
        $pesa = TigoPesa::where('phone_number', $request->phone_number)->first();
        if (!$pesa) {
          $response = [
            'message' =>"phone number not found",
            'code'=>404
          ];
          return response()->json($response,404);
        } else {
          return $this->checkBalance($pesa,$lengo,$amount,$phone_number,$service_provider,$password,$user_id);
        }
      }

      if ($request->service_provider == 'AIRTEL-MONEY') {
        $pesa = AirtelMoney::where('phone_number', $request->phone_number)->first();
        if (!$pesa) {
          $response = [
            'message' =>"phone number not found",
            'code'=>404
          ];
          return response()->json($response,404);
        } else {
          return $this->checkBalance($pesa,$lengo,$amount,$phone_number,$service_provider,$password,$user_id);
        }
      }
  }

  public function checkBalance($pesa,$lengo,$amount,$phone_number,$service_provider,$password,$user_id){
   if($pesa->password == $password){
     if ($pesa->balance < $amount) {
       $response = [
         'message' =>"You dont have enough money",
         'balance' => $pesa->balance,
         'code'=>400
       ];
       return response()->json($response,400);
     }else {
       $deposit = new Deposit;
       $deposit->lengo  = $lengo;
       $deposit->service_provider  = $service_provider;
       $deposit->phone_number      = $phone_number     ;
       $deposit->amount = $amount;
       $deposit->user_id = $user_id;
       $deposit->save();

     $balance = Balance::where('user_id', $user_id)->where('lengo', $lengo)->first();
     $balance->amount = $balance->amount + $amount;
     $balance->save();

     $pesa->balance = $pesa->balance  - $amount ;
     $pesa->save();

     $response = [
       'deposit' =>$deposit,
       'code'=>200
     ];
     return response()->json($response,200);          }
   }else {
     $response = [
       'message' =>"password invalid",
       'code'=>401
     ];
     return response()->json($response,401);
   }
  }

  public function history($user_id){

      $deposit = Deposit::where('user_id', $user_id)->get();
      $response = [
        'deposits' =>$deposit,
        'code'=>200
      ];
      return response()->json($response,200);
  }

}
