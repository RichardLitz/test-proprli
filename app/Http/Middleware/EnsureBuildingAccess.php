<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBuildingAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $building = $request->route('building');
        
        // Se o building não foi resolvido (não existe)
        if (!$building) {
            return response()->json([
                'message' => 'Building not found or invalid.',
                'details' => 'The requested building does not exist or the ID is invalid.'
            ], 404);
        }
        
        // Verifica se o usuário tem acesso ao edifício
        if (!$request->user() || !$building->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json([
                'message' => 'Unauthorized access to building.',
                'details' => 'User does not have permission to access this building.'
            ], 403);
        }
        
        return $next($request);
    }
}