@extends('Admin.layout.template')
@section('middlecontent')

<section class="content">
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-3 col-6">
      <div class="small-box bg-primary">
        <div class="inner">
          <h3>{{ $userCount ?? 0 }}</h3>
          <p>Total Users</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
        <a href="{{route('adminUsers')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="small-box bg-success">
        <div class="inner">
          <h3>{{ $activeUsers ?? 0 }}</h3>
          <p>Active Users</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-check"></i>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3>{{ $blockedUsers ?? 0 }}</h3>
          <p>Blocked Users</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-slash"></i>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="small-box bg-info">
        <div class="inner">
          <h3>{{ $newUsers7d ?? 0 }}</h3>
          <p>New Users (7d)</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-plus"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-3 col-6">
      <div class="small-box bg-secondary">
        <div class="inner">
          <h3>{{ $chatsTotal ?? 0 }}</h3>
          <p>Total Chats</p>
        </div>
        <div class="icon">
          <i class="fas fa-comments"></i>
        </div>
        <a href="{{route('admin.chat-history.index')}}" class="small-box-footer">View chats <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3>{{ $chats7d ?? 0 }}</h3>
          <p>Chats (7d)</p>
        </div>
        <div class="icon">
          <i class="fas fa-comment-dots"></i>
        </div>
      </div>
    </div>
  </div>
</div><!-- /.container-fluid -->
</section>


@endsection('middlecontent')