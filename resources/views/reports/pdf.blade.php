<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 25px;
            color: #666;
        }

        .summary {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .summary td {
            border: 1px solid #ddd;
            padding: 10px;
            width: 33.33%;
        }

        .summary strong {
            display: block;
            margin-bottom: 5px;
        }

        table.transactions {
            width: 100%;
            border-collapse: collapse;
        }

        .transactions th,
        .transactions td {
            border: 1px solid #ddd;
            padding: 7px;
            text-align: left;
        }

        .transactions th {
            background: #eee;
        }

        .income {
            color: green;
        }

        .expense {
            color: red;
        }

        .text-right {
            text-align: right !important;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            color: #777;
        }
    </style>
</head>

<body>

    <h2>LAPORAN KEUANGAN MYFINANCE</h2>

    <div class="subtitle">
        Periode:
        {{ \Carbon\Carbon::parse($validated['start_date'])->format('d-m-Y') }}
        sampai
        {{ \Carbon\Carbon::parse($validated['end_date'])->format('d-m-Y') }}
    </div>

    <table class="summary">
        <tr>
            <td>
                <strong>Total Pemasukan</strong>
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </td>

            <td>
                <strong>Total Pengeluaran</strong>
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </td>

            <td>
                <strong>Selisih</strong>
                Rp {{ number_format($balance, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <table class="transactions">
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Dompet</th>
                <th>Jenis</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>

        <tbody>
            @forelse($transactions as $index => $transaction)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') }}
                    </td>

                    <td>{{ $transaction->description }}</td>

                    <td>
                        {{ $transaction->category?->name ?? 'Tanpa Kategori' }}
                    </td>

                    <td>
                        {{ $transaction->wallet?->name ?? '-' }}
                    </td>

                    <td
                        class="{{ $transaction->type === 'income' ? 'income' : ($transaction->type === 'expense' ? 'expense' : '') }}">
                        {{ ucfirst($transaction->type) }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d-m-Y H:i') }}
    </div>

</body>

</html>
