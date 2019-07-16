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

    public function register(Request $request)
    {
        $country = $request->input('country');
        $state = $request->input('state');
        $city = $request->input('city');
        $kecamatan = $request->input('kecamatan');
        $desa = $request->input('desa');
        $road = $request->input('road');
        $token = $request->input('notificationToken');

        if(!empty($token) && !empty($country) && !empty($state) && !empty($city) && !empty($kecamatan) && !empty($desa) && !empty($road) && !empty($request->input('msisdn')) && !empty($request->input('name')) && !empty($request->header('Authorization'))) {
            if ($request->header('Authorization')) {
              $key = explode(' ',$request->header('Authorization'));
              $jwt = $key[1];

              //$debug = true;

              $user = file_get_contents('http://alfarady.runup.web.id/jwt/test.php?jwt='.$jwt);
              $user = json_decode($user);
              if($user->status){
                  if(time() <= $user->data->exp){
                      if(User::where('msisdn', $request->input('msisdn'))->count() == 0){
                          $msisdn = $request->input('msisdn');
                          $name = $request->input('name');
                          $insertId = User::create(['id' => $user->data->user_id, 'msisdn' => $msisdn, 'name' => $name, 'token' => $token]);
                          if($insertId){
                                $insertAddrId = User::where(['id' => $insertId->id])->first()->getAddress()->create(['country' => $country, 'state' => $state, 'city' => $city, 'kecamatan' => $kecamatan, 'desa' => $desa, 'road' => $road]);
                                if($insertAddrId){
                                    return response()->json(['status' => true, 'message' => 'Register successfully!']);
                                } else {
                                    return response()->json(['status' => false, 'message' => 'Something went wrong']);
                                }
                          } else {
                                return response()->json(['status' => false, 'message' => 'User already registered!']);
                          }
                      } else {
                          return response()->json(['status' => false, 'message' => 'User already exist!']);
                      }
                  } else {
                      return response()->json(['status' => false, 'message' => 'Unauthorized!']);
                  }
              } else {
                  return response()->json(['status' => false, 'message' => 'Unauthorized!']);
              }
            } else {
                return response()->json(['status' => false, 'message' => 'Bad Request!']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request!']);
        }
    }

    //
}
