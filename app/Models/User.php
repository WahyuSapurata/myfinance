<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function incomeSources()
    {
        return $this->hasMany(IncomeSource::class);
    }

    public function salaryProfiles()
    {
        return $this->hasMany(SalaryProfile::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function savingsGoals()
    {
        return $this->hasMany(SavingsGoal::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
}
