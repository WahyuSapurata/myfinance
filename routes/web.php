<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeSourceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SalaryProfileController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Target Tabungan
    Route::get('/savings-goals', [SavingsGoalController::class, 'index'])
        ->name('savings-goals.index');

    Route::get('/savings-goals/create', [SavingsGoalController::class, 'create'])
        ->name('savings-goals.create');

    Route::post('/savings-goals', [SavingsGoalController::class, 'store'])
        ->name('savings-goals.store');

    Route::get(
        '/savings-goals/{savingsGoal}/history',
        [SavingsGoalController::class, 'history']
    )
        ->name('savings-goals.history');

    Route::patch(
        '/savings-goals/{savingsGoal}/contributions/{contribution}/cancel',
        [SavingsGoalController::class, 'cancelContribution']
    )->name('savings-goals.contribution.cancel');

    // Setoran Target Tabungan
    Route::get(
        '/savings-goals/{savingsGoal}/contribute',
        [SavingsGoalController::class, 'createContribution']
    )
        ->name('savings-goals.contribute');

    Route::post(
        '/savings-goals/{savingsGoal}/contribute',
        [SavingsGoalController::class, 'storeContribution']
    )
        ->name('savings-goals.contribute.store');

    Route::get(
        '/savings-goals/{savingsGoal}/edit',
        [SavingsGoalController::class, 'edit']
    )
        ->name('savings-goals.edit');

    Route::put(
        '/savings-goals/{savingsGoal}',
        [SavingsGoalController::class, 'update']
    )
        ->name('savings-goals.update');

    Route::patch(
        '/savings-goals/{savingsGoal}/complete',
        [SavingsGoalController::class, 'complete']
    )
        ->name('savings-goals.complete');

    Route::get(
        '/savings-goals/archived',
        [SavingsGoalController::class, 'archived']
    )
        ->name('savings-goals.archived');

    Route::patch(
        '/savings-goals/{savingsGoal}/archive',
        [SavingsGoalController::class, 'archive']
    )
        ->name('savings-goals.archive');

    Route::patch(
        '/savings-goals/{savingsGoal}/restore',
        [SavingsGoalController::class, 'restore']
    )
        ->name('savings-goals.restore');

    // Income Sources
    Route::resource('income-sources', IncomeSourceController::class)
        ->except(['show', 'destroy']);

    Route::patch(
        '/income-sources/{incomeSource}/toggle-status',
        [IncomeSourceController::class, 'toggleStatus']
    )->name('income-sources.toggle-status');

    // Salary Profiles
    Route::resource('salary-profiles', SalaryProfileController::class)
        ->except(['show', 'destroy']);

    Route::patch(
        '/salary-profiles/{salaryProfile}/toggle-status',
        [SalaryProfileController::class, 'toggleStatus']
    )->name('salary-profiles.toggle-status');

    // Pengingat
    Route::resource('reminders', ReminderController::class)
        ->except(['show']);

    Route::patch('/reminders/{reminder}/status', [ReminderController::class, 'updateStatus'])
        ->name('reminders.update-status');

    Route::get('/reminders/{reminder}/histories', [ReminderController::class, 'histories'])
        ->name('reminders.histories');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/wallets', [WalletController::class, 'index'])
        ->name('wallets.index');

    Route::get('/wallets/create', [WalletController::class, 'create'])
        ->name('wallets.create');

    Route::post('/wallets', [WalletController::class, 'store'])
        ->name('wallets.store');

    Route::get('/wallets/{wallet}/edit', [WalletController::class, 'edit'])
        ->name('wallets.edit');

    Route::put('/wallets/{wallet}', [WalletController::class, 'update'])
        ->name('wallets.update');

    Route::patch('/wallets/{wallet}/toggle-status', [WalletController::class, 'toggleStatus'])
        ->name('wallets.toggle-status');

    // Daftar transaksi
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');

    // Tambah transaksi
    Route::get('/transactions/create', [TransactionController::class, 'create'])
        ->name('transactions.create');

    Route::post('/transactions', [TransactionController::class, 'store'])
        ->name('transactions.store');

    // Transfer antar-dompet
    Route::get('/transactions/transfer', [TransactionController::class, 'createTransfer'])
        ->name('transactions.transfer');

    Route::post('/transactions/transfer', [TransactionController::class, 'storeTransfer'])
        ->name('transactions.transfer.store');

    // Edit transaksi
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])
        ->name('transactions.edit');

    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])
        ->name('transactions.update');

    // Batalkan transaksi
    Route::patch('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])
        ->name('transactions.cancel');
});

require __DIR__ . '/auth.php';
