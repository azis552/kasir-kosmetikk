@extends('template.master')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Manajemen User</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Create User</a>

                    <!-- Form Pencarian -->
                    <form action="{{ route('users.index') }}" method="GET" class=" d-flex form-inline  mb-3">
                        <input type="text" name="search" class="form-control" placeholder="Search..."
                            value="{{ request()->search }}">
                        <button type="submit" class="btn btn-primary ml-2">Search</button>
                    </form>
<div class="table-responsive">
                    <!-- Tabel Pengguna -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <a
                                        href="{{ route('users.index', ['sort' => 'name', 'direction' => request()->direction == 'asc' ? 'desc' : 'asc', 'search' => request()->search]) }}">
                                        Name
                                        @if (request()->sort == 'name')
                                            @if (request()->direction == 'asc')
                                                <i class="ph ph-arrow-up"></i>
                                            @else
                                                <i class="ph ph-arrow-down"></i>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a
                                        href="{{ route('users.index', ['sort' => 'email', 'direction' => request()->direction == 'asc' ? 'desc' : 'asc', 'search' => request()->search]) }}">
                                        Email
                                        @if (request()->sort == 'email')
                                             @if (request()->direction == 'asc')
                                                <i class="ph ph-arrow-up"></i>
                                            @else
                                                <i class="ph ph-arrow-down"></i>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->roles->pluck('name')->implode(', ') }}</td>
                                    <td>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                            style="display:inline;">
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
                                {{ $users->appends(request()->all())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection
