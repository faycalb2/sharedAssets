<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function destroy()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->successResponse('You are logged out.');
    }
}
