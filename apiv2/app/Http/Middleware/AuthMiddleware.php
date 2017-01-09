<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;

use Closure;

use App\User;

use \Exception;

class AuthMiddleware
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
		global $currentUser;

		$loginHash = $request->cookie('loginHash');
		if ($loginHash && strlen($loginHash)) {
			list($username, $loginHash) = explode('|', sanitizeString($loginHash));
			try {
				$userCheck = DB::connection('mysql')->table('users')->select('userID')->where('username', $username)->whereNull('suspendedUntil')->where('banned', 0)->first();
				if ($userCheck === null) {
					throw new Exception('No user found');
				}
				$currentUser = new User(['username' => $username]);
				if ($currentUser->getLoginHash() === $loginHash) {
					$currentUser->generateLoginCookie();
					DB::connection('mysql')->update('UPDATE users SET lastActivity = NOW() WHERE userID = ?', [$currentUser->userID]);
				} else {
					throw new Exception('Invalid user hash');
				}
			} catch (Exception $e) {
				$currentUser = null;
				setcookie('loginHash', '', time() - 30, '/', COOKIE_DOMAIN);
			}
		} else {
			$currentUser = null;
		}

        return $next($request);
    }
}
