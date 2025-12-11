@extends('Admin.layout.template')
@section('middlecontent')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Add New User</h3>
          </div>
          <form method="POST" action="{{ route('adminUserStore') }}" enctype="multipart/form-data" id="createUserForm">
            @csrf
            <input type="hidden" name="id" value="">
            
            <div class="card-body">
              @if($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <!-- User Type -->
              <div class="form-group">
                <label>User Type <span class="text-danger">*</span></label>
                <select name="user_type" id="user_type" class="form-control" required>
                  <option value="student" {{ old('user_type') == 'student' ? 'selected' : '' }}>Student</option>
                  <option value="parent" {{ old('user_type') == 'parent' ? 'selected' : '' }}>Parent</option>
                  <option value="teacher" {{ old('user_type') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                </select>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    @error('first_name')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                @error('email')
                  <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country') }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Birth Year</label>
                    <input type="number" name="birth_year" class="form-control" value="{{ old('birth_year') }}" min="1900" max="{{ date('Y') }}">
                  </div>
                </div>
              </div>

              <!-- Student-specific fields -->
              <div id="studentFields" class="user-type-fields">
                <hr>
                <h5><i class="fas fa-user-graduate"></i> Student Information</h5>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>School Name</label>
                      <input type="text" name="school_name" class="form-control" value="{{ old('school_name') }}">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Grade Level</label>
                      <select name="grade_level" class="form-control">
                        <option value="">Select Grade</option>
                        <option value="Year 7" {{ old('grade_level') == 'Year 7' ? 'selected' : '' }}>Year 7</option>
                        <option value="Year 8" {{ old('grade_level') == 'Year 8' ? 'selected' : '' }}>Year 8</option>
                        <option value="Year 9" {{ old('grade_level') == 'Year 9' ? 'selected' : '' }}>Year 9</option>
                        <option value="Year 10" {{ old('grade_level') == 'Year 10' ? 'selected' : '' }}>Year 10</option>
                        <option value="Year 11" {{ old('grade_level') == 'Year 11' ? 'selected' : '' }}>Year 11</option>
                        <option value="Year 12" {{ old('grade_level') == 'Year 12' ? 'selected' : '' }}>Year 12</option>
                        <option value="Year 13" {{ old('grade_level') == 'Year 13' ? 'selected' : '' }}>Year 13</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Assign Parent (Optional)</label>
                  <select name="parent_id" class="form-control select2">
                    <option value="">No Parent</option>
                    @foreach($availableParents ?? [] as $parent)
                      <option value="{{ $parent->id }}">{{ $parent->first_name }} {{ $parent->last_name }} ({{ $parent->email }})</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <!-- Teacher-specific fields -->
              <div id="teacherFields" class="user-type-fields" style="display: none;">
                <hr>
                <h5><i class="fas fa-chalkboard-teacher"></i> Teacher Information</h5>
                <div class="form-group">
                  <label>Subjects Teaching</label>
                  <input type="text" name="subjects_teaching" class="form-control" value="{{ old('subjects_teaching') }}" placeholder="e.g. Physics, Chemistry, Biology">
                  <small class="text-muted">Separate multiple subjects with commas</small>
                </div>
              </div>

              <!-- Parent-specific fields -->
              <div id="parentFields" class="user-type-fields" style="display: none;">
                <hr>
                <h5><i class="fas fa-user-friends"></i> Parent Information</h5>
                <p class="text-muted">You can link children (students) after creating this user.</p>
              </div>

              <div class="form-group">
                <label>Profile Image</label>
                <input type="file" name="profile" class="form-control" accept="image/*">
              </div>

              <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                  <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                  <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                  <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create User
              </button>
              <a href="{{ route('adminUsers') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('script')
<script>
$(function() {
  // Toggle fields based on user type
  function toggleUserTypeFields() {
    var userType = $('#user_type').val();
    $('.user-type-fields').hide();
    
    if (userType === 'student') {
      $('#studentFields').show();
    } else if (userType === 'teacher') {
      $('#teacherFields').show();
    } else if (userType === 'parent') {
      $('#parentFields').show();
    }
  }
  
  // Initial toggle
  toggleUserTypeFields();
  
  // On change
  $('#user_type').change(function() {
    toggleUserTypeFields();
  });
});
</script>
@endsection