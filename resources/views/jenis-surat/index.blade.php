@extends('layouts.admin')

@section('title', 'Jenis Surat')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Jenis Surat</h5>
        <small class="text-muted">Total: {{ $jenisSurats->total() }} jenis</small>
    </div>
    @can('surat.create')
    <a href="{{ route('jenis-surat.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Jenis Surat
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jenisSurats as $i => $js)
                <tr>
                    <td>{{ $jenisSurats->firstItem() + $i }}</td>
                    <td><code>{{ $js->kode }}</code></td>
                    <td><strong>{{ $js->nama }}</strong></td>
                    <td><small>{{ Str::limit($js->deskripsi, 60) }}</small></td>
                    <td>
                        @if ($js->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @can('surat.edit')
                        <a href="{{ route('jenis-surat.edit', $js) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endcan
                        @can('surat.delete')
                        <form method="POST" action="{{ route('jenis-surat.destroy', $js) }}" class="d-inline"
                              onsubmit="return confirm('Yakin hapus {{ $js->nama }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada jenis surat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($jenisSurats->hasPages())
    <div class="card-footer bg-white">{{ $jenisSurats->links() }}</div>
    @endif
</div>

@endsection