<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Carbon\Carbon;


class authController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(),422);
        }
        if(Auth::attempt($validator->validated())){
            $payload = [
                'name'=> Auth::user()->name,
                'role'=> Auth::user()->role,
                'email'=> Auth::user()->email,
                'iat'=> Carbon::now()->timestamp,
                'exp'=> Carbon::now()->timestamp + 60*60*2

            ];
            $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');
            return response()->json([
                'messages'=>'Token Berhasil digenerate',
                'name'=>Auth::user()->name,
                'token'=>'Bearer '.$jwt
            ],200);
        }

        return response()->json(
            ['messages'=>"Pengguna tidak ditemukan"],422
        );
    }
}