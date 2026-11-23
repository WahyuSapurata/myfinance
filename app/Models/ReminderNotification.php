<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reminder_id',
        'due_date',
        'title',
        'message',
        'read_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }
}
