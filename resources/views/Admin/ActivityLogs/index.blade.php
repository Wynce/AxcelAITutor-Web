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
            <h3 class="card-title">Activity Logs</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="mb-3">
              <div class="row">
                <div class="col-md-3">
                  <select name="admin_id" class="form-control">
                    <option value="">All Admins</option>
                    @foreach($admins as $admin)
                      <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                        {{ $admin->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <select name="action" class="form-control">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                      <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                        {{ ucfirst($action) }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2">
                  <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                  <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To Date">
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-primary">Filter</button>
                  <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">Reset</a>
                </div>
              </div>
            </form>

            <table class="table table-bordered table-hover">
              <thead>
              <tr>
                <th>Admin</th>
                <th>Action</th>
                <th>Model</th>
                <th>Description</th>
                <th>IP Address</th>
                <th>Date</th>
              </tr>
              </thead>
              <tbody>
                @forelse($logs as $log)
                <tr>
                  <td>{{ $log->admin ? $log->admin->name : 'System' }}</td>
                  <td><span class="badge badge-info">{{ ucfirst($log->action) }}</span></td>
                  <td>
                    @if($log->model_type)
                      {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                    @else
                      N/A
                    @endif
                  </td>
                  <td>{{ $log->description }}</td>
                  <td>{{ $log->ip_address }}</td>
                  <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center">No activity logs found</td>
                </tr>
                @endforelse
              </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-3">
              {{ $logs->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

