@extends('template.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Log Aktivitas User</h5>
                {{-- Tombol hapus log hari ini --}}
                <form action="{{ route('activity-logs.destroy') }}" method="POST"
                      onsubmit="return confirm('Hapus semua log tanggal {{ $date }}?')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="date" value="{{ $date }}">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash mr-1"></i> Hapus Log Hari Ini
                    </button>
                </form>
            </div>
            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Filter --}}
                <form action="{{ route('activity-logs.index') }}" method="GET"
                      class="d-flex form-inline mb-3 flex-wrap gap-2">
                    <input type="date" name="date" class="form-control"
                           value="{{ $date }}" style="width:160px;">
                    <select name="user_id" class="form-control ml-2" style="width:180px;">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                {{ $userId == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="search" class="form-control ml-2"
                           placeholder="Cari aksi atau deskripsi..."
                           value="{{ $search }}" style="width:220px;">
                    <button type="submit" class="btn btn-primary ml-2">Filter</button>
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary ml-1">Reset</a>
                </form>

                {{-- Summary --}}
                <div class="alert alert-light py-2 mb-3" style="font-size:13px;">
                    Menampilkan <strong>{{ $logs->total() }}</strong> log
                    @if($date) pada <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong> @endif
                    @if($userId) untuk user <strong>{{ $users->find($userId)?->name }}</strong> @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th style="width:160px;">Waktu</th>
                                <th style="width:120px;">User</th>
                                <th style="width:80px;">Method</th>
                                <th>Deskripsi</th>
                                <th>Path</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            @php
                                $parts  = explode(' ', $log->action, 2);
                                $method = $parts[0] ?? '-';
                                $path   = $parts[1] ?? '-';
                                $methodColor = match($method) {
                                    'GET'    => 'badge-info',
                                    'POST'   => 'badge-success',
                                    'PUT'    => 'badge-warning',
                                    'DELETE' => 'badge-danger',
                                    default  => 'badge-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="text-muted" style="font-size:12px;">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i:s') }}
                                </td>
                                <td>{{ $log->userDetail?->name ?? 'Unknown' }}</td>
                                <td class="text-center">
                                    {{ $method }}
                                </td>
                                <td>{{ $log->details }}</td>
                                <td class="text-muted" style="font-size:12px;">
                                    <code>{{ $path }}</code>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Tidak ada log aktivitas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $logs->appends(request()->all())->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection