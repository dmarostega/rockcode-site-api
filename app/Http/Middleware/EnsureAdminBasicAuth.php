<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedUsername = config('admin.username');
        $expectedPassword = config('admin.password');

        if (! is_string($expectedUsername) || ! is_string($expectedPassword)) {
            return $this->deny();
        }

        if (trim($expectedUsername) === '' || trim($expectedPassword) === '') {
            return $this->deny();
        }

        $username = $request->getUser();
        $password = $request->getPassword();

        if (! is_string($username) || ! is_string($password)) {
            return $this->deny();
        }

        if (! hash_equals($expectedUsername, $username) || ! hash_equals($expectedPassword, $password)) {
            return $this->deny();
        }

        return $next($request);
    }

    private function deny(): Response
    {
        return response('Authentication required.', Response::HTTP_UNAUTHORIZED, [
            'WWW-Authenticate' => 'Basic realm="Rock Code Labs Admin"',
        ]);
    }
}
