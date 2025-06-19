<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockSqlKeywords
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
        $input = implode(' ', $request->all());

        if (preg_match('/(drop|truncate|select \*|--)/i', $input)) {
            abort(403, 'Malicious input detected.');
        }

        return $next($request);
    }
}
