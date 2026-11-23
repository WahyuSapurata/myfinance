<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\SalaryProfile;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMonthlySalaries extends Command
{
    protected $signature = 'salary:process {--dry-run : Simulasi tanpa menyimpan transaksi}';

    protected $description = 'Memproses pemasukan gaji bulanan otomatis';

    public function handle()
    {
        $today = Carbon::today();

        $dryRun = $this->option('dry-run');

        $profiles = SalaryProfile::where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->get();

        $processed = 0;
        $skipped = 0;

        $salaryCategory = Category::where('name', 'Gaji')
            ->where('type', 'income')
            ->whereNull('user_id')
            ->where('is_active', true)
            ->first();

        if (!$salaryCategory) {
            $this->error('Kategori Gaji aktif tidak ditemukan.');
            return self::FAILURE;
        }

        foreach ($profiles as $profile) {
            $payDay = min(
                (int) $profile->pay_day,
                $today->daysInMonth
            );

            if ($today->day < $payDay) {
                $skipped++;
                continue;
            }

            try {
                $result = DB::transaction(function () use (
                    $profile,
                    $today,
                    $payDay,
                    $salaryCategory,
                    $dryRun
                ) {
                    $lockedProfile = SalaryProfile::whereKey($profile->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$lockedProfile || !$lockedProfile->is_active) {
                        return false;
                    }

                    $alreadyExists = Transaction::where(
                        'salary_profile_id',
                        $lockedProfile->id
                    )
                        ->where('salary_period', $today->format('Y-m'))
                        ->exists();

                    if ($alreadyExists) {
                        return false;
                    }

                    $incomeSource = $lockedProfile->incomeSource;
                    $wallet = $lockedProfile->wallet;

                    if (
                        !$incomeSource ||
                        !$incomeSource->is_active ||
                        !$wallet ||
                        !$wallet->is_active ||
                        $wallet->user_id != $lockedProfile->user_id
                    ) {
                        return false;
                    }

                    if (
                        $lockedProfile->end_date &&
                        Carbon::parse($lockedProfile->end_date)->lt($today)
                    ) {
                        return false;
                    }

                    $transactionDate = $today->copy()->day($payDay);

                    if ($dryRun) {
                        return true;
                    }

                    Transaction::create([
                        'user_id' => $lockedProfile->user_id,
                        'wallet_id' => $lockedProfile->wallet_id,
                        'category_id' => $salaryCategory->id,
                        'income_source_id' => $lockedProfile->income_source_id,
                        'salary_profile_id' => $lockedProfile->id,
                        'salary_period' => $today->format('Y-m'),
                        'type' => 'income',
                        'amount' => $lockedProfile->amount,
                        'transaction_date' => $transactionDate->toDateString(),
                        'description' => 'Gaji bulanan: ' . $lockedProfile->name,
                        'status' => 'posted',
                    ]);

                    return true;
                });

                if ($result) {
                    $processed++;

                    if ($dryRun) {
                        $this->info("[SIMULASI] Gaji siap diproses: {$profile->name}");
                    } else {
                        $this->info("Gaji berhasil dicatat: {$profile->name}");
                    }
                } else {
                    $skipped++;
                }
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '23000') {
                    $skipped++;
                    continue;
                }

                Log::error('Gagal memproses gaji otomatis', [
                    'salary_profile_id' => $profile->id,
                    'error' => $e->getMessage(),
                ]);

                $this->error("Gagal memproses: {$profile->name}");
            }
        }

        if ($dryRun) {
            $this->info("Gaji siap diproses (simulasi): {$processed}");
        } else {
            $this->info("Gaji berhasil dicatat: {$processed}");
        }

        $this->info("Dilewati: {$skipped}");

        return self::SUCCESS;
    }
}
