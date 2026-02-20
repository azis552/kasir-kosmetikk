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
                    <a href="{{ route('users.index') }}" class="btn btn-warning mb-3">Kembali</a>
                    <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST">
                        @csrf
                        @if (isset($user))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $user->name ?? '') }}" required>
                        </div>



                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $user->email ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                {{ !isset($user) ? 'required' : '' }}>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" {{ !isset($user) ? 'required' : '' }}>
                        </div>

                        <div class="mb-3">
                            <label for="roles" class="form-label">Roles</label>
                            <div id="roles" class="form-check">
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                            class="form-check-input"
                                            {{ isset($user) && $user->hasRole($role->name) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="roles">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update' : 'Create' }}
                            User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
    </div>
@endsection
