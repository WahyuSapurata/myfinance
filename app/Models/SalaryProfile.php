<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'income_source_id',
        'wallet_id',
        'name',
        'amount',
        'pay_day',
        'start_date',
        'end_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'pay_day' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function incomeSource()
    {
        return $this->belongsTo(IncomeSource::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    // public function transactions()
    // {
    //     return $this->hasMany(Transaction::class);
    // }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'salary_profile_id');
    }
}
