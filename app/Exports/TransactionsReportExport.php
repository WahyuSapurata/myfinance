<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionsReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $userId;

    public function __construct($startDate, $endDate, $userId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userId = $userId;
    }

    public function collection(): Collection
    {
        return Transaction::where('user_id', $this->userId)
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [
                $this->startDate,
                $this->endDate,
            ])
            ->with(['category', 'wallet'])
            ->orderBy('transaction_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Deskripsi',
            'Kategori',
            'Dompet',
            'Jenis Transaksi',
            'Jumlah',
        ];
    }

    public function map($transaction): array
    {
        return [
            date('d-m-Y', strtotime($transaction->transaction_date)),
            $transaction->description,
            $transaction->category?->name ?? 'Tanpa Kategori',
            $transaction->wallet?->name ?? '-',
            ucfirst($transaction->type),
            $transaction->amount,
        ];
    }
}
