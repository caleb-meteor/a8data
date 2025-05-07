<?php

namespace App\Http\Middleware;

use App\Services\MenuService;
use Caleb\Practice\Exceptions\PracticeAppException;
use Caleb\Practice\ThrowException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionCheck
{
    use ThrowException;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(Request): (Response) $next
     * @return Response
     * @throws PracticeAppException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $permission = $request->route()->action['as'] ?? '';
        if ($permission) {
            $hasPermission = MenuService::instance()->hasPermission($permission, $request->user());
            if (!$hasPermission) {
                $this->throwAppException('没有权限', 403);
            }
        }
        return $next($request);
    }
}
