<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class HandleLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $frontendLocale = $request->cookie('locale', 'pt-BR');

        $laravelLocale = match ($frontendLocale) {
            'pt-BR' => 'pt_BR',
            'en' => 'en',
            default => 'en',
        };

        App::setLocale($laravelLocale);

        return $next($request);
    }
}
