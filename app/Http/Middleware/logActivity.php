<?php

// app/Http/Middleware/LogActivity.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Log;       // El modelo de logs
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        // Pasamos la petición al siguiente middleware/controlador
        $response = $next($request);

        // Aquí registramos el log DESPUÉS de que se ejecute la acción
        // porque así sabemos si el usuario está autenticado y quién es
        $user = Auth::user(); // o auth()->user() si usas login en middleware de token

        Log::create([
            'user_id'    => $user ? $user->id : null,
            'endpoint'   => $request->path(),    // /api/users/5/details
            'method'     => $request->method(),  // GET, POST...
            'ip_address' => $request->ip()
        ]);

        return $response;
    }
}

