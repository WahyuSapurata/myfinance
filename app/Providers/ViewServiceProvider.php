<?php

namespace App\Providers;

use App\Models\ReminderNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $unreadNotifications = 0;

            if (Auth::check()) {
                $unreadNotifications = ReminderNotification::where(
                    'user_id',
                    Auth::id()
                )
                    ->whereNull('read_at')
                    ->count();
            }

            $view->with('unreadNotifications', $unreadNotifications);
        });
    }
}
