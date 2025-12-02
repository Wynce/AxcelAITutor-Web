
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="robots" content="noindex">
 
<title>{{$pageTitle ?? "Notification"}}  | {{config('constants.APP_NAME')}}</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- custom admin css -->
  <link rel="stylesheet" href="{{url('/')}}/assets/admin/css/admin.css">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{url('/')}}/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  
    <!-- Favicons -->
  <link rel="icon" href="{{asset('storage/settings/axcel_logo.png')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{url('/')}}/assets/adminlte/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
  <link rel="stylesheet" href="{{url('/')}}/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{url('/')}}/assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="{{url('/')}}/assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{url('/')}}/assets/adminlte/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="stylesheet" href="{{url('/')}}/assets/admin/css/sweetalert.css">
  <link rel="stylesheet" href="{{url('/')}}/assets/admin/css/jquery-confirm.min.css">
  <link rel="stylesheet" href="{{url('/')}}/assets/admin/css/admin.js">

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<style>
    .select2-selection__choice__display {
        color: black !important;
    }
    
</style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

   <!-- Header -->
     @include ('Admin.layout.header')

    <!-- Sidebar -->
 @include ('Admin.layout.sidebar')
  

   

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{$pageTitle ?? "Notification"}}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <!-- <li class="breadcrumb-item"><a href="#">Home</a></li> -->
              <li class="breadcrumb-item active"><a href="{{ route('adminDashboard') }}">Dashboard</a> </li>
              <li class="breadcrumb-item"><a href="{{$module_url}}">{{$module_name}}</a></li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- Content Header (Page header) -->
    
    <!-- /.content-header -->

    <!-- Main content -->
   <!--  <div class="content"> -->
       @yield('middlecontent')
               
     
  <!--   </div> -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
<!--   <aside class="control-sidebar control-sidebar-dark">
   
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside> -->
  <!-- /.control-sidebar -->
   @yield('script')
   @include ('Admin.layout.footer')
</div>
<!-- ./wrapper -->

<script type="text/javascript">
  var siteUrl = "{{url('/')}}";
</script>
<!-- REQUIRED SCRIPTS --><!-- 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
<script src="{{url('/')}}/assets/admin/js/jquery.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true
        });
        
        $("#years").hide();
        var otp = $("#notificationType #type").val();
        if(otp == 'years') {
          $("#years").show();
        }

        $("#notificationType #type").on('change', function() {
           var sel = $(this).val();
           if(sel == 'years') {
               $("#years").show();
           } else {
               $("#years").hide();
           }
        });
    });
</script>

<!-- jQuery Developer js-->
<script src="{{url('/')}}/assets/admin/js/admin.js"></script>
<!-- Bootstrap 4 -->
<script src="{{url('/')}}/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="{{url('/')}}/assets/adminlte/dist/js/adminlte.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{url('/')}}/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="{{url('/')}}/assets/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{url('/')}}/assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{url('/')}}/assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{url('/')}}/assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<!-- <script src="{{url('/')}}/assets/adminlte/dist/js/adminlte.min.js"></script> -->
<!-- AdminLTE for demo purposes --><!-- 
<script src="{{url('/')}}/assets/adminlte/dist/js/demo.js"></script> -->
<script src="{{url('/')}}/assets/admin/js/sweetalert.js"></script>
<script src="{{url('/')}}/assets/admin/js/jquery-confirm.min.js"></script>
</body>
</html>
