@extends('Admin.layout.template')

@section('middlecontent')
<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Simulators</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.simulators.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Simulator
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="subject" class="form-control">
                                <option value="">All Subjects</option>
                                @foreach(\App\Models\Simulator::SUBJECTS as $subject)
                                    <option value="{{ $subject }}" {{ request('subject') == $subject ? 'selected' : '' }}>
                                        {{ $subject }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach(\App\Models\Simulator::STATUSES as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info btn-block">Filter</button>
                        </div>
                    </div>
                </form>

                <!-- Table -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th width="80">Thumbnail</th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($simulators as $simulator)
                            <tr>
                                <td>{{ $simulator->id }}</td>
                                <td>
                                    @if($simulator->thumbnail)
                                        <img src="{{ Storage::url($simulator->thumbnail) }}" alt="" width="60" height="40" style="object-fit: cover;">
                                    @else
                                        <span class="badge badge-secondary">No image</span>
                                    @endif
                                </td>
                                <td>{{ $simulator->title }}</td>
                                <td><span class="badge badge-info">{{ $simulator->subject }}</span></td>
                                <td>
                                    @if($simulator->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($simulator->status == 'inactive')
                                        <span class="badge badge-danger">Inactive</span>
                                    @else
                                        <span class="badge badge-warning">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $simulator->sort_order }}</td>
                                <td>
                                    <a href="{{ route('admin.simulators.show', $simulator) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.simulators.edit', $simulator) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.simulators.destroy', $simulator) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this simulator?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No simulators found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $simulators->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection