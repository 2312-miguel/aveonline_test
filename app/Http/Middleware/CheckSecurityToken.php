<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckSecurityToken
{
    public function handle(Request $request, Closure $next)
    {
        $headerName   = 'X-Security-Token';
        $tokenCliente = $request->header($headerName);

        // Validar que exista el header
        if (!$tokenCliente) {
            return response()->json(['error' => 'Token incorrecto'], 401);
        }

        // Buscar usuario con ese api_token
        $user = User::where('api_token', $tokenCliente)->first();

        if (!$user) {
            return response()->json(['error' => 'Token incorrecto'], 401);
        }

        // Autenticamos a ese usuario en el contexto (opcional):
        // Con esto, en el request podremos usar Auth::user().
        // Si no deseas usar Auth, al menos ya sabes que $user es el dueño.
        Auth::login($user);

        return $next($request);
    }
}
