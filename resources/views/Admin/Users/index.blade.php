@extends('Admin.layout.template')
@section('middlecontent')
  <!-- Content Wrapper. Contains page content -->


<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">

          @include('Admin.alert_messages')

         <div class="card-header">  
            <!--  <button type="button" class="btn btn-block btn-info col-sm-2 pull-left export">Export</button>
          </div>  -->             
          <!-- /.card-header -->
          <div class="card-body">
            <table id="userTable" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th data-orderable="false">Image</th>
                {{-- <th>Username</th> --}}
                <th>Fullname</th>
                <th>Email</th>
                <th>Birthyear</th>
                <th>Country</th>
                <th data-orderable="false">Status</th>              
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
<script src="{{url('/')}}/assets/admin/js/jquery.min.js"></script>

<script>

  $(function () {
  var dataTable =  $("#userTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "serverSide": true,
      columnDefs: [
        {
            targets: [2,3,4,5],
            className: "text-center",
        }],
      "ajax": {
      headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url : '{{route("adminUserGetRecords")}}',
      'data': function(data){
        data.status = $("#status").val();
      },
       type:'post',
    },
    });
    

    $('.export').click(function(){
    $('#exportval').val(1);
    dataTable.draw();
    $('#exportval').val('0');
    $.ajax({
      url: "{{url('/')}}"+'/admin/users/exportdata?search='+$('#userTable_filter').find('input').val(),
    
      type: 'get',
      data: { },

      success: function(output){ 
        url="{{url('/')}}"+'/storage/uploads/UserDetails/UsersFromCMS.csv';
        window.open(url,"_self")
      }
    });
});

});

</script>

  @endsection