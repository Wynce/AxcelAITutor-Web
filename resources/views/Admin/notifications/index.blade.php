@extends('Admin.layout.template')
@section('middlecontent')

<div class="content-header">
  <div class="container-fluid px-4">
    <div class="card mt-4">
      <div class="card-body">
        @include ('Admin.alert_messages')

        <!--<form method="POST" action="" class="needs-validation" novalidate>-->
        <!--  @csrf-->
        <!--  <div class="card mb-3">-->
        <!--    <label>Notification Title <span class="text-danger">*</span></label>-->
        <!--    <input type="text" class="form-control" name="title" required placeholder="Enter title">-->
        <!--  </div> -->

        <!--  <div class="card mb-3">-->
        <!--    <label>Message <span class="text-danger">*</span></label>-->
        <!--    <textarea class="form-control" name="message" required placeholder="Enter message"></textarea>-->
        <!--  </div>-->

        <!--  <button type="submit" class="btn btn-success">Send Notification</button>-->
        <!--</form>-->

        <!--<h4 class="mt-4">Recent Notifications</h4>-->
        <!--<ul class="list-group">-->
        <!--  @foreach($notifications as $notification)-->
        <!--    <li class="list-group-item">-->
        <!--      <strong>{{ $notification->title }}</strong>: {{ $notification->message }}-->
        <!--    </li>-->
        <!--  @endforeach-->
        <!--</ul>-->
        
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="thead-dark">
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Body</th>
                <th>Image</th>
                <th>Type</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($notifications as $index => $notification)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $notification->title }}</td>
                  <td>{{ $notification->message }}</td>
                  <td>
                    @if($notification->image)
                      <img src="{{ asset('uploads/' . $notification->image) }}" alt="Notification Image" width="60" height="60" class="rounded">
                    @else
                      <span class="text-muted">No Image</span>
                    @endif
                  </td>
                  <td><span class="badge badge-primary text-uppercase">{{ $notification->type }}</span></td>
                  <td>
                    <form method="POST" action="{{ route('sendFCMToTopic') }}">
                      @csrf
                      <input type="hidden" name="topic" value="{{ $notification->type }}">
                      <input type="hidden" name="title" value="{{ $notification->title }}">
                      <input type="hidden" name="body" value="{{ $notification->message }}">
                      <input type="hidden" name="image" value="{{ $notification->path }}">
                      <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-paper-plane"></i> Send
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
