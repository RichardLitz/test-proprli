<?php

namespace App\Models;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'description',
    ];

    /**
     * Obtém as tarefas.
     *     
     * pode ter múltiplas tarefas associadas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */    
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Obtém os usuários.
     *     
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }
}