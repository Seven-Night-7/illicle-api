<?php

namespace App\Http\Middleware;

use Closure;

class ApiCheckLogin
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
        $user = session('user');

        if (!$user) {
            return $this->echoDeny('请登录', -1);
        }

        return $next($request);
    }

    public function echoDeny($msg = '', $code = '')
    {
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:Access-Control-Allow-Methods');
        header('Access-Control-Allow-Headers:Content-Type, Accept, Authorization, X-Requested-With, Application');
        echo json_encode([
            'code' => $code,
            'msg' => $msg,
            'data' => [],
        ]);

        die(1);
    }
}
