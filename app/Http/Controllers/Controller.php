<?php

namespace App\Http\Controllers;

abstract class Controller
{


    function apiSuccess($data = [], $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
