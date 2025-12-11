@extends('Admin.layout.template')
@section('middlecontent')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4">
        <div class="card card-primary card-outline">
          <div class="card-body box-profile">
            <div class="text-center">
              @if($user->image)
                <img class="profile-user-img img-fluid img-circle" src="{{ asset('assets/' . $user->image) }}" alt="User profile picture">
              @else
                <img class="profile-user-img img-fluid img-circle" src="{{ asset('assets/profile_images/default/no-image.png') }}" alt="User profile picture">
              @endif
            </div>
            <h3 class="profile-username text-center">{{ $user->first_name }} {{ $user->last_name }}</h3>
            <p class="text-muted text-center">{{ $user->email }}</p>
            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <b>Status</b>
                <span class="float-right">
                  @if($user->status == 'active')
                    <span class="badge badge-success">Active</span>
                  @elseif($user->status == 'inactive')
                    <span class="badge badge-warning">Inactive</span>
                  @else
                    <span class="badge badge-danger">Blocked</span>
                  @endif
                </span>
              </li>
              <li class="list-group-item">
                <b>Total Chats</b> <span class="float-right">{{ $chatCount }}</span>
              </li>
              <li class="list-group-item">
                <b>Country</b> <span class="float-right">{{ $user->country ?? 'N/A' }}</span>
              </li>
              <li class="list-group-item">
                <b>Birth Year</b> <span class="float-right">{{ $user->birth_year ?? 'N/A' }}</span>
              </li>
              <li class="list-group-item">
                <b>Registered</b> <span class="float-right">{{ $user->created_at->format('M d, Y') }}</span>
              </li>
              <li class="list-group-item">
                <b>Last Active</b> <span class="float-right">
                  {{ $user->last_active_at ? $user->last_active_at->format('M d, Y H:i') : 'Never' }}
                </span>
              </li>
            </ul>
            <div class="text-center">
              <a href="{{ route('adminUserEdit', base64_encode($user->id)) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit User
              </a>
              <a href="{{ route('adminUsers') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
              </a>
            </div>
          </div>
        </div>
      </div>

      @php
        use Illuminate\Support\Str;
      @endphp

      <div class="col-md-8">
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Recent Chats</a></li>
              <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">User Info</a></li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="active tab-pane" id="activity">
                @if($recentChats->count() > 0)
                  @foreach($recentChats as $chat)
                    <div class="post">
                      <div class="user-block">
                        <span class="username">
                          <a href="#">{{ $user->first_name }}</a>
                        </span>
                        <span class="description">{{ $chat->created_at->format('M d, Y - H:i') }}</span>
                      </div>
                      <p><strong>User:</strong> {{ Str::limit($chat->user_message, 100) }}</p>
                      <p><strong>Bot:</strong> {{ Str::limit($chat->response, 100) }}</p>
                    </div>
                  @endforeach
                @else
                  <p class="text-muted">No chat history available</p>
                @endif
              </div>
              <div class="tab-pane" id="settings">
                <table class="table table-bordered">
                  <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                  </tr>
                  <tr>
                    <th>First Name</th>
                    <td>{{ $user->first_name ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <th>Last Name</th>
                    <td>{{ $user->last_name ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <th>Country</th>
                    <td>{{ $user->country ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <th>Birth Year</th>
                    <td>{{ $user->birth_year ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <th>Login Type</th>
                    <td>{{ $user->login_type ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <th>Email Verified</th>
                    <td>
                      @if($user->email_verified_at)
                        <span class="badge badge-success">Yes ({{ $user->email_verified_at->format('M d, Y') }})</span>
                      @else
                        <span class="badge badge-warning">No</span>
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <th>First Login</th>
                    <td>{{ $user->is_first_login ? 'Yes' : 'No' }}</td>
                  </tr>
                  <tr>
                    <th>Created At</th>
                    <td>{{ $user->created_at->format('M d, Y H:i:s') }}</td>
                  </tr>
                  <tr>
                    <th>Updated At</th>
                    <td>{{ $user->updated_at->format('M d, Y H:i:s') }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

