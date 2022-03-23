<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use App\Models\Role;
use App\Models\ApiModule;
use App\Models\RoleModule;
use App\Services\CustomLogService;
use Lang;

class APIRoleAuthorization
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
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
                    "request path: ".$request->path());
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
                    "request method: ".$request->getMethod());

        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "login users".$user);
            $module = ApiModule::getModuleByPathName($request->path(), $request->getMethod(), "/");
            $role = Role::find($user->role_id, "role_identifier");
            if ($module && $role) {
                CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
                    "module_identifier: ".$module['module_identifier']);
                
                CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
                    "role_identifier: ".$role->role_identifier);

                if(RoleModule::checkPermission($role->role_identifier, $module['module_identifier'])) 
                    return $next($request);
            }
        }

        CustomLogService::error(__FILE__, __LINE__, __CLASS__, 
                    "[error] module_identifier: "
                    .$module['module_identifier']);
                
        CustomLogService::error(__FILE__, __LINE__,__CLASS__, 
            "[error] role_identifier: ".$role->role_identifier);

        return response()->json([
            'response' => [
                'status' => [
                    'code' => 401,
                    'message' => Lang::get('api.error_message.un_authorization')
                ]
            ]
        ], 401);
    }
}
