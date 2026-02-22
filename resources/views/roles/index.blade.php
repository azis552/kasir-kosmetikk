@extends('template.master')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Manajemen Akses</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <a href="{{ route('roles.create') }}" class="btn btn-primary mb-3">Create Role</a>

                    <!-- Form Pencarian -->
<form action="{{ route('roles.index') }}" method="GET" class="d-flex  form-inline mb-3">
    <input autofocus type="text" name="search" class="form-control" placeholder="Search by Role Name..." value="{{ request()->search }}">
    <button type="submit" class="btn btn-primary ml-2">Search</button>
</form>
<div class="table-responsive">
<!-- Tabel Roles -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <a href="{{ route('roles.index', ['sort' => 'name', 'direction' => request()->direction == 'asc' ? 'desc' : 'asc', 'search' => request()->search]) }}">
                    Name
                    @if (request()->sort == 'name')
                        @if (request()->direction == 'asc')
                            <i class="fas fa-arrow-up"></i>
                        @else
                            <i class="fas fa-arrow-down"></i>
                        @endif
                    @endif
                </a>
            </th>
            <th>Permissions</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($roles as $role)
            <tr>
                <td>{{ $role->name }}</td>
                <td>{{ $role->permissions->pluck('name')->implode(', ') }}</td>
                <td>
                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
<!-- Pagination Bootstrap -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                {{ $roles->appends(request()->all())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection
