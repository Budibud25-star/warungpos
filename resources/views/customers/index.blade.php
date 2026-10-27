@extends('layouts.app')
@section('title', 'Pelanggan')
@section('content')

<div class="card p-3">
    <div class="d-flex justify-content-between mb-3">
        <h6 class="mb-0">Daftar Pelanggan</h6>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">
            <i class="bi bi-plus"></i> Tambah
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th width="130">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                <tr>
                    <td>{{ $c->code }}</td>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>{{ $c->email }}</td>
                    <td>
                        @if($c->is_member)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-award"></i> Member {{ $c->discount_percent }}%
                            </span>
                        @else
                            <span class="badge bg-secondary">Non-Member</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('customers.show', $c) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i>
                        </a>
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit{{ $c->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('customers.destroy', $c) }}" method="POST"
                              class="d-inline" onsubmit="return confirm('Hapus pelanggan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">Belum ada pelanggan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $customers->links() }}</div>
</div>

{{-- ================== MODAL TAMBAH ================== --}}
<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('customers.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title">Tambah Pelanggan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>

                <hr class="my-3">
                <h6 class="small text-muted mb-2">STATUS MEMBER</h6>
                <div class="form-check form-switch mb-3">
                    <input type="checkbox" name="is_member" value="1"
                           class="form-check-input memberCheck" id="memberAdd">
                    <label class="form-check-label" for="memberAdd">Jadikan Member</label>
                </div>
                <div class="mb-2 discountField" style="display:none">
                    <label class="form-label">Diskon Member (%)</label>
                    <div class="input-group">
                        <input type="number" name="discount_percent" class="form-control"
                               value="0" min="0" max="100" step="0.5">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-muted">Contoh: 5 = diskon 5% setiap pembelian</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ================== MODAL EDIT (di luar tabel) ================== --}}
@foreach($customers as $c)
<div class="modal fade" id="modalEdit{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('customers.update', $c) }}" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h6 class="modal-title">Edit Pelanggan — {{ $c->name }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $c->name }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" class="form-control" value="{{ $c->phone }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control" value="{{ $c->whatsapp }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $c->email }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="2">{{ $c->address }}</textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="2">{{ $c->notes }}</textarea>
                </div>

                <hr class="my-3">
                <h6 class="small text-muted mb-2">STATUS MEMBER</h6>
                <div class="form-check form-switch mb-3">
                    <input type="checkbox" name="is_member" value="1"
                           class="form-check-input memberCheck"
                           id="memberEdit{{ $c->id }}"
                           @checked($c->is_member)>
                    <label class="form-check-label" for="memberEdit{{ $c->id }}">Jadikan Member</label>
                </div>
                <div class="mb-2 discountField" style="display:{{ $c->is_member ? 'block' : 'none' }}">
                    <label class="form-label">Diskon Member (%)</label>
                    <div class="input-group">
                        <input type="number" name="discount_percent" class="form-control"
                               value="{{ $c->discount_percent }}" min="0" max="100" step="0.5">
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                @if($c->is_member && $c->member_since)
                <div class="alert alert-info py-2 small mt-2 mb-0">
                    <i class="bi bi-info-circle"></i>
                    Member sejak {{ $c->member_since->format('d/m/Y') }}
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@push('scripts')
<script>
// Toggle form diskon saat checkbox member di-klik
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('memberCheck')) {
        const form = e.target.closest('form');
        const discField = form.querySelector('.discountField');
        if (discField) {
            discField.style.display = e.target.checked ? 'block' : 'none';
        }
    }
});
</script>
@endpush

@endsection