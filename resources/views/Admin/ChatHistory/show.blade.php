@extends('Admin.layout.template')
@section('middlecontent')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Chat #{{ $messages->first()->chat_id }}</h3>
          </div>
          <div class="card-body">
            @foreach($messages as $message)
              <div class="post">
                <div class="user-block">
                  <span class="username">
                    <a href="#">{{ $message->user ? $message->user->first_name : 'User' }}</a>
                  </span>
                  <span class="description">{{ $message->created_at->format('M d, Y H:i') }}</span>
                </div>
                <p><strong>User:</strong> {{ $message->user_message }}</p>
                <p><strong>Bot:</strong> {{ $message->response }}</p>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

