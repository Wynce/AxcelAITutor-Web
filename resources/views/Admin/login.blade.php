<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{config('constants.APP_NAME')}} | {{$pageTitle}}</title>
  
  <!-- Favicons -->
  <link rel="icon" href="{{asset('storage/uploads/settings/default/favicon.png')}}">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{url('/')}}/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{url('/')}}/assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{url('/')}}/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
   @include('Admin.alert_messages')

      @php
        // Default logo path if not found in the database
        $defaultLogoPath = asset('assets/settings/axcel_logo.png');
      @endphp

  <div class="login-logo">
       <a href="{{route('adminLogin')}}"><b><img src="{{ $defaultLogoPath }}" alt="{{config('constants.APP_NAME')}}" width="180"/></a>
  </div>
  <!-- /.login-logo -->
  <div class="card card-outline card-primary" style="border-top: 3px solid #0A5972 !important;">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="{{route('adminDoLogin')}}" method="POST">
        @csrf
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" id="pass_log_id">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock toggle-password"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{url('/')}}/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{url('/')}}/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="{{url('/')}}/assets/adminlte/dist/js/adminlte.min.js"></script>
<script>
$(".toggle-password").click(function(){
    var input = $("#pass_log_id");
    if(input.attr('type') === 'password'){
      input.attr('type','text');
      $(this).addClass("fa-unlock");
      $(this).removeClass("fa-lock");
    }else{
      input.attr('type','password'); 
      $(this).addClass("fa-lock");
      $(this).removeClass("fa-unlock");
    }
    
});
</script>
</body>
</html>
