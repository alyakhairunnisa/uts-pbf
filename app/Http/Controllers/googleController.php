<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Carbon\Carbon;


class googleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        
        $userFromGoogle = Socialite::driver('google')->stateless()->user();


        $userFromDatabase = User::where('email', $userFromGoogle->getEmail())->first();

        if ($userFromDatabase) {
            $payload = [
                'name'=> $userFromGoogle->getEmail(),
                'role'=> 'user',
                'email' => $userFromGoogle->getEmail(),
                'iat'=> Carbon::now()->timestamp,
                'exp'=> Carbon::now()->timestamp + 60*60*2
    
            ];
            $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');
            return response()->json([
                'messages'=>'Token Berhasil digenerate',
                'name'=>$userFromGoogle->getName(),
                'token'=>'Bearer '.$jwt
            ],200);

        }
        $newUser = User::create([
            'name' => $userFromGoogle->getName(),
            'email' => $userFromGoogle->getEmail(),
            'password' => bcrypt($userFromGoogle->getEmail())
        ]);

        $payload = [
            'name'=> $userFromGoogle->getEmail(),
            'email' => $userFromGoogle->getEmail(),
            'role'=> 'user',
            'iat'=> Carbon::now()->timestamp,
            'exp'=> Carbon::now()->timestamp + 60*60*2

        ];

        $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');
        //kirim token ke user
        return response()->json([
            'messages'=>'Register Berhasil',
            'name'=>$userFromGoogle->getName(),
            'token'=>'Bearer '.$jwt
        ],200);

    }
}
