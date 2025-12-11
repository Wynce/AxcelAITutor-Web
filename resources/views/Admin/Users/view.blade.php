@extends('Admin.layout.template')
@section('middlecontent')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <!-- Profile Card -->
      <div class="col-md-4">
        <div class="card card-primary card-outline">
          <div class="card-body box-profile">
            <div class="text-center">
              @if($user->profile)
                <img class="profile-user-img img-fluid img-circle" src="{{ asset('storage/uploads/User/resized/' . $user->profile) }}" alt="User profile picture" style="width: 100px; height: 100px; object-fit: cover;">
              @elseif($user->image)
                <img class="profile-user-img img-fluid img-circle" src="{{ asset('assets/' . $user->image) }}" alt="User profile picture" style="width: 100px; height: 100px; object-fit: cover;">
              @else
                <img class="profile-user-img img-fluid img-circle" src="{{ asset('assets/profile_images/default/no-image.png') }}" alt="User profile picture" style="width: 100px; height: 100px; object-fit: cover;">
              @endif
            </div>
            <h3 class="profile-username text-center">{{ $user->first_name }} {{ $user->last_name }}</h3>
            <p class="text-muted text-center">{{ $user->email }}</p>
            <p class="text-center">
              @php
                $typeBadge = match($user->user_type ?? 'student') {
                  'student' => '<span class="badge badge-primary"><i class="fas fa-user-graduate"></i> Student</span>',
                  'parent' => '<span class="badge badge-success"><i class="fas fa-user-friends"></i> Parent</span>',
                  'teacher' => '<span class="badge badge-info"><i class="fas fa-chalkboard-teacher"></i> Teacher</span>',
                  default => '<span class="badge badge-secondary">Unknown</span>',
                };
              @endphp
              {!! $typeBadge !!}
            </p>
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
              @if($user->user_type == 'student')
              <li class="list-group-item">
                <b>School</b> <span class="float-right">{{ $user->school_name ?? 'N/A' }}</span>
              </li>
              <li class="list-group-item">
                <b>Grade</b> <span class="float-right">{{ $user->grade_level ?? 'N/A' }}</span>
              </li>
              @endif
              @if($user->user_type == 'teacher')
              <li class="list-group-item">
                <b>Subjects</b> <span class="float-right">{{ $user->subjects_teaching ?? 'N/A' }}</span>
              </li>
              @endif
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
              <a href="{{ route('adminUserEdit', base64_encode($user->id)) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit
              </a>
              <a href="{{ route('adminUsers') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
              </a>
            </div>
          </div>
        </div>

        <!-- Analytics Summary Card -->
        <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-bar"></i> 30-Day Analytics</h3>
          </div>
          <div class="card-body p-0">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between">
                <span><i class="fas fa-comments text-primary"></i> Total Chats</span>
                <strong>{{ $analytics['total_chats'] ?? 0 }}</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span><i class="fas fa-eye text-info"></i> Simulator Views</span>
                <strong>{{ $analytics['total_simulator_views'] ?? 0 }}</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span><i class="fas fa-question-circle text-warning"></i> Quiz Attempts</span>
                <strong>{{ $analytics['total_quiz_attempts'] ?? 0 }}</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span><i class="fas fa-check-circle text-success"></i> Correct Answers</span>
                <strong>{{ $analytics['total_quiz_correct'] ?? 0 }}</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span><i class="fas fa-clock text-secondary"></i> Time Spent</span>
                <strong>{{ $analytics['total_time_spent'] ?? 0 }} mins</strong>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-8">
        @php
          use Illuminate\Support\Str;
        @endphp
        
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Recent Chats</a></li>
              <li class="nav-item"><a class="nav-link" href="#relationships" data-toggle="tab">Relationships</a></li>
              <li class="nav-item"><a class="nav-link" href="#info" data-toggle="tab">User Info</a></li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <!-- Recent Chats Tab -->
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

              <!-- Relationships Tab -->
              <div class="tab-pane" id="relationships">
                @if($user->user_type == 'student')
                  <!-- Student's Parent -->
                  <div class="card card-outline card-success mb-3">
                    <div class="card-header">
                      <h5 class="card-title mb-0"><i class="fas fa-user-friends"></i> Parent</h5>
                    </div>
                    <div class="card-body">
                      @if($user->parent)
                        <div class="d-flex align-items-center justify-content-between">
                          <div>
                            <strong>{{ $user->parent->first_name }} {{ $user->parent->last_name }}</strong>
                            <br><small class="text-muted">{{ $user->parent->email }}</small>
                          </div>
                          <button class="btn btn-sm btn-danger" onclick="removeParent()">
                            <i class="fas fa-times"></i> Remove
                          </button>
                        </div>
                      @else
                        <form id="assignParentForm">
                          <div class="input-group">
                            <select name="parent_id" id="parent_id" class="form-control">
                              <option value="">Select Parent</option>
                              @foreach($availableParents ?? [] as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->first_name }} {{ $parent->last_name }} ({{ $parent->email }})</option>
                              @endforeach
                            </select>
                            <div class="input-group-append">
                              <button type="button" class="btn btn-success" onclick="assignParent()">
                                <i class="fas fa-plus"></i> Assign
                              </button>
                            </div>
                          </div>
                        </form>
                      @endif
                    </div>
                  </div>

                  <!-- Student's Teachers -->
                  <div class="card card-outline card-info">
                    <div class="card-header">
                      <h5 class="card-title mb-0"><i class="fas fa-chalkboard-teacher"></i> Teachers</h5>
                    </div>
                    <div class="card-body">
                      @if($user->teachers->count() > 0)
                        <ul class="list-group mb-3">
                          @foreach($user->teachers as $teacher)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                              <div>
                                <strong>{{ $teacher->first_name }} {{ $teacher->last_name }}</strong>
                                <br><small class="text-muted">{{ $teacher->email }} | {{ $teacher->pivot->subject ?? 'N/A' }}</small>
                              </div>
                              <button class="btn btn-sm btn-danger" onclick="removeTeacher({{ $teacher->id }})">
                                <i class="fas fa-times"></i>
                              </button>
                            </li>
                          @endforeach
                        </ul>
                      @else
                        <p class="text-muted">No teachers assigned</p>
                      @endif
                      <form id="assignTeacherForm">
                        <div class="row">
                          <div class="col-md-5">
                            <select name="teacher_id" id="teacher_id" class="form-control form-control-sm">
                              <option value="">Select Teacher</option>
                              @foreach($availableTeachers ?? [] as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="col-md-4">
                            <input type="text" name="subject" id="subject" class="form-control form-control-sm" placeholder="Subject">
                          </div>
                          <div class="col-md-3">
                            <button type="button" class="btn btn-info btn-sm btn-block" onclick="assignTeacher()">
                              <i class="fas fa-plus"></i> Add
                            </button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                @elseif($user->user_type == 'parent')
                  <!-- Parent's Children -->
                  <div class="card card-outline card-primary">
                    <div class="card-header">
                      <h5 class="card-title mb-0"><i class="fas fa-user-graduate"></i> Children (Students)</h5>
                    </div>
                    <div class="card-body">
                      @if($user->children->count() > 0)
                        <ul class="list-group mb-3">
                          @foreach($user->children as $child)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                              <div>
                                <strong>{{ $child->first_name }} {{ $child->last_name }}</strong>
                                <br><small class="text-muted">{{ $child->email }} | {{ $child->school_name ?? 'No school' }}</small>
                              </div>
                              <div>
                                <a href="{{ route('admin.users.view', base64_encode($child->id)) }}" class="btn btn-sm btn-info">
                                  <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="removeChild({{ $child->id }})">
                                  <i class="fas fa-times"></i>
                                </button>
                              </div>
                            </li>
                          @endforeach
                        </ul>
                      @else
                        <p class="text-muted">No children linked</p>
                      @endif
                      <form id="addChildForm">
                        <div class="input-group">
                          <select name="student_id" id="student_id" class="form-control">
                            <option value="">Select Student to Add</option>
                            @foreach($availableStudents ?? [] as $student)
                              <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }} ({{ $student->email }})</option>
                            @endforeach
                          </select>
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" onclick="addChild()">
                              <i class="fas fa-plus"></i> Add Child
                            </button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                @elseif($user->user_type == 'teacher')
                  <!-- Teacher's Students -->
                  <div class="card card-outline card-primary">
                    <div class="card-header">
                      <h5 class="card-title mb-0"><i class="fas fa-user-graduate"></i> Students ({{ $user->students->count() }})</h5>
                    </div>
                    <div class="card-body">
                      @if($user->students->count() > 0)
                        <div class="table-responsive">
                          <table class="table table-sm table-hover">
                            <thead>
                              <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($user->students as $student)
                                <tr>
                                  <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                  <td>{{ $student->email }}</td>
                                  <td>{{ $student->pivot->subject ?? 'N/A' }}</td>
                                  <td>
                                    <a href="{{ route('admin.users.view', base64_encode($student->id)) }}" class="btn btn-xs btn-info">
                                      <i class="fas fa-eye"></i>
                                    </a>
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      @else
                        <p class="text-muted">No students assigned</p>
                      @endif
                    </div>
                  </div>

                  <!-- Teacher's Classrooms -->
                  <div class="card card-outline card-warning mt-3">
                    <div class="card-header">
                      <h5 class="card-title mb-0"><i class="fas fa-school"></i> Classrooms</h5>
                    </div>
                    <div class="card-body">
                      @if($user->ownedClassrooms->count() > 0)
                        <ul class="list-group">
                          @foreach($user->ownedClassrooms as $classroom)
                            <li class="list-group-item d-flex justify-content-between">
                              <span>{{ $classroom->name }}</span>
                              <span class="badge badge-info">{{ $classroom->students_count ?? 0 }} students</span>
                            </li>
                          @endforeach
                        </ul>
                      @else
                        <p class="text-muted">No classrooms created</p>
                      @endif
                    </div>
                  </div>
                @else
                  <p class="text-muted">No relationships available for this user type.</p>
                @endif
              </div>

              <!-- User Info Tab -->
              <div class="tab-pane" id="info">
                <table class="table table-bordered">
                  <tr>
                    <th width="30%">Email</th>
                    <td>{{ $user->email }}</td>
                  </tr>
                  <tr>
                    <th>User Type</th>
                    <td>{{ ucfirst($user->user_type ?? 'student') }}</td>
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
                  @if($user->user_type == 'student')
                  <tr>
                    <th>School Name</th>
                    <td>{{ $user->school_name ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <th>Grade Level</th>
                    <td>{{ $user->grade_level ?? 'N/A' }}</td>
                  </tr>
                  @endif
                  @if($user->user_type == 'teacher')
                  <tr>
                    <th>Subjects Teaching</th>
                    <td>{{ $user->subjects_teaching ?? 'N/A' }}</td>
                  </tr>
                  @endif
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

@section('script')
<script>
var userId = '{{ base64_encode($user->id) }}';
var csrfToken = $('meta[name="csrf-token"]').attr('content');

function assignParent() {
  var parentId = $('#parent_id').val();
  if (!parentId) {
    alert('Please select a parent');
    return;
  }
  $.ajax({
    url: '{{ route("admin.users.assignParent") }}',
    type: 'POST',
    headers: {'X-CSRF-TOKEN': csrfToken},
    data: { student_id: userId, parent_id: parentId },
    success: function(response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.message || 'Error assigning parent');
      }
    },
    error: function() { alert('Error assigning parent'); }
  });
}

function removeParent() {
  if (!confirm('Remove parent from this student?')) return;
  $.ajax({
    url: '{{ route("admin.users.assignParent") }}',
    type: 'POST',
    headers: {'X-CSRF-TOKEN': csrfToken},
    data: { student_id: userId, parent_id: '' },
    success: function(response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.message || 'Error removing parent');
      }
    },
    error: function() { alert('Error removing parent'); }
  });
}

function assignTeacher() {
  var teacherId = $('#teacher_id').val();
  var subject = $('#subject').val();
  if (!teacherId) {
    alert('Please select a teacher');
    return;
  }
  $.ajax({
    url: '{{ route("admin.users.assignTeacher") }}',
    type: 'POST',
    headers: {'X-CSRF-TOKEN': csrfToken},
    data: { student_id: userId, teacher_id: teacherId, subject: subject },
    success: function(response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.message || 'Error assigning teacher');
      }
    },
    error: function() { alert('Error assigning teacher'); }
  });
}

function removeTeacher(teacherId) {
  if (!confirm('Remove this teacher?')) return;
  $.ajax({
    url: '{{ route("admin.users.removeTeacher") }}',
    type: 'POST',
    headers: {'X-CSRF-TOKEN': csrfToken},
    data: { student_id: userId, teacher_id: teacherId },
    success: function(response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.message || 'Error removing teacher');
      }
    },
    error: function() { alert('Error removing teacher'); }
  });
}

function addChild() {
  var studentId = $('#student_id').val();
  if (!studentId) {
    alert('Please select a student');
    return;
  }
  $.ajax({
    url: '{{ route("admin.users.addChild") }}',
    type: 'POST',
    headers: {'X-CSRF-TOKEN': csrfToken},
    data: { parent_id: userId, student_id: studentId },
    success: function(response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.message || 'Error adding child');
      }
    },
    error: function() { alert('Error adding child'); }
  });
}

function removeChild(studentId) {
  if (!confirm('Remove this child from parent?')) return;
  $.ajax({
    url: '{{ route("admin.users.removeChild") }}',
    type: 'POST',
    headers: {'X-CSRF-TOKEN': csrfToken},
    data: { student_id: studentId },
    success: function(response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.message || 'Error removing child');
      }
    },
    error: function() { alert('Error removing child'); }
  });
}
</script>
@endsection