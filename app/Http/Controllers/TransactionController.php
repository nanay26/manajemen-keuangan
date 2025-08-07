<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller {
public function index(Request $request)
{
    $query = auth()->user()
        ->transactions()
        ->with('category')
        ->latest();

    if ($request->filled('month')) {
        $month = $request->input('month');
        $query->whereYear('date', substr($month, 0, 4))
              ->whereMonth('date', substr($month, 5, 2));
    }

    if ($request->filled('type')) {
        $query->where('type', $request->input('type'));
    }

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->input('category_id'));
    }

    $transactions = $query->get();
    $categories = auth()->user()->categories;

    $categories = auth()->user()->categories;
return view('transactions.index', compact('transactions', 'categories'));

}


    public function create()
    {
        $categories = auth()->user()->categories;
        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Cek apakah kategori milik user
        $category = Category::where('id', $validated['category_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        auth()->user()->transactions()->create($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $categories = auth()->user()->categories;
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Validasi kategori milik user
        Category::where('id', $validated['category_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($request->hasFile('receipt')) {
            if ($transaction->receipt_path) {
                Storage::disk('public')->delete($transaction->receipt_path);
            }
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaksi diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        if ($transaction->receipt_path) {
            Storage::disk('public')->delete($transaction->receipt_path);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaksi dihapus.');
    }

  public function exportPdf()
{
    $transactions = auth()->user()->transactions()->with('category')->latest()->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transactions.pdf', compact('transactions'));

    return $pdf->download('transaksi-keuangan.pdf');
}

    protected function authorizeTransaction(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
