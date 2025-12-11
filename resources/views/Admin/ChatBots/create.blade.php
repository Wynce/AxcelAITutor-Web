@extends('Admin.layout.template')
@section('middlecontent')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <form method="POST" action="{{ route('admin.chatbots.store') }}" novalidate>
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="form-group">
                <label>Bot Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="bot_name" value="{{ old('bot_name') }}" required>
                <div class="text-danger">{{ $errors->first('bot_name') }}</div>
              </div>

              <div class="form-group">
                <label>Base Bot <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="base_bot" value="{{ old('base_bot') }}" required>
                <div class="text-danger">{{ $errors->first('base_bot') }}</div>
              </div>

              <div class="form-group">
                <label>Prompt <span class="text-danger">*</span></label>
                <textarea class="form-control" name="prompt" rows="4" required>{{ old('prompt') }}</textarea>
                <div class="text-danger">{{ $errors->first('prompt') }}</div>
              </div>

              <div class="form-group">
                <label>Greeting Message</label>
                <textarea class="form-control" name="greeting_message" rows="2">{{ old('greeting_message') }}</textarea>
              </div>

              <div class="form-group">
                <label>Bot Bio</label>
                <textarea class="form-control" name="bot_bio" rows="3">{{ old('bot_bio') }}</textarea>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="public_access" id="public_access" value="1" {{ old('public_access') ? 'checked' : '' }}>
                <label class="form-check-label" for="public_access">Public Access</label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="show_prompt" id="show_prompt" value="1" {{ old('show_prompt') ? 'checked' : '' }}>
                <label class="form-check-label" for="show_prompt">Show Prompt</label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_default">Default Bot</label>
              </div>

              <div class="form-group mt-3">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Save
                </button>
                <a href="{{ route('admin.chatbots.index') }}" class="btn btn-secondary">
                  <i class="fas fa-times"></i> Cancel
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
@endsection

