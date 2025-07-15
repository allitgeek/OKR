<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'key_result_id',
        'objective_id',
        'assignee_id',
        'creator_id',
        'start_date',
        'due_date',
        'status',
        'progress'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'progress' => 'float'
    ];

    public function keyResult()
    {
        return $this->belongsTo(KeyResult::class);
    }

    public function objective()
    {
        return $this->belongsTo(Objective::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
