<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{

    public function sendResponse($data, $messge, $code = 200)
    {
        return response()->json([
            'status' => 'Success',
            'message' => $messge,
            'data' => $data
        ]);
    }


    public function sendError($data="", $messge, $code = 400)
    {
        return response()->json([
            'status' => 'Error',
            'message' => $messge,
            'data' => $data
        ]);
    }
}
