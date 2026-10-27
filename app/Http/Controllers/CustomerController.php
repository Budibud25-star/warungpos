<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderByDesc('id')->paginate(20);
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string|max:20',
            'whatsapp'         => 'nullable|string|max:20',
            'email'            => 'nullable|email',
            'address'          => 'nullable|string',
            'notes'            => 'nullable|string',
            'is_member'        => 'nullable|boolean',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $last = Customer::withTrashed()->count() + 1;
        $data['code'] = 'CUST-' . str_pad($last, 3, '0', STR_PAD_LEFT);
        $data['is_member'] = $request->boolean('is_member', false);
        $data['discount_percent'] = $data['is_member'] ? ($data['discount_percent'] ?? 0) : 0;
        $data['member_since'] = $data['is_member'] ? now() : null;

        $c = Customer::create($data);
        AuditLog::record("Menambah pelanggan {$c->name}", 'customer', $c->id);
        return back()->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string|max:20',
            'whatsapp'         => 'nullable|string|max:20',
            'email'            => 'nullable|email',
            'address'          => 'nullable|string',
            'notes'            => 'nullable|string',
            'is_member'        => 'nullable|boolean',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $data['is_member'] = $request->boolean('is_member', false);
        $data['discount_percent'] = $data['is_member'] ? ($data['discount_percent'] ?? 0) : 0;

        if ($data['is_member'] && !$customer->is_member) {
            $data['member_since'] = now();
        } elseif (!$data['is_member']) {
            $data['member_since'] = null;
        }

        $customer->update($data);
        AuditLog::record("Mengubah pelanggan {$customer->name}", 'customer', $customer->id);
        return back()->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $name = $customer->name;
        $customer->delete();
        AuditLog::record("Menghapus pelanggan {$name}", 'customer', $customer->id);
        return back()->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function show(Customer $customer)
    {
        $sales = $customer->sales()->with('user')->orderByDesc('date')->paginate(20);
        $total = $customer->sales()->where('status', 'completed')->sum('grand_total');
        return view('customers.show', compact('customer', 'sales', 'total'));
    }
}