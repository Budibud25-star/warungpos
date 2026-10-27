@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')

<div class="row g-3">
    {{-- Kolom Informasi Akun --}}
    <div class="col-md-5">
        <div class="card p-4 h-100">
            <h5 class="mb-3 border-bottom pb-2">Informasi Akun</h5>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="130"><strong>Nama Lengkap</strong></td>
                    <td>: {{ auth()->user()->name }}</td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>: {{ auth()->user()->email }}</td>
                </tr>
                <tr>
                    <td><strong>Username</strong></td>
                    <td>: {{ auth()->user()->username ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Role</strong></td>
                    <td>: <span class="badge bg-primary">{{ ucfirst(auth()->user()->role->name ?? '-') }}</span></td>
                </tr>
                <tr>
                    <td><strong>Status</strong></td>
                    <td>: 
                        @if(auth()->user()->status == 'active')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                </tr>
            </table>
            <div class="alert alert-info py-2 small mb-0 mt-auto">
                <i class="bi bi-info-circle"></i> Anda dapat mengubah password secara berkala untuk menjaga keamanan akun.
            </div>
        </div>
    </div>

    {{-- Kolom Ubah Password --}}
    <div class="col-md-7">
        <div class="card p-4 h-100">
            <h5 class="mb-3 border-bottom pb-2">Ubah Password</h5>
            
            <form action="{{ route('profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" id="passLama" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passLama')"><i class="bi bi-eye"></i></button>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="passBaru" required minlength="6">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passBaru')"><i class="bi bi-eye"></i></button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="text-muted">Minimal 6 karakter.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" class="form-control" id="passKonfirm" required minlength="6">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passKonfirm')"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save"></i> Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endpush

@endsection