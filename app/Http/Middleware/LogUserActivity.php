<?php

namespace App\Http\Middleware;

use App\Models\UserActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah pengguna login
        if (auth()->check()) {
            // Mencatat aktivitas pengguna
            UserActivityLog::create([
                'user_id' => auth()->user()->id,
                'action' => $request->method() . ' ' . $request->path(), // Metode HTTP dan URL
                'details' => 'User accessed ' . $request->path(),
            ]);
        }
        return $next($request);
    }
}
