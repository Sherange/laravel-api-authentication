<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function userLogin(Request $request)
   {
       $validator = Validator::make($request->all(),[
            'email' => 'required|max:255',
            'password' => 'required'
       ]);

       if ($validator->fails()){
           return response()->json([
               'error' => true,
               'meassage' => $validator->errors(), 
               'status_code' => 400
           ], 400);
       }

       if(Auth::attempt(['email' => request('email'), 'password' => request('password')] )){
           
           $user = Auth::user();
           $response['token'] = $user->createToken('MyApp')->accessToken;
           
           return response()->json([
                'error' => false,
                'data' => $response, 
                'status_code' => 200
           ], 200);

       }else{

        return response()->json([
            'error' => true,
            'meassage' => 'Unauthorised', 
            'status_code' => 400
       ], 400);

       }
   }

   public function userRegistration(Request $request)
   {

        $validator = Validator::make($request->all(),[
                'name' => 'required|max:255',
                'email' => 'required|email',
                'password' => 'required'
        ]);

        if ($validator->fails()){
         
            return response()->json([
                'error' => true,
                'meassage' => $validator->errors(), 
                'status_code' => 400
            ], 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        
        $user = User::create($input);
        $response['token'] = $user->createToken('MyApp')->accessToken;
        $response['name'] = $user->name;

            return response()->json([
                'error' => false,
                'data' => $response, 
                'status_code' => 200
            ], 200);
        }

    public function userDetails()
    {
        $user = User::get();
        return response()->json([
            'error' => false,
            'data' => $user, 
            'status_code' => 200
        ], 200);
    } 
}
