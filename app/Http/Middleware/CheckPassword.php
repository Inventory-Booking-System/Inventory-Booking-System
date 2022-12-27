<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPassword
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
        //Get the authenticated user
        $user = Auth::user();

        //If the user has not created a password, redirect to the register page
        if (!$user->password_set){
            return redirect()->route('register');
        }else{
            //If the user has created a password, allow them access to the route
            return $next($request);
        }
    }
}
