<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;



class registerController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8'
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(),422);
        }
        
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);
        if($user) {
            return response()->json('Registrasi berhasil')->setStatusCode(201);
        }
        return response()->json('Registrasi gagal')->setStatusCode(409);
    }
}