@extends('Admin.layout.template')
@section('middlecontent')

<section class="content">
<div class="container-fluid">
  <form method="POST" name="filterForm" id="filterForm" action="{{route('adminDashboard')}}">
    @csrf

<div class="row">


  <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-primary">
        <div class="inner">
          <h3>@if($userCount) {{$userCount}} @else 0 @endif</h3>

          <p>Users</p>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
        <a href="{{route('adminUsers')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
</div>


</form>
<!-- /.row -->
 </div><!-- /.container-fluid -->
</section>


@endsection('middlecontent')