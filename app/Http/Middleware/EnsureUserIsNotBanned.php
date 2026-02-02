<?php

namespace App\Http\Middleware;

use App\Domains\Shared\Exceptions\ForbiddenForYouException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check() && auth()->user()->is_banned){
            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if($request->expectsJson()){
                throw new ForbiddenForYouException(403, 'Ваш аккаунт заблокирован!');
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'message' => 'Ваш аккаунт заблокирован!'
                ]);
        }

        return $next($request);
    }
}
