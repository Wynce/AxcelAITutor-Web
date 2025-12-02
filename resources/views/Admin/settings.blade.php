@extends('Admin.layout.template')
@section('middlecontent')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-fullscreen/dist/leaflet.fullscreen.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-search/4.0.0/leaflet-search.min.css" />

<!-- Main content -->
<section class="content">
   <div class="container-fluid">

      @include('Admin.alert_messages')
      
      <form id="settingsForm" method="POST" action="{{ route('adminSaveSettings') }}" enctype="multipart/form-data">
        @csrf

        <!-- Field to update logo -->
        <div class="form-group">
            <label>Logo</label>
            <img id="logoPreview" src="#" alt="Logo Preview" width="100" style="display: none; margin-top: 10px;">
            @if ($settings && $settings->logo)
                <img id="currentLogo" src="{{ asset('assets/' . $settings->logo) }}" alt="Logo" width="100" style="margin-top: 10px;">
            @endif
            <input type="file" class="form-control" id="logo" name="logo">
        </div>


        <!-- Name field -->
        <div class="form-group">
            <label>Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" value="{{ $settings->name ?? '' }}" required>
            <small>Should be unique and use 4-20 characters, including letters, numbers, dashes, periods, and underscores.</small>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Base Bot dropdown -->
        <div class="form-group">
            <label>Base Bot <span class="text-danger">*</span></label>
            <select class="form-control" name="base_bot" required>
                <option value="GPT-40-Mini" {{ ($settings && $settings->base_bot == 'GPT-40-Mini') ? 'selected' : '' }}>GPT-40-Mini</option>
            </select>
        </div>

        <!-- Prompt field -->
        <div class="form-group">
            <label>Prompt <span class="text-danger">*</span></label>
            <textarea class="form-control" name="prompt" required>{{ $settings->prompt ?? '' }}</textarea>
            <small>Tell your bot how to behave and respond to user messages. Try to be as specific as possible.</small>
        </div>

        <!-- Knowledge base PDF upload -->
        <div class="form-group">
            <label>Knowledge Base (PDF) <span class="text-danger"></span></label>
            <input type="file" class="form-control" name="knowledge_base" accept=".pdf">
            @if ($settings && $settings->knowledge_base)
                <a href="{{ asset('assets/' . $settings->knowledge_base) }}" target="_blank">View Current PDF</a>
            @endif
            <small>Upload a PDF file containing your knowledge base.</small>
        </div>

        <!-- Greeting message -->
        <div class="form-group">
            <label>Greeting Message</label>
            <textarea class="form-control" name="greeting_message">{{ $settings->greeting_message ?? '' }}</textarea>
            <small>The bot will send this message at the beginning of every conversation.</small>
        </div>

        <!-- Suggest replies -->
        <div class="form-group">
            <label>Suggest Replies</label><br>
            <input type="radio" name="suggest_replies" value="1" {{ ($settings && $settings->suggest_replies) ? 'checked' : '' }}> Yes
            <input type="radio" name="suggest_replies" value="0" {{ ($settings && !$settings->suggest_replies) ? 'checked' : '' }}> No
        </div>
        
        <!-- Attachment replies -->
        <div class="form-group">
            <label>Attachment</label><br>
            <input type="radio" name="attachment" value="1" {{ ($settings && $settings->attachment) ? 'checked' : '' }}> Yes
            <input type="radio" name="attachment" value="0" {{ ($settings && !$settings->attachment) ? 'checked' : '' }}> No
        </div>
        
        <!-- Voice replies -->
        <div class="form-group">
            <label>Voice</label><br>
            <input type="radio" name="voice" value="1" {{ ($settings && $settings->voice) ? 'checked' : '' }}> Yes
            <input type="radio" name="voice" value="0" {{ ($settings && !$settings->voice) ? 'checked' : '' }}> No
        </div>
        
        <!-- Attachment file size field -->
        <div class="form-group">
            <label>Attachment File Size (KB)<span class="text-danger">*</span></label>
            <input type="tel" class="form-control" name="attachment_file_size" value="{{ $settings->attachment_file_size ?? '' }}" required>
        </div>

        <!-- Bio field -->
        <div class="form-group">
            <label>Bio</label>
            <textarea class="form-control" name="bio">{{ $settings->bio ?? '' }}</textarea>
        </div>

        <!-- Public Access -->
        <div class="form-group">
            <label>Make Bot Publicly Accessible</label><br>
            <input type="radio" name="public_access" value="1" {{ ($settings && $settings->public_access) ? 'checked' : '' }}> Yes
            <input type="radio" name="public_access" value="0" {{ ($settings && !$settings->public_access) ? 'checked' : '' }}> No
        </div>

        <!-- Submit button -->
        <div>
            <button type="submit" class="btn btn-success" style="margin-bottom: 60px;">Update Settings</button>
        </div>

    </form>
   </div>
</section>
<!-- /.content -->

<script src="{{url('/')}}/assets/admin/js/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Show logo preview and hide current logo when a new logo is uploaded
    $('#logo').change(function(e) {
        var reader = new FileReader();
        
        // Hide the current logo if it exists
        $('#currentLogo').hide();

        reader.onload = function(e) {
            $('#logoPreview').attr('src', e.target.result).show();
        }

        reader.readAsDataURL(this.files[0]);
    });

    // Validate mandatory fields on form submit
    $('#settingsForm').on('submit', function(e) {
        var isValid = true;

        // Check if mandatory fields are filled
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault(); // Prevent form submission if validation fails
            alert('Please fill all mandatory fields.');
        }
    });
});
</script>

@endsection('middlecontent')
