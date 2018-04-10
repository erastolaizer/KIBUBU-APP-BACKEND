<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class UserController extends Controller
{
    public function register(Request $request){
    $this->validate($request,[
        'name'=> 'required',
        'phone_number'=>'required|unique:users',
        'password'=> 'required'
      ]);

      $user = new User;
        $user->name = $request->name;
        $user->phone_number = $request->phone_number;
        $user->password = bcrypt($request->password);

      $user->save();
      $response = [
        'user' =>$user,
        'code'=>200
      ];
      return response()->json($response,200);
    /*  return response()->json($request->all(),200); */
    }
    public function login(Request $request){
      $this->validate($request,[
          'phone_number'=>'required',
          'password'=> 'required'
        ]);

        $credentials = $request->only('phone_number', 'password');
        try {
              if (!$token = JWTAuth::attempt($credentials)){
                return response()->json([
                  'error'=> 'Invalid credentials'
                ], 401);
              }
        } catch (JWTException $e) {
          return response()->json([
            'error'=> 'Server temporary down'
          ], 500);
        }
        JWTAuth::setToken($token);
        $user = JWTAuth::toUser();
    return response()->json([
    'user'=>$user,
    'token'=>$token],200);
    }

    public function forgetPass(Request $request){

      $name = User::where('phone_number',$request->phone_number)->first();
      if (!$name) {
        return response()->json([
          'error'=> 'Invalid phone_number, please enter details correctly'
        ], 401);
      }

      else {
        $user = User::where('name',$request->name)->where('phone_number',$request->phone_number)->first();
        if (!$user) {
          return response()->json([
            'error'=> 'Full name Invalid, please register'
          ], 402);
        }
        else {
          $user->password = bcrypt($request->password);
          $user->save();
          return response()->json([
            'message'=> 'Password recovered'
          ], 200);
        }
      }
    }
}
