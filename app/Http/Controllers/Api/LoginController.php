<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoginController extends BaseController
{
    public function login(Request $request){
        try{
            $parameters = [
                'avatar' => 'required',
                'type' => 'required',
                'name' => 'required',
                'open_id' => 'required',
                'email' => 'max:50',
                'phone' => 'max:30',
            ];
            
            $validator = Validator::make($request->all(),$parameters);

            if($validator->fails()){
                return $this->sendRepo(-1,$validator->errors()->first(),'No valid data');
            }

            $validated = $validator->validate();

            $map =[];
            $map['type'] = $validated['type'];
            $map['open_id'] = $validated['open_id'];
            $map['email'] = $validated['email'];
            $map['avatar'] = $validated['avatar'];

            $result = DB::table('users')
            ->select('name','description','type','token','access_token','online')
            ->where($map)->first();
     
            $validated['access_token'] = md5(uniqid().rand(10000,99999));
            $validated['expire_date'] = Carbon::now()->addDays(30);
                   
            if(empty($result)){
                $validated['token'] = md5(uniqid().rand(10000,99999));
                $validated['created_at'] = Carbon::now();

                $user_id = DB::table('users')->insertGetId($validated);
                $user_result = DB::table('users')
                ->select('avatar','name','description','type','token','access_token','online')
                ->where('id','=', $user_id)->first();

                return $this->sendRepo(0,'User has been created',$user_result);
            }

            DB::table('users')->where($map)->update([
                'access_token' =>  $validated['access_token'],
                'expire_date' =>  $validated['expire_date'],
            ]);

            $result->access_token = $validated['access_token'];
            return $this->sendRepo(0,'User information updated',$result);
            
        }catch(Exception $e){
           return $this->sendRepo(-1,"",(String)$e);
        }
    }
}
