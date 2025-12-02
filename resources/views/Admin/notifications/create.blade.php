@extends('Admin.layout.template')

@section('middlecontent')

<!-- Main content -->
<section class="content">
   <div class="container-fluid">

      @include('Admin.alert_messages')

      <form id="notificationForm" method="POST" action="{{ route('adminSaveNotification') }}" enctype="multipart/form-data">
        @csrf

        <!-- Notification Title -->
        <div class="form-group">
            <label>Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <!--<img src="{{ asset('storage/app/public/chatbot/profile/bot_profile_1726740607(1).jpg') }}" />-->
        <!-- Notification Message -->
        <div class="form-group">
            <label>Message <span class="text-danger">*</span></label>
            <textarea class="form-control" name="message" required>{{ old('message') }}</textarea>
            @error('message')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Notification Type -->
        <div class="form-group" id="notificationType">
            <label>Users <span class="text-danger">*</span></label>
            <select class="form-control" name="type" id="type" required>
                <option value="all" {{ old('type') == 'all' ? 'selected' : '' }}>All</option>
                <option value="years" {{ old('type') == 'years' ? 'selected' : '' }}>Years</option>
                <!--<option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>Success</option>-->
            </select>
            @error('type')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group" id="years">
            <label>Select Years <span class="text-danger">*</span></label>
            <select class="form-control select2" name="yearsSel[]" multiple="" >
                @foreach($users as $user)
                    <option value="{{ $user }}">{{ $user }}</option>
                @endforeach
            </select>
            @error('yearsSel')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Notification Image (Optional) -->
        <div class="form-group">
            <label>Upload Image (Optional)</label>
            <input type="file" class="form-control" name="image" accept="image/*">
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit button -->
        <div>
            <button type="submit" class="btn btn-success" style="margin-bottom: 60px;">Create Notification</button>
        </div>

    </form>
   </div>
</section>
<!-- /.content -->

<script src="{{url('/')}}/assets/admin/js/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Validate mandatory fields on form submit
    $('#notificationForm').on('submit', function(e) {
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
