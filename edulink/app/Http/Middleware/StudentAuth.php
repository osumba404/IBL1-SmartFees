<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('student')->check()) {
            return redirect()->route('student.login');
        }

        $student = Auth::guard('student')->user();

        // Check if student account is active
        if ($student->status === 'suspended') {
            Auth::guard('student')->logout();
            return redirect()->route('student.login')->withErrors([
                'message' => 'Your account has been suspended. Please contact administration.'
            ]);
        }

        if ($student->status === 'inactive') {
            Auth::guard('student')->logout();
            return redirect()->route('student.login')->withErrors([
                'message' => 'Your account is inactive. Please contact administration.'
            ]);
        }

        return $next($request);
    }
}
