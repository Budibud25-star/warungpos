@extends('layouts.app')
@section('title', 'User Management')
@section('content')

<div class="card p-3">
    <div class="d-flex justify-content-between mb-3">
        <h6 class="mb-0">Daftar User</h6>
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
                    <th>Email</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th width="130">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($u->photo)
                                <img src="{{ asset('storage/'.$u->photo) }}"
                                     class="rounded-circle" width="32" height="32" style="object-fit:cover">
                            @else
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                     style="width:32px;height:32px;font-size:.8rem">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                            @endif
                            <span>{{ $u->name }}</span>
                        </div>
                    </td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->username }}</td>
                    <td>
                        @php
                            $roleColors = ['admin' => 'primary', 'kasir' => 'info', 'owner' => 'warning'];
                            $color = $roleColors[$u->role->name ?? ''] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucfirst($u->role->name ?? '-') }}</span>
                    </td>
                    <td>
                        @if($u->status == 'active')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit{{ $u->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if($u->id !== auth()->id())
                        <form action="{{ route('users.destroy', $u) }}" method="POST"
                              class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">Belum ada user</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $users->links() }}</div>
</div>

{{-- ================== MODAL TAMBAH ================== --}}
<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('users.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title">Tambah User</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" id="passAdd" required minlength="6">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('passAdd')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>
                <div class="mb-2">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select" required>
                        <option value="">- Pilih Role -</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ ucfirst($r->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" class="form-control">
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
@foreach($users as $u)
<div class="modal fade" id="modalEdit{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('users.update', $u) }}" method="POST"
              class="modal-content" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-header">
                <h6 class="modal-title">Edit User — {{ $u->name }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $u->username }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control"
                               id="passEdit{{ $u->id }}" minlength="6"
                               placeholder="Kosongkan jika tidak diubah">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePass('passEdit{{ $u->id }}')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password.</small>
                </div>
                <div class="mb-2">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select" required>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" @selected($u->role_id == $r->id)>
                                {{ ucfirst($r->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" class="form-control" value="{{ $u->phone }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Foto</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    @if($u->photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$u->photo) }}" class="rounded" style="max-height:60px">
                        </div>
                    @endif
                </div>
                <div class="form-check form-switch">
                    <input type="checkbox" name="status" value="1"
                           class="form-check-input"
                           id="statusEdit{{ $u->id }}"
                           @checked($u->status == 'active')>
                    <label class="form-check-label" for="statusEdit{{ $u->id }}">Aktif</label>
                </div>

                @if($u->id === auth()->id())
                <div class="alert alert-warning py-2 small mt-2 mb-0">
                    <i class="bi bi-info-circle"></i> Ini adalah akun Anda sendiri.
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
function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endpush

@endsection