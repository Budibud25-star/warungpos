@extends('layouts.app')
@section('title', 'Kategori')
@section('content')
<div class="card p-3">
    <div class="d-flex justify-content-between mb-3">
        <h6>Daftar Kategori</h6>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd"><i class="bi bi-plus"></i> Tambah</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light"><tr><th>#</th><th>Nama</th><th>Deskripsi</th><th>Jumlah Produk</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($categories as $c)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->description }}</td>
                    <td>{{ $c->products_count }}</td>
                    <td>{!! $c->status ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' !!}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $c->id }}"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('categories.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="modalEdit{{ $c->id }}">
                    <div class="modal-dialog"><form action="{{ route('categories.update', $c) }}" method="POST" class="modal-content">
                        @csrf @method('PUT')
                        <div class="modal-header"><h6>Edit Kategori</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <div class="mb-2"><label>Nama</label><input type="text" name="name" class="form-control" value="{{ $c->name }}" required></div>
                            <div class="mb-2"><label>Deskripsi</label><textarea name="description" class="form-control">{{ $c->description }}</textarea></div>
                            <div class="form-check"><input type="checkbox" name="status" value="1" class="form-check-input" @checked($c->status)><label class="form-check-label">Aktif</label></div>
                        </div>
                        <div class="modal-footer"><button class="btn btn-primary">Update</button></div>
                    </form></div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $categories->links() }}
</div>

<div class="modal fade" id="modalAdd">
    <div class="modal-dialog"><form action="{{ route('categories.store') }}" method="POST" class="modal-content">
        @csrf
        <div class="modal-header"><h6>Tambah Kategori</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-2"><label>Nama *</label><input type="text" name="name" class="form-control" required></div>
            <div class="mb-2"><label>Deskripsi</label><textarea name="description" class="form-control"></textarea></div>
            <div class="form-check"><input type="checkbox" name="status" value="1" class="form-check-input" checked><label class="form-check-label">Aktif</label></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div>
@endsection