<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Balance;
use App\Lengo;
use App\MPesa;
use App\TigoPesa;
use App\AirtelMoney;
use Carbon\Carbon;
use App\User ;

class BalanceController extends Controller
{

  public function Balances($id){
    $balance = Balance::where('user_id' ,$id)->get();
    $response = [
      'balances' =>$balance,
      'code'=>200
    ];
    return response()->json($response,200);
  }

  public function withdraw(Request $request){
    $this->validate($request,[
        'lengo_name'=> 'required',
        'phone_number'=>'required',
        'service_provider'=>'required',
        'password'=>'required',
        'user_id'=> 'required'
      ]);
  $lengo =  $request->lengo_name ;
  $phone_number = $request->phone_number ;
  $service_provider = $request->service_provider ;
  $password = $request->password ;
  $user_id = $request->user_id ;

       $user = User::where('id',$user_id)->first();
       if(Hash::check($password,$user->password)){

       $userLengo = Lengo::where('lengo_name',$lengo)->where('user_id', $user_id)->first();
         if ($userLengo->time == "1 Month") {
          $expire = $userLengo->created_at->addMonth();
          return $this->checkTime($userLengo,$expire,$phone_number,$service_provider,$user_id);
         }
         else if ($userLengo->time == "3 Month") {
          $expire = $userLengo->created_at->addMonths(3);
          return $this->checkTime($userLengo,$expire,$phone_number,$service_provider,$user_id);
         }
         else if ($userLengo->time == "6 Month") {
          $expire = $userLengo->created_at->addMonths(6);
          return $this->checkTime($userLengo,$expire,$phone_number,$service_provider,$user_id);
         }
         else if ($userLengo->time == "1 Year") {
          $expire = $userLengo->created_at->addYear();
          return $this->checkTime($userLengo,$expire,$phone_number,$service_provider,$user_id);
         }
         else if ($userLengo->time == "1.5 Year") {
          $expire = $userLengo->created_at->addMonths(18);
          return $this->checkTime($userLengo,$expire,$phone_number,$service_provider,$user_id);
         }
     }
else {
  $response = [
    'message' =>"wrong kibubu password",
    'code'=>401
  ];
  return response()->json($response,401);
}
}


public function checkTime($userLengo,$expire,$phone_number,$service_provider,$user_id){
    $exp = Carbon::parse($expire);
    $date = Carbon::now();
    if($date->gte($exp)){
      if ($service_provider == 'M-PESA') {
        $pesa = MPesa::where('phone_number', $phone_number)->first();
        if (!$pesa) {
          $response = [
            'message' =>"phone number not found",
            'code'=>404
          ];
          return response()->json($response,404);
        } else {
          return $this->successWithdraw($pesa,$userLengo,$user_id);
        }
      }

      if ($service_provider == 'TIGO-PESA') {
        $pesa = TigoPesa::where('phone_number', $phone_number)->first();
        if (!$pesa) {
          $response = [
            'message' =>"phone number not found",
            'code'=>404
          ];
          return response()->json($response,404);
        } else {
          return $this->successWithdraw($pesa,$userLengo,$user_id);
        }
      }

      if ($service_provider == 'AIRTEL-MONEY') {
        $pesa = AirtelMoney::where('phone_number', $phone_number)->first();
        if (!$pesa) {
          $response = [
            'message' =>"phone number not found",
            'code'=>404
          ];
          return response()->json($response,404);
        } else {
          return $this->successWithdraw($pesa,$userLengo,$user_id);
        }
      }
    }
    else {
      $response = [
        'expire' =>$expire,
        'time'=> $userLengo->time,
        'code' => 400
      ];
      return response()->json($response,400);
    }
   }
   public function successWithdraw($pesa,$userLengo,$user_id){
     $balance = Balance::where('user_id',$user_id)->where('lengo',$userLengo->lengo_name)->first();
     $pesa->balance = $pesa->balance + $balance->amount ;
     $pesa->save();

     $balance->amount  = 0 ;
     $balance->save();

     $response = [
       'balance' =>$pesa->balance,
       'kibubu_balance'=> $balance->amount,
       'code' => 200
     ];
     return response()->json($response,200);
   }
}
