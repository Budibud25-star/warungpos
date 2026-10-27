@extends('layouts.app')
@section('title', 'Tambah Pembelian')
@section('content')
<div class="card p-3">
    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <label>Supplier *</label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">- Pilih -</option>
                    @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4"><label>Tanggal *</label><input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
            <div class="col-md-4"><label>Catatan</label><input type="text" name="notes" class="form-control"></div>
        </div>

        <table class="table" id="itemsTable">
            <thead><tr><th>Produk</th><th>Harga Beli</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
            <tbody></tbody>
        </table>
        <button type="button" class="btn btn-sm btn-outline-primary mb-3" onclick="addRow()"><i class="bi bi-plus"></i> Tambah Item</button>

        <div class="row g-2 mb-3">
            <div class="col-md-4"><label>Diskon</label><input type="number" name="discount" id="discount" class="form-control" value="0" min="0" oninput="calc()"></div>
            <div class="col-md-4"><label>Uang Dibayar</label><input type="number" name="paid_amount" id="paid" class="form-control" value="0" min="0"></div>
            <div class="col-md-4"><label>Total</label><input type="text" id="grand" class="form-control" readonly></div>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

@push('scripts')
<script>
const products = @json($products);
function addRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="items[][product_id]" class="form-select form-select-sm" required onchange="fillPrice(this)">
                <option value="">- Pilih Produk -</option>
                ${products.map(p => `<option value="${p.id}" data-price="${p.purchase_price}">${p.name}</option>`).join('')}
            </select>
        </td>
        <td><input type="number" name="items[][price]" class="form-control form-control-sm price" value="0" min="0" required oninput="calc()"></td>
        <td><input type="number" name="items[][quantity]" class="form-control form-control-sm qty" value="1" min="1" required oninput="calc()"></td>
        <td class="subtotal">Rp 0</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); calc();"><i class="bi bi-x"></i></button></td>
    `;
    tbody.appendChild(tr);
}
function fillPrice(sel) {
    const price = sel.selectedOptions[0].dataset.price || 0;
    sel.closest('tr').querySelector('.price').value = price;
    calc();
}
function calc() {
    let sub = 0;
    document.querySelectorAll('#itemsTable tbody tr').forEach(tr => {
        const p = +tr.querySelector('.price').value || 0;
        const q = +tr.querySelector('.qty').value || 0;
        const s = p * q;
        sub += s;
        tr.querySelector('.subtotal').textContent = 'Rp ' + s.toLocaleString('id-ID');
    });
    const disc = +document.getElementById('discount').value || 0;
    document.getElementById('grand').value = 'Rp ' + (sub - disc).toLocaleString('id-ID');
}
addRow();
</script>
@endpush
@endsection