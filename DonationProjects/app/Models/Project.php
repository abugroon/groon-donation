<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'target_amount',
        'collected_amount',
        'progress',
        'status',
        'image',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
        'progress' => 'decimal:2',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function getCompletionStatusAttribute(): string
    {
        return match ($this->status) {
            'completed' => __('projects.status.completed'),
            'in_progress' => __('projects.status.in_progress'),
            default => __('projects.status.open'),
        };
    }
}
