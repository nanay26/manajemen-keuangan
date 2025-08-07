@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ isset($category) ? 'Edit Kategori' : 'Tambah Kategori' }}</h5>
            </div>

            <div class="card-body">
                <form action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}" method="POST">
                    @csrf
                    @if(isset($category))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" id="name"
                               value="{{ old('name', $category->name ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Jenis</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="income" {{ (old('type', $category->type ?? '') == 'income') ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ (old('type', $category->type ?? '') == 'expense') ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
