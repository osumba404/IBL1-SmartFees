<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('student')->user();

        if (!$user) {
            return redirect()->route('student.login');
        }

        // Check if student account is active
        if ($user->status !== 'active') {
            Auth::guard('student')->logout();
            
            return redirect()->route('student.login')
                ->withErrors(['account' => 'Your account is not active. Please contact the administrator.']);
        }

        return $next($request);
    }
}
