<?php

namespace App\Exceptions;

use App\Http\Resources\Api\ApiStatus;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Lang;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof UnauthorizedHttpException) {
            // detect previous instance
            if ($exception->getPrevious() instanceof TokenExpiredException) {
                return response()->json([
                    'response' => [
                        'status' => [
                            'code' => $exception->getStatusCode(),
                            'message' => Lang::get('api.error_message.token_expired')
                        ]
                    ]
                ], $exception->getStatusCode());
            } else if ($exception->getPrevious() instanceof TokenInvalidException) {
                return response()->json([
                    'response' => [
                        'status' => [
                            'code' => $exception->getStatusCode(),
                            'message' => Lang::get('api.error_message.token_invalid')
                        ]
                    ]
                ], $exception->getStatusCode());
            } else if ($exception->getPrevious() instanceof TokenBlacklistedException) {
                return response()->json([
                    'response' => [
                        'status' => [
                            'code' => $exception->getStatusCode(),
                            'message' => Lang::get('api.error_message.token_black_list')
                        ]
                    ]
                ], $exception->getStatusCode());
            } else {
                return response()->json([
                    'response' => [
                        'status' => [
                            'code' => 401,
                            'message' => Lang::get('api.error_message.un_authorization')
                        ]
                    ]
                ], 401);
            }
        } else if ($exception instanceof ModelNotFoundException) {
            $apiStatus = new ApiStatus(404);
            return response()->json([
                'response' => [
                    'status' => [
                        'code' => $apiStatus->getStatusCode(),
                        'message' => $apiStatus->getStatusMsg()
                    ]
                ]
            ], $apiStatus->getStatusCode());
        }
        return parent::render($request, $exception);
    }
}
