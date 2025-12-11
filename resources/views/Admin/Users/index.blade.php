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
            <div class="row">
              <div class="col-md-6">
                <h3 class="card-title">Users Management</h3>
              </div>
              <div class="col-md-6 text-right">
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
                <div class="col-md-3">
                  <select name="status" id="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="blocked">Blocked</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <input type="text" name="country" id="country" class="form-control" placeholder="Filter by Country">
                </div>
                <div class="col-md-2">
                  <input type="date" name="date_from" id="date_from" class="form-control" placeholder="From Date">
                </div>
                <div class="col-md-2">
                  <input type="date" name="date_to" id="date_to" class="form-control" placeholder="To Date">
                </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-primary" id="applyFilters">Apply Filters</button>
                  <button type="button" class="btn btn-secondary" id="resetFilters">Reset</button>
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

            <table id="userTable" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th data-orderable="false"><input type="checkbox" id="selectAll"></th>
                <th data-orderable="false">Image</th>
                <th>Fullname</th>
                <th>Email</th>
                <th>Birthyear</th>
                <th>Country</th>
                <th>Chats</th>
                <th>Last Active</th>
                <th data-orderable="false">Status</th>
                <th data-orderable="false">Action</th>
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

  // Update table to include checkboxes (this would need to be done in the controller response)
  // For now, we'll add checkboxes via JavaScript after table loads
  dataTable.on('draw', function() {
    $('.user-checkbox').change(toggleBulkActionBtn);
  });
});

// Helper functions
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
