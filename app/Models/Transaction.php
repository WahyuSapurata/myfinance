<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'destination_wallet_id',
        'category_id',
        'income_source_id',
        'salary_profile_id',
        'type',
        'amount',
        'transaction_date',
        'description',
        'reference_number',
        'attachment',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function destinationWallet()
    {
        return $this->belongsTo(Wallet::class, 'destination_wallet_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function incomeSource()
    {
        return $this->belongsTo(IncomeSource::class);
    }

    public function salaryProfile()
    {
        return $this->belongsTo(SalaryProfile::class);
    }

    public function goalContribution()
    {
        return $this->hasOne(GoalContribution::class);
    }
}
