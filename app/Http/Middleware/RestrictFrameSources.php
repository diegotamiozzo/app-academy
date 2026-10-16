<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictFrameSources
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set(
            'Content-Security-Policy',
            "frame-src 'self' https://docs.google.com https://drive.google.com https://www.youtube-nocookie.com"
        );

        return $response;
    }
}
