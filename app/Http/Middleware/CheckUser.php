<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;

class CheckUser {
    public function handle($request, Closure $next){
        $authorization = $request->header('Authorization');
        if(empty($authorization)){
            return response([
                'code' => 401,
                'message' => 'Authentication failed'
            ],401);
        }

        $access_token = trim(ltrim($authorization,'Bearer'));
        $res_user = DB::table('users')->where('access_token',$access_token)
        ->select('id','avatar','name','token','type','access_token','expire_date')->first();

        //check account exitst
        if(empty($res_user)){
            return response(['code'=>401,'message' => 'User does not exits'],401);
        }

        //check access_token expire
        $expire_date = $res_user->expire_date;
        if(empty($expire_date)){
            return response(['code'=>401,'message' => 'You must login again'],401);
        }

        //check access_token expired compare to date now
        if($expire_date < Carbon::now()){
            return response(['code'=>401,'message' => 'Your token has expired. You must login again'],401);
        }

        $addTime = Carbon::now()->addDays(5);
        if($expire_date < $addTime){
            $add_expire_date = Carbon::now()->addDays(30);

            DB::table('users')->where('access_token',$access_token)
            ->update(['expire_date' => $add_expire_date]);
        }

        $request->user_id = $res_user->id;
        $request->user_type = $res_user->type;
        $request->user_name = $res_user->name;
        $request->user_avatar = $res_user->avatar;
        $request->user_token = $res_user->token;
        return $next($request);
    }
}