@extends('Admin.layout.template')
@section('middlecontent')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <form method="POST" action="{{ route('admin.roles.store') }}" novalidate>
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>
                <div class="text-danger">{{ $errors->first('name') }}</div>
              </div>

              <div class="form-group">
                <label>Slug</label>
                <input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug') }}" placeholder="Auto-generated if left blank">
                <small class="form-text text-muted">Leave blank to auto-generate from name</small>
                <div class="text-danger">{{ $errors->first('slug') }}</div>
              </div>

              <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="description" id="description" rows="3">{{ old('description') }}</textarea>
                <div class="text-danger">{{ $errors->first('description') }}</div>
              </div>

              <div class="form-group">
                <label>Permissions</label>
                @foreach($permissions as $group => $groupPermissions)
                  <div class="card mb-3">
                    <div class="card-header">
                      <strong>{{ ucfirst($group ?? 'Other') }}</strong>
                    </div>
                    <div class="card-body">
                      @foreach($groupPermissions as $permission)
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="permissions[]" 
                                 value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                 {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                          <label class="form-check-label" for="permission_{{ $permission->id }}">
                            {{ $permission->name }}
                            @if($permission->description)
                              <small class="text-muted">- {{ $permission->description }}</small>
                            @endif
                          </label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endforeach
                <div class="text-danger">{{ $errors->first('permissions') }}</div>
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Save
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-danger">
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

