<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendRepo($code = 0,$msg = "",$data = []){
        return [
            "code" => $code,
            "msg" => $msg,
            "data" => $data,
        ];
    }
}
