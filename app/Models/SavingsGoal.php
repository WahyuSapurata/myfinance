<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingsGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'target_date',
        'description',
        'icon',
        'color',
        'status',
        'is_archived',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'target_date' => 'date',
        'is_archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contributions()
    {
        return $this->hasMany(GoalContribution::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(
            Transaction::class,
            GoalContribution::class,
            'savings_goal_id',
            'id',
            'id',
            'transaction_id'
        );
    }
}
