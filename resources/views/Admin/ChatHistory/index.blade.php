@extends('Admin.layout.template')
@section('middlecontent')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          @include('Admin.alert_messages')

          <div class="card-header">
            <h3 class="card-title">Chat History</h3>
          </div>

          <div class="card-body">
            <form method="GET" action="{{ route('admin.chat-history.index') }}" class="mb-3">
              <div class="row">
                <div class="col-md-3">
                  <select name="user_id" class="form-control select2">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                      <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->first_name }} {{ $u->last_name }} ({{ $u->email }})
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <select name="bot_id" class="form-control select2">
                    <option value="">All Bots</option>
                    @foreach($bots as $bot)
                      <option value="{{ $bot->id }}" {{ request('bot_id') == $bot->id ? 'selected' : '' }}>{{ $bot->bot_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2">
                  <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                  <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To Date">
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-primary">Filter</button>
                  <a href="{{ route('admin.chat-history.index') }}" class="btn btn-secondary">Reset</a>
                </div>
              </div>
            </form>

            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>Chat ID</th>
                  <th>User</th>
                  <th>Bot</th>
                  <th>Last Message</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($chats as $chat)
                  <tr>
                    <td>#{{ $chat->chat_id }}</td>
                    <td>{{ $chat->user ? $chat->user->first_name.' '.$chat->user->last_name : 'N/A' }}</td>
                    <td>{{ $chat->selected_bot_id ?? 'N/A' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($chat->user_message, 60) }}</td>
                    <td>{{ $chat->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                      <a href="{{ route('admin.chat-history.show', $chat->chat_id) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center">No chat records found</td>
                  </tr>
                @endforelse
              </tbody>
            </table>

            <div class="mt-3">
              {{ $chats->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

