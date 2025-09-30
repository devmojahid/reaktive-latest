@extends('agency.master_layout')
@section('title', 'Destination Details')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Destination Details</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="{{ route('agency.tourbooking.destinations.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('agency.tourbooking.destinations.edit', $destination) }}" class="btn btn-success">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Basic Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 200px;">ID</th>
                                            <td>{{ $destination->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $destination->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Country</th>
                                            <td>{{ $destination->country }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @if($destination->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Featured</th>
                                            <td>
                                                @if($destination->is_featured)
                                                    <span class="badge badge-info">Yes</span>
                                                @else
                                                    <span class="badge badge-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $destination->created_at->format('F d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td>{{ $destination->updated_at->format('F d, Y h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    @if($destination->image)
                                        <div class="text-center">
                                            <h5 class="mb-3">Featured Image</h5>
                                            <img src="{{ asset($destination->image) }}"
                                                alt="{{ $destination->name }}"
                                                class="img-fluid rounded"
                                                style="max-height: 300px;">
                                        </div>
                                    @else
                                        <div class="text-center p-5 bg-light rounded">
                                            <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                            <p class="mb-0">No image available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Description</h3>
                        </div>
                        <div class="card-body">
                            @if($destination->description)
                                <div class="description-content">
                                    {!! $destination->description !!}
                                </div>
                            @else
                                <p class="text-muted">No description available</p>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">SEO Information</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Meta Title</th>
                                    <td>{{ $destination->meta_title ?? 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <th>Meta Keywords</th>
                                    <td>{{ $destination->meta_keywords ?? 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <th>Meta Description</th>
                                    <td>{{ $destination->meta_description ?? 'Not set' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Related Tours</h3>
                        </div>
                        <div class="card-body">
                            @if($destination->tours && $destination->tours->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($destination->tours as $tour)
                                                <tr>
                                                    <td>{{ $tour->id }}</td>
                                                    <td>{{ $tour->title }}</td>
                                                    <td>{{ number_format($tour->price, 2) }}</td>
                                                    <td>
                                                        @if($tour->status)
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('agency.tourbooking.tours.show', $tour) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No tours associated with this destination</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .description-content {
        min-height: 100px;
    }
    .description-content img {
        max-width: 100%;
        height: auto;
    }
</style>
@endpush
