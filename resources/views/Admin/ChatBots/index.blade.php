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
            <a href="{{ route('admin.chatbots.create') }}" class="btn btn-primary">
              <i class="fas fa-plus"></i> Add Chatbot
            </a>
          </div>

          <div class="card-body">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Base Bot</th>
                  <th>Public</th>
                  <th>Default</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($bots as $bot)
                  <tr>
                    <td>{{ $bot->bot_name }}</td>
                    <td>{{ $bot->base_bot }}</td>
                    <td>
                      @if($bot->public_access)
                        <span class="badge badge-success">Yes</span>
                      @else
                        <span class="badge badge-secondary">No</span>
                      @endif
                    </td>
                    <td>
                      @if($bot->is_default)
                        <span class="badge badge-info">Default</span>
                      @else
                        <span class="badge badge-light">-</span>
                      @endif
                    </td>
                    <td>{{ $bot->created_at->format('Y-m-d') }}</td>
                    <td>
                      <a href="{{ route('admin.chatbots.edit', base64_encode($bot->id)) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="{{ route('admin.chatbots.delete', base64_encode($bot->id)) }}" class="btn btn-sm btn-danger" onclick="return confirm('Delete this chatbot?');">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center">No chatbots found</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

