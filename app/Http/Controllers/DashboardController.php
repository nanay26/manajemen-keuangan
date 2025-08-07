<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Total pemasukan dan pengeluaran
        $totalIncome = $user->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $user->transactions()->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Grafik pengeluaran per kategori
        $pengeluaran = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(fn($row) => $row->sum('amount'));

        // Grafik pemasukan vs pengeluaran bulanan (12 bulan terakhir)
        $monthlyData = $user->transactions()
            ->whereBetween('date', [
                Carbon::now()->subMonths(11)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->get()
            ->groupBy(fn($item) => Carbon::parse($item->date)->format('Y-m'));

        $months = collect();
        $incomeData = collect();
        $expenseData = collect();

        foreach (range(0, 11) as $i) {
            $month = Carbon::now()->subMonths(11 - $i)->format('Y-m');
            $months->push($month);

            $monthly = $monthlyData->get($month, collect());

            $income = $monthly->where('type', 'income')->sum('amount');
            $expense = $monthly->where('type', 'expense')->sum('amount');

            $incomeData->push($income);
            $expenseData->push($expense);
        }

        // Ambil 5 transaksi terbaru
        $recentTransactions = $user->transactions()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'labels' => $pengeluaran->keys(),
            'data' => $pengeluaran->values(),
            'months' => $months,
            'incomeData' => $incomeData,
            'expenseData' => $expenseData,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
