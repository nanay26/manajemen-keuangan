@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Daftar Transaksi</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

  <form method="GET" class="row g-3 mb-4 align-items-end">
    {{-- Filter Bulan --}}
    <div class="col-md-3">
        <label>Bulan</label>
        <input type="month" name="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
    </div>

    {{-- Filter Jenis Transaksi --}}
    <div class="col-md-3">
        <label>Jenis Transaksi</label>
        <select name="type" class="form-control">
            <option value="">Semua</option>
            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
        </select>
    </div>

    {{-- Filter Kategori --}}
    <div class="col-md-3">
        <label>Kategori</label>
        <select name="category_id" class="form-control">
            <option value="">Semua</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Tombol --}}
    <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Reset</a>
        <a href="{{ route('transactions.exportPdf') }}" class="btn btn-danger ms-auto">Export PDF</a>
    </div>
</form>


    <a href="{{ route('transactions.create') }}" class="btn btn-primary mb-3">
       <i class="fas fa-plus me-1"></i> Tambah Kategori
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($t->date)->format('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $t->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($t->type) }}
                        </span>
                    </td>
                    <td>{{ $t->category->name ?? '-' }}</td>
                    <td>Rp{{ number_format($t->amount, 0, ',', '.') }}</td>
                    <td>{{ $t->description }}</td>
                    <td>
                        <a href="{{ route('transactions.edit', $t) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('transactions.destroy', $t) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin hapus?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
