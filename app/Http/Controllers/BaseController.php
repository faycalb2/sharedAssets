<?php

namespace App\Http\Controllers;

abstract class BaseController extends Controller
{
    /**
     * @OA\Info(
     *    title="Shared Assets APIs",
     *    version="1.0.0",
     * ),
     *   @OA\SecurityScheme(
     *       securityScheme="bearerAuth",
     *       in="header",
     *       name="bearerAuth",
     *       type="http",
     *       scheme="bearer",
     *       bearerFormat="JWT",
     *    ),
     */

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
