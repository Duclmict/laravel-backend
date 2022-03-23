<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class CMSRoleAuthorization
{
	/**
	 * @param string $path
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	protected function redirectDefault($path = '/login')
	{
		return redirect($path);
	}
	
	/**
	 * unauthorized
	 * @param int $status
	 */
	protected function unauthorized($status = 401)
	{
		return abort($status);
	}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
    	if (!Auth::check()) {
            return $this->redirectDefault();
        }

        $role = Role::find(Auth::user()->role_id, "name");
        error_log($role);
        if ($role && in_array($role->name, $roles)) {
            return $next($request);
        }

        return $this->unauthorized();
    }
}