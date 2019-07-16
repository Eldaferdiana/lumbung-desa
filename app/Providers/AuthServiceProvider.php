<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.
        /*
        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
        });*/

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                $key = explode(' ',$request->header('Authorization'));
                $jwt = $key[1];

                $token = file_get_contents('http://alfarady.runup.web.id/jwt/test.php?jwt='.$jwt);
                $token = json_decode($token);
                $user = null;
                if($token->status){
                    if(time() <= $token->data->exp){
                        $user = User::where('id', $token->data->user_id)->first();
                        $request->request->add(['id' => $token->data->user_id]);
                    }
                }
                return $user;
            }
        });
    }
}
