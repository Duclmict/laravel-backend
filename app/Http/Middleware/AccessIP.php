<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AccessIP
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
        if (config('constants.allow_ip.allow') == false) {
            return $next($request);
        } else {
            $requestIP = $request->ip();
            $list = config('constants.allow_ip.list');
            if (in_array($requestIP, $list)) {
                return $next($request);
            }
        }

        $request->session()->flush();
        return redirect('/access-denied');
    }
}
