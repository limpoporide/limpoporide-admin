<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use Config;

class AfterFrontEndLogAuth
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
        
        if(Auth::guard('user')->check() AND auth()->guard('user')->user()->status == 1)
        {
                return $next($request);
        }
        else
        {
            return redirect('/user');
        }
       
    }

}
