<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'building_id',
        'created_by',
        'assigned_to',
        'title',
        'description',
        'status',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Obtém o edifício ao qual esta tarefa pertence.
     *
     * Relação muitos-para-um com o modelo Building.
     *
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Obtém o usuário que criou esta tarefa.
     *
     * Relação muitos-para-um com o modelo User, usando o campo 'created_by'.
     *
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtém o usuário atribuído a esta tarefa.
     *
     * Relação muitos-para-um com o modelo User, usando o campo 'assigned_to'.
     *
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Obtém os comentários associados a esta tarefa.
     *
     * Relação um-para-muitos com o modelo Comment.
     *
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}