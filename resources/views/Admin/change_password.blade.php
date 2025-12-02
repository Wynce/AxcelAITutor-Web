@extends('Admin.layout.template')
@section('middlecontent')
<div class="content-header">
  <div class="container-fluid px-4">
    <div class="card mt-4">
      <div class="card-body">

        @include ('Admin.alert_messages')
        <form method="POST" action="{{route('adminChangePasswordStore')}}" class="needs-validation"    enctype="multipart/form-data" novalidate="" id="seller-change-password">
          @csrf
          <div class="card mb-3">
            <label>New Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" name="password" id="password" required tabindex="2" placeholder="New Password">
            <div class="text-danger" id="err_password">@if($errors->has('password')) {{ $errors->first('password') }}@endif</div>
          </div> 

          <div class="card mb-3">
            <label class="label_css">Confirm Password</label>
            <input type="password" class="form-control" name="password_confirmation" required tabindex="3" id="password_confirmation" placeholder="Confirm Password">
            <div class="text-danger" id="err_confirm_password">@if($errors->has('password_confirmation')) {{ $errors->first('password_confirmation') }}@endif</div>
          </div>

          <div class="col-md-12 ">
            <button type="submit" class="btn btn-icon icon-left btn-success" tabindex="15"><i class="" id="changePasswordBtn"></i>Save</button>&nbsp;&nbsp;
            <a href="{{route('adminDashboard')}}" class="btn btn-icon icon-left btn-danger" tabindex="16"><i class=""></i>Cancel</a>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection('middlecontent')
