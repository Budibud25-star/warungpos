<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\CashTransaction;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('category', 'user');
        if ($request->filled('date_from')) $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('date', '<=', $request->date_to);
        $expenses = $query->orderByDesc('date')->paginate(20);
        $categories = ExpenseCategory::all();
        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $inv = 'EXP-' . date('Ymd') . '-' . str_pad(
                Expense::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT
            );
            $data['invoice_number'] = $inv;
            $data['user_id'] = auth()->id();
            $expense = Expense::create($data);

            CashTransaction::create([
                'user_id' => auth()->id(),
                'type' => 'out',
                'amount' => $data['amount'],
                'description' => "Pengeluaran {$inv}",
                'reference_type' => 'expense',
                'reference_id' => $expense->id,
                'date' => $data['date'],
            ]);

            AuditLog::record("Menambah pengeluaran {$inv}", 'expense', $expense->id);
        });

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function destroy(Expense $expense)
    {
        DB::transaction(function () use ($expense) {
            CashTransaction::where('reference_type', 'expense')
                ->where('reference_id', $expense->id)->delete();
            AuditLog::record("Menghapus pengeluaran {$expense->invoice_number}", 'expense', $expense->id);
            $expense->delete();
        });
        return back()->with('success', 'Pengeluaran dihapus.');
    }
}