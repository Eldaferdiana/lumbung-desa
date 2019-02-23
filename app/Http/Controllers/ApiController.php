<?php

namespace App\Http\Controllers;
use App\ProductImage;
use Illuminate\Http\Request;
use App\User;
use App\Address;
use App\Product;
use App\Store;
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
        $this->middleware('auth', ['except' => 'index']);
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

    public function show_store(Request $request)
    {
        $user = Auth::user()->getStore()->first();
        if(!is_null($user)){
            $product = Auth::user()->getStore()->first()->getProduct()->get();
            return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $user, 'product' => $product]);
        } else {
            return response()->json(['status' => false, 'message' => 'No store available']);
        }
    }

    public function edit_store(Request $request)
    {
        $name = $request->input('store_name');
        $desc = $request->input('store_desc');

        if(!empty($name) && !empty($desc)) {
            $user_store = Auth::user()->getStore()->first();
            $id_address = Address::where(['id_user' => $this->user_id])->first()->id;
            if (is_null($user_store)) {
                $insertId = Auth::user()->getStore()->create(['id_address' => $id_address, 'store_name' => $name, 'store_desc' => $desc]);
                if($insertId){
                    return response()->json(['status' => true, 'message' => 'Store Updated']);
                } else {
                    return response()->json(['status' => false, 'message' => 'Something went wrong'], 401);
                }
            } else {
                $insertId = Address::where(['id' => $user_store->id])->update(['store_name' => $name, 'store_desc' => $desc]);
                if($insertId){
                    return response()->json(['status' => true, 'message' => 'Store Updated']);
                } else {
                    return response()->json(['status' => false, 'message' => 'Something went wrong'], 401);
                }
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request'], 500);
        }
    }

    public function show_product(Request $request)
    {
        $prodid = $request->input('productId');

        if(!empty($prodid)){
            $product = Product::where(['id' => $prodid])->first();
            if(!is_null($product)){
                $product_img = ProductImage::where(['product_id' => $prodid])->get();
                return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $product, 'image_url' => $product_img]);
            } else {
                return response()->json(['status' => false, 'message' => 'No product available']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request'], 500);
        }
    }

    public function add_product(Request $request)
    {
        $this->validate($request, [
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $name = $request->input('product_name');
        $desc = $request->input('product_desc');
        $price = $request->input('product_price');
        $cat = $request->input('product_cat');
        $exp = $request->input('expired_at');

        $tomorrow = new \DateTime('+ '.$exp.' hour');
        $expired_date = $tomorrow->format("Y-m-d H:i:s");

        if(!empty($name) && !empty($desc) && !empty($price) && !empty($cat) && !empty($exp) && $request->hasFile('product_image')) {
            $insertId = Auth::user()->getStore()->first()->getProduct()->create(['id_category' => $cat, 'product_name' => $name, 'product_desc' => $desc, 'product_price' => $price, 'expired_at' => $expired_date]);
            if($insertId){
                $image = $request->file('product_image');
                $name = md5($insertId->id.time()).'.'.$image->getClientOriginalExtension();
                $destinationPath = base_path('public').'/images/product/';
                $image->move($destinationPath, $name);
                $img_url = url('/').'/images/product/'.$name;

                $insertImageId = Auth::user()->getStore()->first()->getProduct()->where('id', $insertId->id)->first()->getProductImage()->create(['product_image' => $img_url]);

                if($insertImageId){
                    return response()->json(['status' => true, 'message' => 'Product Added', 'data' => $img_url]);
                } else {
                    Product::where(['id' => $insertId->id])->delete();
                    File::delete($destinationPath.$name);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Something went wrong'], 401);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request'], 500);
        }
    }

    public function index(Request $request)
    {
        $product = Product::all();
        if(!is_null($product)){
            return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $product]);
        } else {
            return response()->json(['status' => false, 'message' => 'No product available']);
        }
    }

    public function feed_home(Request $request)
    {
        $user_city = Auth::user()->getAddress()->first()->city;
        $user_addresses = Address::where(['city' => $user_city])->get();
        $product = array();
        foreach ($user_addresses as $key => $user_address){
            $id_store = Store::where(['id_address' => $user_address->id])->first();
            $products = Product::where(['id_store' => $id_store->id])->get();
            foreach ($products as $keys => $prod){
                array_push($product, $prod);
            }
        }

        if(!is_null($product)){
            return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $product]);
        } else {
            return response()->json(['status' => false, 'message' => 'No product available']);
        }
    }

    //

    public function userava(Request $request) {
        $this->validate($request, [
            'input_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('input_img')) {
            $image = $request->file('input_img');
            $name = md5($this->user_id.time()).'.'.$image->getClientOriginalExtension();
            $destinationPath = base_path('public').'/images/user/';
            $image->move($destinationPath, $name);

            return response()->json(['status' => true, 'message' => 'Image Uploaded', 'data' => url('/').'/images/user/'.$name]);
        }
    }
}
