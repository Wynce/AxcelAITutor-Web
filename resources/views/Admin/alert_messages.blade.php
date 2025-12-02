<!-- Common Alert Messages  -->
@if(Session::has('success'))
<div class="alert alert-success alert-dismissible show fade">
   <div class="alert-body">
   <strong>Success</strong> {{Session::get('success')}}
   </div>
</div>
@endif
@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible show fade">
   <div class="alert-body">
   <strong>Error </strong> {{Session::get('error')}}
   </div>
</div>
@endif
@if(Session::has('warning'))
<div class="alert alert-warning alert-dismissible show fade">
   <div class="alert-body">
   <strong>Warning </strong> {{Session::get('warning')}}
   </div>
</div>
@endif