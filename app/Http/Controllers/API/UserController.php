<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;


class UserController extends Controller{

    public $success_status = 200;

    /**
    *   Login Api
    *   @return \Illuminate\Http\Response
    */
    public function login()
    {
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $tokenResult = $user->createToken('MyApp');
            $token = $tokenResult->token;
            $success = [
                'token' => $tokenResult->accessToken,
                'email' => $user->email,
                'expires_at' => Carbon::now()->addHours(1)
            ];
            return response()->json(['success' => $success], $this->success_status);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    /**
    *   register Api
    *
    *   @return \Illuminate\Http\Response
    */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        $tokenResult = $user->createToken('MyApp');
        $token = $tokenResult->token;
        $succes = [
            'token' => $token->accessToken,
            'email' => $user->email,
            'name' => $user->name,
            'expires_at' => Carbon::now()->addHours(1)
        ];

        return response()->json(['success'=>$success], 200);
    }


    /**
     * details api
     *
     * @return \Illuminate\Http\Response
    */

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->success_status);
    }
}
