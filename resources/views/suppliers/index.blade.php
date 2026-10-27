@extends('layouts.app')
@section('title', 'Supplier')
@section('content')

<div class="card p-3">
    <div class="d-flex justify-content-between mb-3">
        <h6 class="mb-0">Daftar Supplier</h6>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">
            <i class="bi bi-plus"></i> Tambah
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Status</th>
                    <th width="130">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td>{{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td>
                    <td>{{ $s->name }}</td>
                    <td>
                        {{ $s->phone }}
                        @if($s->whatsapp)
                            <br><small class="text-muted"><i class="bi bi-whatsapp"></i> {{ $s->whatsapp }}</small>
                        @endif
                    </td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->address }}</td>
                    <td>
                        @if($s->status)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit{{ $s->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('suppliers.destroy', $s) }}" method="POST"
                              class="d-inline" onsubmit="return confirm('Hapus supplier ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">Belum ada supplier</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $suppliers->links() }}</div>
</div>

{{-- ================== MODAL TAMBAH ================== --}}
<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('suppliers.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title">Tambah Supplier</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="row g-2">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control">
                    </div>
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
                <div class="form-check form-switch">
                    <input type="checkbox" name="status" value="1"
                           class="form-check-input" id="statusAdd" checked>
                    <label class="form-check-label" for="statusAdd">Aktif</label>
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
@foreach($suppliers as $s)
<div class="modal fade" id="modalEdit{{ $s->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('suppliers.update', $s) }}" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h6 class="modal-title">Edit Supplier — {{ $s->name }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $s->name }}" required>
                </div>
                <div class="row g-2">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ $s->phone }}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="{{ $s->whatsapp }}">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $s->email }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="2">{{ $s->address }}</textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="2">{{ $s->notes }}</textarea>
                </div>
                <div class="form-check form-switch">
                    <input type="checkbox" name="status" value="1"
                           class="form-check-input"
                           id="statusEdit{{ $s->id }}"
                           @checked($s->status)>
                    <label class="form-check-label" for="statusEdit{{ $s->id }}">Aktif</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection