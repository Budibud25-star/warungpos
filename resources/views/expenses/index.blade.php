@extends('layouts.app')
@section('title', 'Pengeluaran')
@section('content')
<div class="card p-3 mb-3">
    <h6>Tambah Pengeluaran</h6>
    <form action="{{ route('expenses.store') }}" method="POST" class="row g-2">
        @csrf
        <div class="col-md-3">
            <select name="expense_category_id" class="form-select form-select-sm" required>
                <option value="">- Kategori -</option>
                @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required></div>
        <div class="col-md-2"><input type="number" name="amount" class="form-control form-control-sm" placeholder="Nominal" min="0" required></div>
        <div class="col-md-4"><input type="text" name="description" class="form-control form-control-sm" placeholder="Deskripsi"></div>
        <div class="col-md-1"><button class="btn btn-sm btn-primary w-100">Simpan</button></div>
    </form>
</div>

<div class="card p-3">
    <h6>Daftar Pengeluaran</h6>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light"><tr><th>No</th><th>Tanggal</th><th>Kategori</th><th>Nominal</th><th>Deskripsi</th><th>User</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($expenses as $e)
                <tr>
                    <td>{{ $e->invoice_number }}</td>
                    <td>{{ $e->date->format('d/m/Y') }}</td>
                    <td>{{ $e->category->name }}</td>
                    <td>{{ format_rupiah($e->amount) }}</td>
                    <td>{{ $e->description }}</td>
                    <td>{{ $e->user->name }}</td>
                    <td>
                        <form action="{{ route('expenses.destroy', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $expenses->links() }}
</div>
@endsection