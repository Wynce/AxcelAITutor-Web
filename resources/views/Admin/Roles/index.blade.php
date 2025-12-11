@extends('Admin.layout.template')
@section('middlecontent')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          @include('Admin.alert_messages')

          <div class="card-header">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
              <i class="fas fa-plus"></i> Add Role
            </a>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-bordered table-hover">
              <thead>
              <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th>Permissions Count</th>
                <th>Admins Count</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
              </thead>
              <tbody>
                @forelse($roles as $role)
                <tr>
                  <td>{{ $role->name }}</td>
                  <td><code>{{ $role->slug }}</code></td>
                  <td>{{ $role->description ?? 'N/A' }}</td>
                  <td>{{ $role->rolePermissions->count() }}</td>
                  <td>{{ $role->admins->count() }}</td>
                  <td>
                    @if($role->is_active)
                      <span class="badge badge-success">Active</span>
                    @else
                      <span class="badge badge-danger">Inactive</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('admin.roles.edit', base64_encode($role->id)) }}" class="btn btn-sm btn-primary">
                      <i class="fas fa-edit"></i>
                    </a>
                    @if($role->slug !== 'super-admin')
                      <a href="{{ route('admin.roles.delete', base64_encode($role->id)) }}" 
                         class="btn btn-sm btn-danger" 
                         onclick="return confirm('Are you sure?')">
                        <i class="fas fa-trash"></i>
                      </a>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center">No roles found</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

