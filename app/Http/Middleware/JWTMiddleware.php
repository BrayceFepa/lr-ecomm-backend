<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
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
        $message = '';
        try {
            //check validation of the Token
            JWTAuth::parseToken()->authenticate();
            return $next($request);
        } catch (TokenExpiredException $e) {
            $message = "Token expired";
        } catch (TokenInvalidException $e) {
            $message = "Invalid Token";
        } catch (JWTException $e) {
            $message = "Provide token";
        }

        return response()->json(['success' => false, 'message' => $message]);
    }
}