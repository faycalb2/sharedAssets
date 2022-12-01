<?php

namespace App\Http\Controllers;

abstract class BaseController extends Controller
{
    public function successResponse($message, $data = [])
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ]);
    }

    public function errorResponse($message)
    {
        return response()->json([
            'message' => $message,
        ]);
    }
}
