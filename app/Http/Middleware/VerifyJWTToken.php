<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Lang;

class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'response' => [
                        'status' => [
                            'code' => 404,
                            'message' => Lang::get('api.error_message.no_object')
                        ]
                    ]
                ], 404);
            }
        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json([
                    'response' => [
                        'status' => [
                            'code' => $e->getStatusCode(),
                            'message' => Lang::get('api.error_message.token_expired')
                        ]
                    ]
                ], $e->getStatusCode());
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    'response' => [
                        'status' => [
                            'code' => $e->getStatusCode(),
                            'message' => Lang::get('api.error_message.token_invalid')
                        ]
                    ]
                ], $e->getStatusCode());
            }else{
                return response()->json([
                    'response' => [
                        'status' => [
                            'code' => 401,
                            'message' => Lang::get('api.error_message.token_required')
                        ]
                    ]
                ], 401);
            }
        }
        return $next($request);
    }
}
