<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Task;

class CommentService
{
    /**
     * Cria um novo comentário para uma tarefa.
     *
     * Utiliza o ID do usuário autenticado como o autor do comentário
     * e associa o comentário à tarefa especificada.
     *
     * @param \App\Models\Task $task A tarefa à qual o comentário será associado
     * @param array $data Os dados do comentário (conteúdo)
     * @return \App\Models\Comment O comentário criado
     */
    public function createComment(Task $task, array $data): Comment
    {
        $data['task_id'] = $task->id;
        $data['user_id'] = auth()->id();
        
        return Comment::create($data);
    }
}