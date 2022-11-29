<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $message = "";

        try {
            $token = JWTAuth::parseToken();
            $user = $token->authenticate();
        } catch (TokenExpiredException $e) {
            $message = "Token expired";
            return response()->json(['success' => false, 'message' => $message]);
        } catch (TokenInvalidException $e) {
            $message = "Invalid Token";
            return response()->json(['success' => false, 'message' => $message]);
        } catch (JWTException $e) {
            $message = "Provide token";
            return response()->json(['success' => false, 'message' => $message]);
        }

        if ($user && $user->role_as == 1) {
            return $next($request);
        } else {
            return response()->json([
                'message' => 'Access denied as you are not an admin'
            ], 403);
        }

        return $this->unauthorized();
    }


    private function unauthorized($message = null)
    {
        return response()->json([
            'message' => 'You are unauthorized to access this resource, login first',
            'success' => false
        ], 401);
    }
}