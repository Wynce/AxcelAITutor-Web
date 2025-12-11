@extends('Admin.layout.template')
@section('middlecontent')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <form method="POST" action="{{ route('admin.admins.store') }}" class="needs-validation" novalidate>
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <input type="hidden" name="id" id="id" value="{{ $id }}">

              <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $admin->name) }}" required>
                <div class="text-danger">{{ $errors->first('name') }}</div>
              </div>

              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $admin->email) }}" required>
                <div class="text-danger">{{ $errors->first('email') }}</div>
              </div>

              <div class="form-group">
                <label>Password <small>(Leave blank to keep current password)</small></label>
                <input type="password" class="form-control" name="password" id="password">
                <div class="text-danger">{{ $errors->first('password') }}</div>
              </div>

              <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
              </div>

              <div class="form-group">
                <label>Role</label>
                <select name="role_id" class="form-control select2" id="role_id">
                  <option value="">Select Role</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $admin->role_id) == $role->id ? 'selected' : '' }}>
                      {{ $role->name }}
                    </option>
                  @endforeach
                </select>
                <div class="text-danger">{{ $errors->first('role_id') }}</div>
              </div>

              <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" id="status">
                  <option value="active" {{ old('status', $admin->status) == 'active' ? 'selected' : '' }}>Active</option>
                  <option value="inactive" {{ old('status', $admin->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('admin.admins.index') }}" class="btn btn-danger">
                  <i class="fas fa-times"></i> Cancel
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
@endsection

