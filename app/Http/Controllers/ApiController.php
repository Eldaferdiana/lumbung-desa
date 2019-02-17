<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Address;
use Auth;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $user_id = null;

    public function __construct()
    {
        $this->middleware('auth');
        if($this->user_id == null)
            $this->user_id = Auth::id();
    }

    public function userinfo(Request $request)
    {
        $user = User::where(['id' => $this->user_id])->first();
        return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $user]);
    }

    public function show_useraddress(Request $request)
    {
        $user = Auth::user()->getAddress()->first();
        return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $user]);
    }

    public function edit_useraddress(Request $request)
    {
        $country = $request->input('country');
        $state = $request->input('state');
        $city = $request->input('city');
        $kecamatan = $request->input('kecamatan');
        $desa = $request->input('desa');
        $road = $request->input('road');

        if(!empty($country) && !empty($state) && !empty($city) && !empty($kecamatan) && !empty($desa) && !empty($road)) {
            $user_address = Auth::user()->getAddress()->first();
            if (is_null($user_address)) {
                $insertId = Auth::user()->getAddress()->create(['country' => $country, 'state' => $state, 'city' => $city, 'kecamatan' => $kecamatan, 'desa' => $desa, 'road' => $road]);
                if($insertId){
                    return response()->json(['status' => true, 'message' => 'Address Updated']);
                } else {
                    return response()->json(['status' => false, 'message' => 'Something went wrong'], 401);
                }
            } else {
                $insertId = Address::where(['id' => $user_address->id])->update(['country' => $country, 'state' => $state, 'city' => $city, 'kecamatan' => $kecamatan, 'desa' => $desa, 'road' => $road]);
                if($insertId){
                    return response()->json(['status' => true, 'message' => 'Address Updated']);
                } else {
                    return response()->json(['status' => false, 'message' => 'Something went wrong'], 401);
                }
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request'], 500);
        }
    }

    //
}
