<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if($user['role'] != 'user'){
            return response()->json([
                'status' => 0 ,
                'data' => [] ,
                'message' => 'you dont have the access for this .....',
            ] , 404);
        }
        return $next($request);
    }
}
