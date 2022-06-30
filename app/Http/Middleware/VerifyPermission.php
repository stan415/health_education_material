<?php


namespace App\Http\Middleware;

use App\Service\Admin\LoginService;
use App\Traits\ResponseAdapter;
use Closure;

class VerifyPermission
{
    use ResponseAdapter;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $jwt = $request->header('token');
        if (empty($jwt)) {
            return $this->unauthorized('token缺少，没有权限');
        }

        try {
            LoginService::validateToken($jwt);
        } catch (\Exception $e) {
            return $this->unauthorized('token无效');
        }

        $user_id = $request->get('user_id');
        if (empty($user_id)) {
            return $this->unauthorized('user_id缺少，没有权限');
        }

        return $next($request);
    }
}