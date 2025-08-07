@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Dashboard</h1>

    {{-- Ringkasan --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-start-success">
                <div class="card-body">
                    <h5>Total Pemasukan</h5>
                    <p class="text-success fs-4 fw-bold">Rp{{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-start-danger">
                <div class="card-body">
                    <h5>Total Pengeluaran</h5>
                    <p class="text-danger fs-4 fw-bold">Rp{{ number_format($totalExpense, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-start-primary">
                <div class="card-body">
                    <h5>Saldo</h5>
                    <p class="fs-4 fw-bold">Rp{{ number_format($balance, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Pengeluaran per Kategori --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Pengeluaran per Kategori</h5>
            <canvas id="expenseChart" height="100"></canvas>
        </div>
    </div>

    {{-- Grafik Bulanan --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Pemasukan vs Pengeluaran (12 Bulan)</h5>
            <canvas id="monthlyChart" height="100"></canvas>
        </div>
    </div>

    {{-- Tabel Transaksi Terbaru --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Transaksi Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentTransactions as $trx)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}</td>
                                <td>{{ $trx->category->name ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $trx->type == 'income' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($trx->type) }}
                                    </span>
                                </td>
                                <td>Rp{{ number_format($trx->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Grafik pengeluaran per kategori
    const ctx = document.getElementById('expenseChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Total Pengeluaran',
                data: {!! json_encode($data) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Grafik pemasukan vs pengeluaran bulanan
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [
                {
                    label: 'Pemasukan',
                    data: {!! json_encode($incomeData) !!},
                    backgroundColor: 'rgba(25, 135, 84, 0.6)',
                },
                {
                    label: 'Pengeluaran',
                    data: {!! json_encode($expenseData) !!},
                    backgroundColor: 'rgba(220, 53, 69, 0.6)',
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
