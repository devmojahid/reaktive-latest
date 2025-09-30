@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Media Gallery') }} - {{ $service->translation->title ?? $service->title }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Media Gallery') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Services') }} >>
        {{ __('translate.Media Gallery') }}</p>
@endsection

@push('style_section')
    <style>
        .media-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .media-item {
            position: relative;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .media-item img,
        .media-item video {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
        }

        .media-item-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 5px;
        }

        .media-item-footer {
            padding: 10px;
            background-color: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }

        .media-caption {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .thumbnail-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #28a745;
            color: white;
            border-radius: 3px;
            padding: 3px 6px;
            font-size: 12px;
        }

        .media-upload-card {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
        }

        .uploader-icon {
            font-size: 48px;
            color: #aaa;
            margin-bottom: 15px;
        }

        .media-type-badge {
            position: absolute;
            bottom: 10px;
            left: 10px;
            border-radius: 3px;
            padding: 3px 6px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .media-type-badge.image {
            background-color: #007bff;
            color: white;
        }

        .media-type-badge.video {
            background-color: #dc3545;
            color: white;
        }
    </style>
@endpush

@section('body-content')
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12 mg-top-30">
                                    <div class="crancy-product-card">
                                        <div class="create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Media Gallery for') }}:
                                                {{ $service->translation->title ?? $service->title }}</h4>
                                            <div>
                                                <a href="{{ route('admin.tourbooking.services.edit', ['service' => $service->id, 'lang_code' => admin_lang()]) }}"
                                                    class="crancy-btn"><i class="fa fa-edit"></i>
                                                    {{ __('translate.Edit Service') }}</a>
                                                <a href="{{ route('admin.tourbooking.services.index') }}"
                                                    class="crancy-btn"><i class="fa fa-list"></i>
                                                    {{ __('translate.Service List') }}</a>
                                            </div>
                                        </div>

                                        <div class="row mg-top-30">
                                            <div class="col-12">
                                                <div class="accordion" id="mediaAccordion">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                                aria-expanded="true" aria-controls="collapseOne">
                                                                {{ __('translate.Upload New Media') }}
                                                            </button>
                                                        </h2>
                                                        <div id="collapseOne" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingOne" data-bs-parent="#mediaAccordion">
                                                            <div class="accordion-body">
                                                                <form
                                                                    action="{{ route('admin.tourbooking.services.media.store', $service->id) }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Media File') }}
                                                                                    *</label>
                                                                                <div
                                                                                    class="crancy-product-card__upload crancy-product-card__upload--border">
                                                                                    <input type="file" class="btn-check"
                                                                                        name="file" id="input-media"
                                                                                        autocomplete="off"
                                                                                        onchange="previewMedia(event)"
                                                                                        required>
                                                                                    <label
                                                                                        class="crancy-image-video-upload__label"
                                                                                        for="input-media">
                                                                                        <img id="view_media"
                                                                                            src="{{ asset($general_setting->placeholder_image ?? 'admin/img/img-placeholder.jpg') }}">
                                                                                        <h4
                                                                                            class="crancy-image-video-upload__title">
                                                                                            {{ __('translate.Click here to') }}
                                                                                            <span
                                                                                                class="crancy-primary-color">{{ __('translate.Choose File') }}</span>
                                                                                            {{ __('translate.and upload') }}
                                                                                        </h4>
                                                                                    </label>
                                                                                </div>
                                                                                <small
                                                                                    class="form-text text-muted">{{ __('translate.Supported files: jpg, jpeg, png, gif, webp, mp4, avi, mov (Max: 10MB)') }}</small>
                                                                                @error('file')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Caption') }}</label>
                                                                                <input class="crancy__item-input"
                                                                                    type="text" name="caption"
                                                                                    value="{{ old('caption') }}">
                                                                                @error('caption')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror

                                                                                <div class="mg-top-30">
                                                                                    <button type="submit"
                                                                                        class="crancy-btn">{{ __('translate.Upload Media') }}</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mg-top-30">
                                            <div class="col-12">
                                                <h4 class="crancy-product-card__title">{{ __('translate.Existing Media') }}
                                                </h4>

                                                @if ($service->media->count() > 0)
                                                    <div class="media-gallery">
                                                        @foreach ($service->media as $media)
                                                            <div class="media-item">
                                                                @if ($media->is_thumbnail)
                                                                    <span
                                                                        class="thumbnail-badge">{{ __('translate.Thumbnail') }}</span>
                                                                @endif

                                                                @if ($media->file_type == 'image')
                                                                    <img src="{{ asset($media->file_path) }}"
                                                                        alt="{{ $media->caption ?? $media->file_name }}">
                                                                    <span
                                                                        class="media-type-badge image">{{ __('translate.Image') }}</span>
                                                                @else
                                                                    <video controls muted>
                                                                        <source
                                                                            src="{{ asset($media->file_path) }}"
                                                                            type="video/mp4">
                                                                        {{ __('translate.Your browser does not support the video tag.') }}
                                                                    </video>
                                                                    <span
                                                                        class="media-type-badge video">{{ __('translate.Video') }}</span>
                                                                @endif

                                                                <div class="media-item-actions">
                                                                    @if ($media->file_type == 'image' && !$media->is_thumbnail)
                                                                        <form
                                                                            action="{{ route('admin.tourbooking.services.media.set-thumbnail', $media->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-primary"
                                                                                title="{{ __('translate.Set as Thumbnail') }}">
                                                                                <i class="fa fa-star"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif

                                                                    <button type="button" class="btn btn-sm btn-danger"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#deleteModal{{ $media->id }}"
                                                                        title="{{ __('translate.Delete') }}">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>

                                                                <div class="media-item-footer">
                                                                    @if ($media->caption)
                                                                        <div class="media-caption"
                                                                            title="{{ $media->caption }}">
                                                                            {{ $media->caption }}</div>
                                                                    @endif
                                                                    <small
                                                                        class="text-muted">{{ \Carbon\Carbon::parse($media->created_at)->format('M d, Y') }}</small>
                                                                </div>
                                                            </div>

                                                            <!-- Delete Modal -->
                                                            <div class="modal fade" id="deleteModal{{ $media->id }}"
                                                                tabindex="-1"
                                                                aria-labelledby="deleteModalLabel{{ $media->id }}"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="deleteModalLabel{{ $media->id }}">
                                                                                {{ __('translate.Confirm Delete') }}</h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            {{ __('translate.Are you sure you want to delete this media item?') }}
                                                                            @if ($media->is_thumbnail)
                                                                                <div class="alert alert-warning mt-3">
                                                                                    <i
                                                                                        class="fa fa-exclamation-triangle"></i>
                                                                                    {{ __('translate.This is the current thumbnail. If deleted, another image will be selected as thumbnail.') }}
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="crancy-btn crancy-btn__default"
                                                                                data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                                                                            <form
                                                                                action="{{ route('admin.tourbooking.services.media.destroy', $media->id) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="crancy-btn delete_danger_btn">{{ __('translate.Delete') }}</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="alert alert-info mg-top-20">
                                                        {{ __('translate.No media found. Add your first media item using the form above.') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js_section')
    <script>
        function previewMedia(event) {
            var file = event.target.files[0];
            var reader = new FileReader();
            var output = document.getElementById('view_media');

            reader.onload = function() {
                output.src = reader.result;
            }

            if (file.type.includes('image/')) {
                reader.readAsDataURL(file);
            } else if (file.type.includes('video/')) {
                // For video, we'll show a placeholder or video thumbnail
                output.src = "{{ asset('admin/img/video-placeholder.jpg') }}";
            }
        };
    </script>
@endpush
