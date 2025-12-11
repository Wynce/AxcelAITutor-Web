@extends('Admin.layout.template')

@section('middlecontent')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $simulator->title }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.simulators.edit', $simulator) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.simulators.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($simulator->thumbnail)
                            <img src="{{ Storage::url($simulator->thumbnail) }}" alt="{{ $simulator->title }}" class="img-fluid img-thumbnail">
                        @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                <span>No Thumbnail</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th width="150">ID</th>
                                <td>{{ $simulator->id }}</td>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <td>{{ $simulator->title }}</td>
                            </tr>
                            <tr>
                                <th>Subject</th>
                                <td><span class="badge badge-info">{{ $simulator->subject }}</span></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($simulator->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($simulator->status == 'inactive')
                                        <span class="badge badge-danger">Inactive</span>
                                    @else
                                        <span class="badge badge-warning">Draft</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Sort Order</th>
                                <td>{{ $simulator->sort_order }}</td>
                            </tr>
                            <tr>
                                <th>Embed URL</th>
                                <td>
                                    <a href="{{ $simulator->embed_url }}" target="_blank">
                                        {{ Str::limit($simulator->embed_url, 50) }} <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($simulator->description)
                    <div class="mt-4">
                        <h5>Description</h5>
                        <p>{{ $simulator->description }}</p>
                    </div>
                @endif

                <div class="mt-4">
                    <h5>Preview</h5>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="{{ $simulator->embed_url }}" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection