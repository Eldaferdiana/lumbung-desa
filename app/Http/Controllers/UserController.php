<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Hashing\BcryptHasher;
use App\User;
use Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function authenticate(Request $request)
    {
        if(!empty($request->input('msisdn')) && !empty($request->input('password'))){
            $user = User::where('msisdn', $request->input('msisdn'))->first();
            if(Hash::check($request->input('password'), $user->password)){
                $apikey = base64_encode(str_random(40));
                User::where('msisdn', $request->input('msisdn'))->update(['token' => "$apikey"]);;
                return response()->json(['status' => true, 'message' => 'Authorized!', 'token' => $apikey]);
            } else {
                return response()->json(['status' => false, 'message' => 'Unauthorized!', 'token' => ''], 401);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request!', 'token' => ''], 500);
        }
    }

    public function register(Request $request)
    {
        if(!empty($request->input('msisdn')) && !empty($request->input('password')) && !empty($request->input('name'))) {
            if(User::where('msisdn', $request->input('msisdn'))->count() == 0){
                $msisdn = $request->input('msisdn');
                $password = password_hash($request->input('password'), PASSWORD_DEFAULT);
                $name = $request->input('name');
                $insertId = User::create(['msisdn' => $msisdn, 'password' => $password, 'name' => $name]);
                if($insertId){
                    $apikey = base64_encode(str_random(40));
                    User::where('msisdn', $request->input('msisdn'))->update(['token' => "$apikey"]);;
                    return response()->json(['status' => true, 'message' => 'Register successfully!', 'token' => $apikey]);
                } else {
                    return response()->json(['status' => false, 'message' => 'User already registered!', 'token' => ''], 401);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'User already registered!', 'token' => ''], 401);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request!', 'token' => ''], 500);
        }
    }

    //
}
