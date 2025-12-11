@extends('Admin.layout.template')
@section('middlecontent')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>{{ $totalUsers ?? 0 }}</h3>
            <p>Total Users</p>
          </div>
          <div class="icon">
            <i class="fas fa-users"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
          <div class="inner">
            <h3>{{ $totalStudents ?? 0 }}</h3>
            <p>Students</p>
          </div>
          <div class="icon">
            <i class="fas fa-user-graduate"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
          <div class="inner">
            <h3>{{ $totalParents ?? 0 }}</h3>
            <p>Parents</p>
          </div>
          <div class="icon">
            <i class="fas fa-user-friends"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3>{{ $totalTeachers ?? 0 }}</h3>
            <p>Teachers</p>
          </div>
          <div class="icon">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          @include('Admin.alert_messages')

          <div class="card-header">
            <div class="row">
              <div class="col-md-6">
                <h3 class="card-title">Users Management</h3>
              </div>
              <div class="col-md-6 text-right">
                <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                  <i class="fas fa-plus"></i> Add User
                </a>
                <button type="button" class="btn btn-info" id="exportBtn">
                  <i class="fas fa-download"></i> Export CSV
                </button>
                <button type="button" class="btn btn-warning" id="bulkActionBtn" style="display:none;">
                  <i class="fas fa-tasks"></i> Bulk Action
                </button>
              </div>
            </div>
          </div>

          <!-- Filters -->
          <div class="card-body">
            <form id="filterForm" class="mb-3">
              <div class="row">
                <div class="col-md-2">
                  <select name="user_type" id="user_type" class="form-control">
                    <option value="">All Types</option>
                    <option value="student">Student</option>
                    <option value="parent">Parent</option>
                    <option value="teacher">Teacher</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <select name="status" id="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="blocked">Blocked</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <input type="text" name="country" id="country" class="form-control" placeholder="Country">
                </div>
                <div class="col-md-2">
                  <input type="date" name="date_from" id="date_from" class="form-control" placeholder="From Date">
                </div>
                <div class="col-md-2">
                  <input type="date" name="date_to" id="date_to" class="form-control" placeholder="To Date">
                </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-primary" id="applyFilters"><i class="fas fa-filter"></i> Filter</button>
                  <button type="button" class="btn btn-secondary" id="resetFilters"><i class="fas fa-undo"></i></button>
                </div>
              </div>
            </form>

            <!-- Bulk Action Form -->
            <form id="bulkActionForm" style="display:none;" class="mb-3">
              <div class="row">
                <div class="col-md-4">
                  <select name="bulk_action" id="bulk_action" class="form-control">
                    <option value="">Select Action</option>
                    <option value="activate">Activate Selected</option>
                    <option value="deactivate">Deactivate Selected</option>
                    <option value="delete">Delete Selected</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-danger">Apply</button>
                </div>
              </div>
            </form>

            <table id="userTable" class="table table-bordered table-hover table-striped">
              <thead>
              <tr>
                <th data-orderable="false" width="30"><input type="checkbox" id="selectAll"></th>
                <th data-orderable="false" width="60">Image</th>
                <th>Name</th>
                <th>Email</th>
                <th width="80">Type</th>
                <th>Country</th>
                <th width="60">Chats</th>
                <th data-orderable="false" width="60">Status</th>
                <th data-orderable="false" width="120">Action</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('script')
<script>
$(function () {
  var dataTable = $("#userTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "serverSide": true,
    "columnDefs": [
      {
        targets: [0],
        className: "text-center",
        orderable: false
      }
    ],
    "ajax": {
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: '{{ route("adminUserGetRecords") }}',
      'data': function(data) {
        data.user_type = $("#user_type").val();
        data.status = $("#status").val();
        data.country = $("#country").val();
        data.date_from = $("#date_from").val();
        data.date_to = $("#date_to").val();
      },
      type: 'post',
    },
  });

  // Apply filters
  $('#applyFilters').click(function() {
    dataTable.draw();
  });

  // Reset filters
  $('#resetFilters').click(function() {
    $('#filterForm')[0].reset();
    dataTable.draw();
  });

  // Export functionality
  $('#exportBtn').click(function() {
    var params = new URLSearchParams();
    if ($('#user_type').val()) params.append('user_type', $('#user_type').val());
    if ($('#status').val()) params.append('status', $('#status').val());
    if ($('#country').val()) params.append('country', $('#country').val());
    if ($('#date_from').val()) params.append('date_from', $('#date_from').val());
    if ($('#date_to').val()) params.append('date_to', $('#date_to').val());
    
    window.location.href = '{{ route("admin.users.export") }}?' + params.toString();
  });

  // Select all checkbox
  $('#selectAll').change(function() {
    $('.user-checkbox').prop('checked', this.checked);
    toggleBulkActionBtn();
  });

  // Toggle bulk action button
  function toggleBulkActionBtn() {
    var checked = $('.user-checkbox:checked').length;
    if (checked > 0) {
      $('#bulkActionBtn').show();
      $('#bulkActionForm').show();
    } else {
      $('#bulkActionBtn').hide();
      $('#bulkActionForm').hide();
    }
  }

  // Bulk action form submit
  $('#bulkActionForm').submit(function(e) {
    e.preventDefault();
    var action = $('#bulk_action').val();
    var selectedIds = [];
    
    $('.user-checkbox:checked').each(function() {
      selectedIds.push($(this).val());
    });

    if (!action || selectedIds.length === 0) {
      alert('Please select an action and at least one user');
      return;
    }

    if (confirm('Are you sure you want to ' + action + ' ' + selectedIds.length + ' user(s)?')) {
      $.ajax({
        url: '{{ route("admin.users.bulkAction") }}',
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {
          action: action,
          user_ids: selectedIds
        },
        success: function(response) {
          if (response.success) {
            alert(response.message);
            dataTable.draw();
            $('#bulkActionForm')[0].reset();
            toggleBulkActionBtn();
          } else {
            alert(response.message);
          }
        },
        error: function() {
          alert('An error occurred');
        }
      });
    }
  });

  dataTable.on('draw', function() {
    $('.user-checkbox').change(toggleBulkActionBtn);
  });
});

function ConfirmStatusFunction(url) {
  if (confirm('Are you sure you want to change the status?')) {
    window.location.href = url;
  }
  return false;
}

function ConfirmDeleteFunction(url) {
  if (confirm('Are you sure you want to delete this user?')) {
    window.location.href = url;
  }
  return false;
}
</script>
@endsection