<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isAdminNasional
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user_role = auth()->user()->role_id;
        if($user_role == 3 || $user_role == 1){ 
            // admin
            return $next($request); // artinya di bolehin 
        }

        return redirect('/')->with('error', "Access denied! (isAdminNasional)");
    }
}
