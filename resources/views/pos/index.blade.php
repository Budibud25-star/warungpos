@extends('layouts.app')
@section('title', 'Kasir / POS')
@section('content')

<div class="row g-3">
    {{-- KIRI: Daftar Produk --}}
    <div class="col-md-7">
        <div class="card p-3">
            <div class="row g-2 mb-3">
                <div class="col-md-8">
                    <input type="text" id="search" class="form-control" placeholder="Cari produk / barcode..." autofocus>
                </div>
                <div class="col-md-4">
                    <select id="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="productList" class="row g-2" style="max-height: 70vh; overflow-y: auto;"></div>
        </div>
    </div>

    {{-- KANAN: Keranjang --}}
    <div class="col-md-5">
        <div class="card p-3" style="position: sticky; top: 10px;">
            <h6>Keranjang Belanja</h6>

            {{-- Pelanggan --}}
            <div class="mb-2">
                <label class="small">Pelanggan</label>
                <select id="customer" class="form-select form-select-sm">
                    <option value="">Pelanggan Umum (Non-Member)</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}"
                            data-member="{{ $c->is_member ? '1' : '0' }}"
                            data-discount="{{ $c->discount_percent }}"
                            data-name="{{ $c->name }}">
                        {{ $c->name }}@if($c->is_member) - Member ({{ $c->discount_percent }}%)@endif
                    </option>
                    @endforeach
                </select>
                <div id="memberInfo" class="small text-success mt-1 d-none">
                    <i class="bi bi-award"></i> Member: <strong id="memberName"></strong>
                    (diskon <span id="memberDisc"></span>%)
                </div>
            </div>

            {{-- Keranjang --}}
            <div id="cart" class="mb-3" style="max-height: 40vh; overflow-y: auto;"></div>

            {{-- Ringkasan --}}
            <div class="border-top pt-2 small">
                <div class="d-flex justify-content-between">
                    <span>Subtotal</span><strong id="subtotal">Rp 0</strong>
                </div>

                <div class="d-flex justify-content-between text-success d-none" id="memberDiscRow">
                    <span>Diskon Member (<span id="memberDiscPct">0</span>%)</span>
                    <strong id="memberDiscAmt">-Rp 0</strong>
                </div>

                <div class="d-flex justify-content-between align-items-center my-1">
                    <span>Diskon Tambahan</span>
                    <input type="number" id="discount" class="form-control form-control-sm" style="width: 120px;" value="0" min="0">
                </div>

                <div class="d-flex justify-content-between">
                    <span>Pajak</span><strong id="tax">Rp 0</strong>
                </div>

                <div class="d-flex justify-content-between fs-5 mt-2">
                    <span>Total</span><strong id="total">Rp 0</strong>
                </div>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="mt-3">
                <label class="small">Metode Pembayaran</label>
                <select id="payment_method" class="form-select form-select-sm">
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                    <option value="qris">QRIS</option>
                    <option value="debit">Debit</option>
                    <option value="kredit">Kredit</option>
                    <option value="ewallet">E-Wallet</option>
                </select>
            </div>

            <div class="mt-2" id="cashRow">
                <label class="small">Uang Dibayar</label>
                <input type="number" id="paid" class="form-control form-control-sm" value="0" min="0">
                <small class="text-muted">Kembalian: <strong id="change">Rp 0</strong></small>
            </div>

            <button id="btnPay" class="btn btn-primary w-100 mt-3 py-2">
                <i class="bi bi-check-circle"></i> Proses Pembayaran
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const TAX_ENABLED = {{ setting('tax_enabled') == '1' ? 'true' : 'false' }};
const TAX_PCT = {{ (int) setting('tax_percentage', 10) }};
let cart = [];
let memberDiscountPercent = 0;

function rupiah(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n)); }

async function loadProducts() {
    const s = document.getElementById('search').value;
    const c = document.getElementById('category').value;
    const res = await fetch(`{{ route('pos.products.search') }}?search=${encodeURIComponent(s)}&category_id=${c}`);
    const data = await res.json();
    const list = document.getElementById('productList');
    list.innerHTML = data.map(p => `
        <div class="col-md-4 col-6">
            <div class="card p-2 h-100" style="cursor:pointer" onclick='addToCart(${JSON.stringify(p).replace(/'/g, "&apos;")})'>
                <div class="small fw-bold">${p.name}</div>
                <div class="small text-primary">${rupiah(p.selling_price)}</div>
                <div class="small text-muted">Stok: ${p.stock}</div>
            </div>
        </div>
    `).join('');
}

function addToCart(p) {
    const idx = cart.findIndex(i => i.product_id === p.id);
    if (idx >= 0) cart[idx].quantity++;
    else cart.push({ product_id: p.id, name: p.name, price: +p.selling_price, quantity: 1, discount: 0, stock: p.stock });
    renderCart();
}

function renderCart() {
    const el = document.getElementById('cart');
    if (!cart.length) {
        el.innerHTML = '<p class="text-muted small">Keranjang kosong</p>';
        calcTotal();
        return;
    }
    el.innerHTML = cart.map((i, idx) => `
        <div class="border-bottom py-2">
            <div class="d-flex justify-content-between">
                <strong class="small">${i.name}</strong>
                <button class="btn btn-sm btn-link text-danger p-0" onclick="removeItem(${idx})">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary" onclick="changeQty(${idx}, -1)">-</button>
                    <span class="btn btn-light disabled">${i.quantity}</span>
                    <button class="btn btn-outline-secondary" onclick="changeQty(${idx}, 1)">+</button>
                </div>
                <span class="small">${rupiah(i.price * i.quantity)}</span>
            </div>
        </div>
    `).join('');
    calcTotal();
}

function changeQty(idx, delta) {
    cart[idx].quantity += delta;
    if (cart[idx].quantity < 1) cart.splice(idx, 1);
    renderCart();
}

function removeItem(idx) { cart.splice(idx, 1); renderCart(); }

function calcTotal() {
    const subtotal = cart.reduce((s, i) => s + (i.price * i.quantity), 0);

    // Diskon member
    const memberDisc = subtotal * (memberDiscountPercent / 100);

    // Diskon manual
    const manualDisc = +document.getElementById('discount').value || 0;

    // Total diskon
    const totalDisc = memberDisc + manualDisc;

    // Pajak
    const tax = TAX_ENABLED ? (subtotal - totalDisc) * TAX_PCT / 100 : 0;
    const total = subtotal - totalDisc + tax;

    document.getElementById('subtotal').textContent = rupiah(subtotal);
    document.getElementById('memberDiscAmt').textContent = '-' + rupiah(memberDisc);
    document.getElementById('tax').textContent = rupiah(tax);
    document.getElementById('total').textContent = rupiah(total);

    const paid = +document.getElementById('paid').value || 0;
    document.getElementById('change').textContent = rupiah(Math.max(0, paid - total));

    return { subtotal, discount: totalDisc, tax, total };
}

// Pilih pelanggan → aktifkan diskon member
document.getElementById('customer').addEventListener('change', function (e) {
    const opt = e.target.selectedOptions[0];
    const isMember = opt.dataset.member === '1';
    const disc = parseFloat(opt.dataset.discount || 0);

    if (isMember && disc > 0) {
        memberDiscountPercent = disc;
        document.getElementById('memberInfo').classList.remove('d-none');
        document.getElementById('memberName').textContent = opt.dataset.name;
        document.getElementById('memberDisc').textContent = disc;
        document.getElementById('memberDiscRow').classList.remove('d-none');
        document.getElementById('memberDiscPct').textContent = disc;
    } else {
        memberDiscountPercent = 0;
        document.getElementById('memberInfo').classList.add('d-none');
        document.getElementById('memberDiscRow').classList.add('d-none');
    }
    calcTotal();
});

document.getElementById('search').addEventListener('input', loadProducts);
document.getElementById('category').addEventListener('change', loadProducts);
document.getElementById('discount').addEventListener('input', calcTotal);
document.getElementById('paid').addEventListener('input', calcTotal);
document.getElementById('payment_method').addEventListener('change', e => {
    document.getElementById('cashRow').style.display = e.target.value === 'cash' ? 'block' : 'none';
});

document.getElementById('btnPay').addEventListener('click', async () => {
    if (!cart.length) return Swal.fire('Keranjang kosong', '', 'warning');
    const t = calcTotal();
    const paid = +document.getElementById('paid').value || 0;
    const method = document.getElementById('payment_method').value;

    if (method === 'cash' && paid < t.total) {
        return Swal.fire('Gagal', 'Uang dibayar kurang dari total', 'error');
    }

    const payload = {
        items: cart.map(i => ({ product_id: i.product_id, quantity: i.quantity, discount: i.discount })),
        discount: +document.getElementById('discount').value || 0, // hanya manual
        payment_method: method,
        paid_amount: paid,
        customer_id: document.getElementById('customer').value || null,
        _token: '{{ csrf_token() }}',
    };

    const res = await fetch('{{ route('pos.sales.store') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
        await Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            html: `No. Transaksi: <b>${data.invoice_number}</b>`,
            confirmButtonText: 'Cetak Struk',
            showCancelButton: true,
            cancelButtonText: 'Transaksi Baru'
        }).then(r => {
            if (r.isConfirmed) window.open(`/pos/receipt/${data.sale_id}`, '_blank');
        });

        cart = [];
        memberDiscountPercent = 0;
        document.getElementById('discount').value = 0;
        document.getElementById('paid').value = 0;
        document.getElementById('search').value = '';
        document.getElementById('customer').value = '';
        document.getElementById('memberInfo').classList.add('d-none');
        document.getElementById('memberDiscRow').classList.add('d-none');
        renderCart();
        loadProducts();
    } else {
        Swal.fire('Gagal', data.message, 'error');
    }
});

loadProducts();
renderCart();
</script>
@endpush
@endsection