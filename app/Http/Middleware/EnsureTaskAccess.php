<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTaskAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $task = $request->route('task');
        
        // Se a task não foi resolvida (não existe)
        if (!$task) {
            return response()->json([
                'message' => 'Task not found or invalid.',
                'details' => 'The requested task does not exist or the ID is invalid.'
            ], 404);
        }
        
        // Verifica se o usuário tem acesso ao edifício da tarefa
        if (!$request->user() || !$task->building->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json([
                'message' => 'Unauthorized access to task.',
                'details' => 'User does not have permission to access this task.'
            ], 403);
        }
        
        return $next($request);
    }
}