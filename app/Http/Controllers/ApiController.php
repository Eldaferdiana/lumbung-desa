<?php

namespace App\Http\Controllers;
use App\ProductImage;
use Illuminate\Http\Request;
use App\User;
use App\Address;
use App\Product;
use App\Store;
use App\Conversation;
use App\Messages;
use App\Payment;
use App\Transaction;
use App\Cart;
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
        $this->middleware('auth', ['except' => ['check_msisdn', 'index', 'show_product', 'userinfo', 'userinfo_product', 'verify_payment']]);
        if($this->user_id == null)
            $this->user_id = Auth::id();
    }

    public function check_msisdn(Request $request){
        $msisdn = $request->input('msisdn');
        if(!empty($msisdn)){
            $user = User::where(['msisdn' => $msisdn])->first();
            if(!is_null($user))
                return response()->json(['status' => true, 'message' => 'User Exist']);
            else
                return response()->json(['status' => false, 'message' => 'User not found']);
        }
    }

    public function userinfo(Request $request)
    {
        $prodid = $request->input('product_id');

        if(!empty($prodid)){
            $product = Product::where(['id' => $prodid])->first();
            $user = User::where(['id' => $product->id_seller])->first();
            $address = Auth::user()->getAddress()->get();
            $user->address = $address[0];
            return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $user]);
        } else {
            $user = User::where(['id' => $this->user_id])->first();
            $address = Auth::user()->getAddress()->get();
            $user->address = $address[0];
            return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $user]);
        }
    }

    public function userinfo_product(Request $request)
    {
        $prodid = $request->input('product_id');

        if(!empty($prodid)){
            $product = Product::where(['id' => $prodid])->first();
            $user = User::where(['id' => $product->id_seller])->first();
            $address = Address::where(['id_user' => $user->id])->first();
            $user->address = $address;
            return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $user]);
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function show_category(Request $request)
    {
        $data = Category::all();
        return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $data]);
    }

    public function show_useraddress(Request $request)
    {
        if($request->input('id_user')){
            $uid = $request->input('id_user');
            $user = User::where(['id' => $uid])->first();
            $address = Address::where(['id_user' => $user->id])->first();
            $user->address = $address;
            return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $user]);
        } else {
            $user = Auth::user()->getAddress()->first();
            return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $user]);
        }
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
                    return response()->json(['status' => false, 'message' => 'Something went wrong']);
                }
            } else {
                $insertId = Address::where(['id' => $user_address->id])->update(['country' => $country, 'state' => $state, 'city' => $city, 'kecamatan' => $kecamatan, 'desa' => $desa, 'road' => $road]);
                if($insertId){
                    return response()->json(['status' => true, 'message' => 'Address Updated']);
                } else {
                    return response()->json(['status' => false, 'message' => 'Something went wrong']);
                }
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
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
                    return response()->json(['status' => false, 'message' => 'Something went wrong']);
                }
            } else {
                $insertId = Address::where(['id' => $user_store->id])->update(['store_name' => $name, 'store_desc' => $desc]);
                if($insertId){
                    return response()->json(['status' => true, 'message' => 'Store Updated']);
                } else {
                    return response()->json(['status' => false, 'message' => 'Something went wrong']);
                }
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function show_product(Request $request)
    {
        $prodid = $request->input('productId');

        if(!empty($prodid)){
            $product = Product::where(['id' => $prodid])->first();
            if(!is_null($product)){
                $product_img = ProductImage::where(['product_id' => $prodid])->get();
                $images_url = array();
                foreach($product_img as $img){
                    array_push($images_url, $img->product_image);
                }
                return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $product, 'images_url' => $images_url]);
            } else {
                return response()->json(['status' => false, 'message' => 'No product available']);
            }
        } else {
            $products = Product::all();

            if(!is_null($products)){
                return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $products]);
            } else {
                return response()->json(['status' => false, 'message' => 'No product available']);
            }
        }
    }

    public function add_product(Request $request)
    {
        $this->validate($request, [
            'product_image.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20000',
        ]);

        $name = $request->input('product_name');
        $desc = $request->input('product_desc');
        $price = $request->input('product_price');
        $stok = $request->input('product_stok');
        $cat = $request->input('product_cat');
        $exp = $request->input('expired_at');

        $tomorrow = new \DateTime('+ '.$exp.' day');
        $expired_date = $tomorrow->format("Y-m-d H:i:s");

        if(!empty($name) && !empty($desc) && !empty($price) && !empty($cat) && !empty($exp) && !empty($stok) && $request->hasFile('product_image')) {
            $insertId = Auth::user()->products()->create(['id_category' => $cat, 'product_name' => $name, 'product_desc' => $desc, 'product_price' => $price, 'product_stok' => $stok, 'expired_at' => $expired_date]);
            if($insertId){
                $images = $request->file('product_image');
                $link_img = array();
                foreach($images as $key => $image){
                    $name = md5($insertId->id.time().$key).'.'.$image->getClientOriginalExtension();
                    $destinationPath = '/home/cieoofkm/runup.web.id/images/product';
                    //$destinationPath = public_path().'/images/product/';
                    $image->move($destinationPath, $name);
                    $img_url = url('/').'/images/product/'.$name;

                    $insertImageId = Auth::user()->products()->where('id', $insertId->id)->first()->getProductImage()->create(['product_image' => $img_url]);
                    array_push($link_img, $img_url);
                }

                if($insertImageId){
                    Product::where('id', $insertId->id)->update(['ava_product' => $link_img[0]]);
                    $data = (object)array();
                    $data->product = Product::where('id', $insertId->id)->first();
                    $data->images_url = $link_img;
                    return response()->json(['status' => true, 'message' => 'Product Added', 'data' => $data]);
                } else {
                    Product::where(['id' => $insertId->id])->delete();
                    File::delete($destinationPath.$name);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Something went wrong']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function index(Request $request)
    {
        $catid = $request->input('catID');

        if(!empty($catid)){
            $product = Product::where(['id_category' => $catid])->get();
            if(count($product) > 0){
                return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $product]);
            } else {
                return response()->json(['status' => false, 'message' => 'No product available']);
            }
        } else {
            $product = Product::all();
            if(!is_null($product)){
                return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $product]);
            } else {
                return response()->json(['status' => false, 'message' => 'No product available']);
            }
        }
    }

    public function feed_home(Request $request)
    {
        $catid = $request->input('catID');

        if(!empty($catid)){
            $user_city = Auth::user()->getAddress()->first()->city;
            $user_addresses = Address::where(['city' => $user_city])->get();
            $product = array();
            foreach ($user_addresses as $key => $user_address){
                $products = Product::where(['id_seller' => $user_address->id_user, 'id_category' => $catid])->orderBy('created_at', 'DESC')->get();
                foreach ($products as $keys => $prod){
                    array_push($product, $prod);
                }
            }

            if(count($product) > 0){
                return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $product]);
            } else {
                return response()->json(['status' => false, 'message' => 'No product available']);
            }
        } else {
            $user_city = Auth::user()->getAddress()->first()->city;
            $user_addresses = Address::where(['city' => $user_city])->get();
            $product = array();
            foreach ($user_addresses as $key => $user_address){
                $products = Product::where(['id_seller' => $user_address->id_user])->orderBy('created_at', 'DESC')->get();
                foreach ($products as $keys => $prod){
                    array_push($product, $prod);
                }
            }

            if(count($product) > 0){
                return response()->json(['status' => true, 'message' => 'Data Retrivied', 'data' => $product]);
            } else {
                return response()->json(['status' => false, 'message' => 'No product available']);
            }
        }
    }

    //

    public function userava(Request $request) {
        $this->validate($request, [
            'input_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20000',
        ]);

        if ($request->hasFile('input_img')) {
            $image = $request->file('input_img');
            $name = md5($this->user_id.time()).'.'.$image->getClientOriginalExtension();
            $destinationPath = base_path().'/../runup.web.id/images/user/';
            $image->move($destinationPath, $name);
            $updateId = Auth::user()->update(['ava_url' => url('/').'/images/user/'.$name]);
            if($updateId)
                return response()->json(['status' => true, 'message' => 'Image Uploaded', 'data' => url('/').'/images/user/'.$name]);
            else
                return response()->json(['status' => false, 'message' => 'Something went wrong']);
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function get_chat(Request $request) {
        $conversation_id = $request->input('chat_id');
        if(!empty($conversation_id)) {
            $chats = Messages::where(['conversation_id' => $conversation_id])->orderBy('created_at', 'ASC')->get();

            if(!is_null($chats)){
                return response()->json(['status' => true, 'message' => 'Chat retrivied', 'data' => $chats]);
            } else {
                return response()->json(['status' => false, 'message' => 'No chat available']);
            }
        } else {
            $conversation = Conversation::where(['buyer_id' => $this->user_id])
                                        ->orWhere(['seller_id' => $this->user_id])
                                        ->get();
            if(!is_null($conversation)){
                foreach($conversation as $key => $convers){
                    if($convers->buyer_id == $this->user_id){
                        $conversation[$key]->destination_ava = User::where(['id' => $convers->seller_id])->first()->ava_url;
                        $conversation[$key]->destination_name = User::where(['id' => $convers->seller_id])->first()->name;
                    } else {
                        $conversation[$key]->destination_ava = User::where(['id' => $convers->buyer_id])->first()->ava_url;
                        $conversation[$key]->destination_name = User::where(['id' => $convers->buyer_id])->first()->name;
                    }
                }
                return response()->json(['status' => true, 'message' => 'Chat retrivied', 'data' => $conversation]);
            } else {
                return response()->json(['status' => false, 'message' => 'No chat available']);
            }
        }
    }

    public function chat_handle(Request $request) {
        $destination_id = $request->input('destination_id');
        $message = $request->input('message');
        if(!empty($destination_id) && !empty($message)){
            $seller_id = User::where(['id' => $destination_id])->first();
            if(!is_null($seller_id)){
                $seller_id = $seller_id->id;
                $conversation = Conversation::where(['seller_id' => $seller_id, 'buyer_id' => $this->user_id])
                                            ->orWhere(['seller_id' => $this->user_id, 'buyer_id' => $seller_id])
                                            ->first();

                if(is_null($conversation)){
                    $insertId = Conversation::create(['buyer_id' => $this->user_id, 'seller_id' => $seller_id]);
                    if($insertId){
                        $insertIdMessage = Messages::create(['conversation_id' => $insertId->id, 'sender_id' => $this->user_id, 'message' => $message]);
                        if($insertIdMessage){
                            $destProfile = User::where(['id' => $seller_id])->first();
                            $this->sendWebNotification($destProfile->token, "Pesan dari ".User::where('id', $this->user_id)->first()->name, $message);
                            return response()->json(['status' => true, 'message' => 'Message sent!', 'data' => $insertIdMessage]);
                       } else
                            return response()->json(['status' => false, 'message' => 'Something went wrong']);
                    } else {
                        return response()->json(['status' => false, 'message' => 'Something went wrong']);
                    }
                } else {
                    $insertIdMessage = Messages::create(['conversation_id' => $conversation->id, 'sender_id' => $this->user_id, 'message' => $message]);
                    if($insertIdMessage){
                        $destProfile = ($insertIdMessage->sender_id==$conversation->buyer_id) ? User::where(['id' => $conversation->seller_id])->first() : User::where(['id' => $conversation->buyer_id])->first();
                        $this->sendWebNotification($destProfile->token, "Pesan dari ".User::where('id', $this->user_id)->first()->name, $message);
                        return response()->json(['status' => true, 'message' => 'Message sent!', 'data' => $insertIdMessage]);
                    }else
                        return response()->json(['status' => false, 'message' => 'Something went wrong']);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'User Not Found']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function cart_handle(Request $request){
        $carts = Transaction::where(['id_buyer' => $this->user_id, 'checked_out' => false])->first();
        if(!is_null($carts)){
            $carts = $carts->getCart()->get();
            foreach($carts as $key => $cart){
                $product = Product::where(['id' => $cart->id_product])->first();
                $carts[$key]->product_name = $product->product_name;
                $carts[$key]->product_price = $product->product_price;
                $carts[$key]->product_ava = $product->ava_product;
            }
            return response()->json(['status' => true, 'message' => 'Cart retrivied!', 'data' => $carts]);
        } else {
            return response()->json(['status' => false, 'message' => 'Cart empty!']);
        }
    }

    public function add_to_cart(Request $request){
        $id_product = $request->input('id_product');
        $quantity = $request->input('quantity');

        if(!empty($id_product) && !empty($quantity)){
            $trxId = Transaction::where(['id_buyer' => $this->user_id, 'checked_out' => false])->first();

            if(is_null($trxId))
                $trxId = Transaction::create(['id_buyer' => $this->user_id]);

            $product = Product::where(['id' => $id_product])->first();
            $priceTotal = (int)$product->product_price*(int)$quantity;
            $insertCart = Cart::create(['id_user' => $this->user_id, 'id_seller' => $product->id_seller,'id_transaction' => $trxId->id, 'id_product' => $product->id, 'quantity' => $quantity, 'priceTotal' => $priceTotal]);
            if($insertCart){
                return response()->json(['status' => true, 'message' => 'Added to cart!']);
            } else {
                return response()->json(['status' => false, 'message' => 'Something went wrong']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function remove_from_cart(Request $request){
        $id_cart = $request->input('id_cart');
        if(!empty($id_cart)){
            $cart = Cart::where(['id' => $id_cart])->first();
            if(!is_null($cart)){
                $delete = Cart::where(['id' => $id_cart])->delete();
                if($delete) {
                    return response()->json(['status' => true, 'message' => 'Removed from cart!']);
                } else
                    return response()->json(['status' => false, 'message' => 'Something went wrong']);
            } else {
                return response()->json(['status' => false, 'message' => 'Product not found!']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function update_cart(Request $request){
        $id_cart = $request->input('id_cart');
        $quantity = $request->input('quantity');

        if(!empty($id_cart) && !empty($quantity)){
            $cart = Cart::where(['id' => $id_cart])->first();
            if(!is_null($cart)){
                $product_price = Product::where(['id' => $cart->id_product])->first()->product_price;
                $priceTotal = (int)$quantity*(int)$product_price;
                $update = Cart::where(['id' => $id_cart])->update(['quantity' => $quantity, 'priceTotal' => $priceTotal]);
                if($update) {
                    $cart = Transaction::where(['id_buyer' => $this->user_id, 'checked_out' => false])->first()->getCart()->get();
                    return response()->json(['status' => true, 'message' => 'Cart Updated!', 'data' => $cart]);
                } else
                    return response()->json(['status' => false, 'message' => 'Something went wrong']);
            } else {
                return response()->json(['status' => false, 'message' => 'Product not found!']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function checkout(Request $request) {
        $id_payment = $request->input('id_payment');

        if(!empty($id_payment)){
            $paid = ($id_payment == 2) ? true : false;
            $trxId = Transaction::where(['id_buyer' => $this->user_id, 'checked_out' => false])->first();
            if(!is_null($trxId)){
                $cartId = Cart::where(['id_transaction' => $trxId->id])->get();
                $subtotal = 0;
                foreach($cartId as $cart){
                    $tempProd = Product::where(['id' => $cart->id_product])->first()->product_stok;
                    $newStok = (int)$tempProd-1;
                    $updateProd = Product::where(['id' => $cart->id_product])->update(['product_stok' => $newStok]);
                    $subtotal += $cart->priceTotal;
                    $user = User::where(['id' => $cart->id_seller])->first();
                    $this->sendWebNotification($user->token, "Anda mendapat orderan!", "Segera cek dan proses pesanan pembeli anda");
                }

                if(!$paid) {
                    $now = date('Y-m-d H:i:s', time() - (3600 * 48));
                    $last_price = Transaction::where([['created_at', '>', $now], ['id_payment', '=', 1], ['paid', '=', 0]])->max('price_unique');
                    if(!is_null($last_price) || empty($last_price))
                        $subtotal += $last_price + 100;
                    else
                        $subtotal += 100;
                }

                $updateId = Transaction::where(['id' => $trxId->id])->update(['checked_out' => true, 'paid' => $paid, 'id_payment' => $id_payment, 'price_total' => $subtotal]);
                if($updateId){
                    $trxId = Transaction::where(['id' => $trxId->id])->first();
                    return response()->json(['status' => true, 'message' => 'Checkout successfully!' /*'data' => ['transaction' => $trxId, 'transaction_detail' => $cartId]*/]);
                } else {
                    return response()->json(['status' => false, 'message' => 'Something went wrong!']);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Transaction not found!']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function detail_handle(Request $request){
        $trxId = $request->input('transaction_id');
        
        if(!empty($trxId)){
            $detail = Transaction::where(['id' => $trxId])->first();

            $carts = Cart::where(['id_transaction' => $trxId])->get();
            $products = array();
            foreach($carts as $cart){
                $temp = Product::where(['id' => $cart->id_product])->get();
                foreach($temp as $key => $t){
                    $temp[$key]->quantity = $cart->quantity;
                }
                array_push($products, $temp[0]);
            }
            $detail->products = $products;

            return response()->json(['status' => true, 'message' => 'History retrivied!', 'data' => $detail]);
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function history_handle(Request $request){
        $status = $request->input('status');

        if(strtoupper($status) == "TAGIHAN") {
            $histories = Auth::user()->getTransaction()->where(['checked_out' => 1, 'paid' => 0, 'shipped' => 0, 'delivered' => 0])->orderBy('created_at', 'DESC')->get();

            foreach ($histories as $key => $history){
                $carts = Cart::where(['id_transaction' => $history->id])->get();
                $products = array();
                foreach($carts as $cart){
                    $temp = Product::where(['id' => $cart->id_product])->get();
                    array_push($products, $temp[0]);
                }
                $histories[$key]->products = $products;
            }

            return response()->json(['status' => true, 'message' => 'History retrivied!', 'data' => $histories]);
        } else if(strtoupper($status) == "PAID") {
            $histories = Auth::user()->getTransaction()->where(['checked_out' => 1, 'paid' => 1])->orderBy('created_at', 'DESC')->get();

            foreach ($histories as $key => $history){
                $carts = Cart::where(['id_transaction' => $history->id])->get();
                $products = array();
                foreach($carts as $cart){
                    $temp = Product::where(['id' => $cart->id_product])->get();
                    array_push($products, $temp[0]);
                }
                $histories[$key]->products = $products;
            }
            return response()->json(['status' => true, 'message' => 'History retrivied!', 'data' => $histories]);
        }  else if(strtoupper($status) == "SUKSES") {
            $histories = Auth::user()->getTransaction()->where(['checked_out' => 1, 'paid' => 1, 'shipped', 'delivered' => 1])->orderBy('created_at', 'DESC')->get();

            foreach ($histories as $key => $history){
                $carts = Cart::where(['id_transaction' => $history->id])->get();
                $products = array();
                foreach($carts as $cart){
                    $temp = Product::where(['id' => $cart->id_product])->get();
                    array_push($products, $temp[0]);
                }
                $histories[$key]->products = $products;
            }
            return response()->json(['status' => true, 'message' => 'History retrivied!', 'data' => $histories]);
        } else if(strtoupper($status) == "SALE") {
            $cartz = Cart::where(['id_seller' => $this->user_id])->get();

            $histories = array();
            foreach($cartz as $key => $cat){
                $trx = Transaction::where(['id' => $cat->id_transaction])->first();
                if($key != 0){
                    if($cartz[$key-1]->id_transaction != $trx->id) array_push($histories, $trx);
                } else {
                    array_push($histories, $trx);
                }
            }

            foreach ($histories as $key => $history){
                $carts = Cart::where(['id_transaction' => $history->id])->get();
                $products = array();
                foreach($carts as $cart){
                    $temp = Product::where(['id' => $cart->id_product, 'id_seller' => $this->user_id])->get();
                    if(count($temp) > 0) array_push($products, $temp[0]);
                }
                $histories[$key]->products = $products;
            }

            return response()->json(['status' => true, 'message' => 'History retrivied!', 'data' => $histories]);
        }  else if(strtoupper($status) == "MIND") {
            $histories = Auth::user()->products()->orderBy('created_at', 'DESC')->get();

            return response()->json(['status' => true, 'message' => 'History retrivied!', 'data' => $histories]);
        } else {
            $histories = Auth::user()->getTransaction()->orderBy('created_at', 'DESC')->get();

            foreach ($histories as $key => $history){
                $carts = Cart::where(['id_transaction' => $history->id])->get();
                $products = array();
                foreach($carts as $cart){
                    $temp = Product::where(['id' => $cart->id_product])->get();
                    array_push($products, $temp[0]);
                }
                $histories[$key]->products = $products;
            }
            return response()->json(['status' => true, 'message' => 'History retrivied!', 'data' => $histories]);
        }
    }

    public function set_done(Request $request){
        $idtrx = $request->input('transaction_id');
        
        if(!empty($idtrx)){
            $trx = Transaction::where(['id_buyer' => $this->user_id, 'id' => $idtrx, 'checked_out' => true, 'paid' => true, 'shipped' => true, 'delivered' => false])->update(['delivered' => true]);
            if($trx){
                $trx = Transaction::where(['id' => $idtrx])->first();
                $cart = Cart::where(['id_transaction' => $trx->id])->first();
                $user = User::where(['id' => $cart->id_seller])->first();
                $this->sendWebNotification($user->token, "Barang telah diterima!", "Selamat barang yang anda jual telah diterima oleh pembeli");
                return response()->json(['status' => true, 'message' => 'Transaction Updated']);
            } else {
                return response()->json(['status' => false, 'message' => 'Transaction not found']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }

    }

    public function set_accept(Request $request){
        $idtrx = $request->input('transaction_id');
        
        if(!empty($idtrx)){
            $trx = Transaction::where(['id' => $idtrx, 'checked_out' => true, 'paid' => true, 'shipped' => false, 'delivered' => false])->update(['shipped' => true]);
            if($trx){
                $trx = Transaction::where(['id' => $idtrx])->first();
                $user = User::where(['id' => $trx->id_buyer])->first();
                $this->sendWebNotification($user->token, "Barang diproses!", "Selamat barang yang anda beli sudah diproses oleh penjual");
                return response()->json(['status' => true, 'message' => 'Transaction Updated']);
            } else {
                return response()->json(['status' => false, 'message' => 'Transaction not found']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }

    }

    public function set_cencel(Request $request){
        $idtrx = $request->input('transaction_id');
        
        if(!empty($idtrx)){
            $trx = Transaction::where(['id' => $idtrx])->update(['cencelled' => true]);
            if($trx){
                $trx = Transaction::where(['id' => $idtrx])->first();
                $user = User::where(['id' => $trx->id_buyer])->first();
                $this->sendWebNotification($user->token, "Pesanan dibatalkan!", "Mohon maaf, penjual membatalkan pesanan anda");
                return response()->json(['status' => true, 'message' => 'Transaction Updated']);
            } else {
                return response()->json(['status' => false, 'message' => 'Transaction not found']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }

    }

    public function fcm_handle(Request $request){
        $fcm = $request->input('fcmToken');
        if(!empty($fcm)){
            $update = User::where(['id' => $this->user_id])->update(['token' => $fcm]);
            if($update){
                return response()->json(['status' => true, 'message' => 'Fcm Updated']);
            } else {
                return response()->json(['status' => false, 'message' => 'Something went wrong']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Bad Request']);
        }
    }

    public function verify_payment(){
        $this->sendWebNotification($to = "ba5ca4e7-a769-4506-8fc7-4f202846c97d", $title = "p", $content = "p");
    }

    private function sendWebNotification($to = "", $title = "", $content = "") {
        $content = array(
            "en" => $content,
            "id" => $content
        );


        $fields = array(
            'app_id' => "899937f0-5bf9-4a3a-aeda-3c88994879e0",
            "include_player_ids" => [$to],
            'headings' => array("en" => $title, "id" => $title),
            'isAnyWeb' => true,
            'small_icon' =>"ic_stat_one_signal_white",
            'android_accent_color' => '2196F3',
            'contents' => $content
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
            'Authorization: Basic ZGM1OGRmYWYtMGJiNy00NGIyLTgxN2QtMDYzOTc1MTQyZWJm'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
