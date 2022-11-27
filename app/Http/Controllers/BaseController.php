<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    public function successResponse($message, $data = [])
    {
        return response()->json([
            "success" => true,
            "status" => 201,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function errorResponse($message)
    {
        return response()->json([
            "error" => true,
            'message' => $message,
        ]);
    }
}
