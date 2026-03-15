@extends('template.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Buat Role Baru</h5>
                <a href="{{ route('roles.index') }}" class="btn btn-sm btn-warning">
                    <i class="ph ph-arrow-left mr-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Role</label>
                        <input type="text" name="name" class="form-control" autofocus
                               value="{{ old('name') }}" placeholder="Contoh: supervisor" required>
                    </div>

                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <label class="form-label fw-bold mb-0">Permissions</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-success mr-1"
                                    onclick="toggleAll(true)">
                                <i class="ph ph-check-square"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="toggleAll(false)">
                                <i class="ph ph-square"></i> Hapus Semua
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        @foreach ($groupedPermissions as $moduleName => $permissions)
                        @php $groupId = Str::slug($moduleName); @endphp
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border h-100">
                                <div class="card-header py-2 d-flex justify-content-between align-items-center"
                                     style="background:#f8f9fa;">
                                    <strong style="font-size:0.85rem;">{{ $moduleName }}</strong>
                                    <div>
                                        <button type="button" class="btn btn-sm py-0 px-1 btn-outline-secondary"
                                                onclick="toggleGroup('{{ $groupId }}', true)" title="Pilih semua">✓</button>
                                        <button type="button" class="btn btn-sm py-0 px-1 btn-outline-secondary ml-1"
                                                onclick="toggleGroup('{{ $groupId }}', false)" title="Hapus semua">✗</button>
                                    </div>
                                </div>
                                <div class="card-body py-2 px-3" id="group-{{ $groupId }}">
                                    @foreach ($permissions as $permission)
                                    <div class="form-check mb-1">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               class="form-check-input perm-checkbox"
                                               id="perm_{{ $permission->id }}"
                                               {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="font-size:0.82rem;"
                                               for="perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <span id="checkedCount">0</span> permission dipilih
                        </small>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-plus mr-1"></i> Buat Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateCount() {
    document.getElementById('checkedCount').textContent =
        document.querySelectorAll('.perm-checkbox:checked').length;
}
function toggleAll(checked) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = checked);
    updateCount();
}
function toggleGroup(groupId, checked) {
    document.querySelectorAll('#group-' + groupId + ' .perm-checkbox')
            .forEach(cb => cb.checked = checked);
    updateCount();
}
document.addEventListener('DOMContentLoaded', function () {
    updateCount();
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.addEventListener('change', updateCount);
    });
});
</script>
@endsection