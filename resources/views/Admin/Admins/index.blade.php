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
            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
              <i class="fas fa-plus"></i> Add Admin
            </a>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="adminTable" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th data-orderable="false">Avatar</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th data-orderable="false">Status</th>
                <th>Last Login</th>
                <th>Created At</th>
                <th data-orderable="false">Action</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
<!-- /.content -->

<script>
$(function () {
  var dataTable = $("#adminTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "serverSide": true,
    "ajax": {
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: '{{ route("admin.admins.getRecords") }}',
      type: 'post',
      'data': function(data) {
        data.status = $("#status").val();
      }
    },
    "columns": [
      { "data": 0 },
      { "data": 1 },
      { "data": 2 },
      { "data": 3 },
      { "data": 4 },
      { "data": 5 },
      { "data": 6 },
      { "data": 7 }
    ]
  });
});
</script>

@endsection

