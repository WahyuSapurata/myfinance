<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoalContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'savings_goal_id',
        'transaction_id',
        'amount',
        'contribution_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'contribution_date' => 'date',
    ];

    public function savingsGoal()
    {
        return $this->belongsTo(SavingsGoal::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
