@extends('agency.master_layout')
@section('title')
    <title>{{ __('translate.Itinerary') }} - {{ $service->translation->title ?? $service->title }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Itinerary') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Services') }} >> {{ __('translate.Itinerary') }}</p>
@endsection

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
                                            <h4 class="crancy-product-card__title">{{ __('translate.Itinerary for') }}: {{ $service->translation->title ?? $service->title }}</h4>
                                            <div>
                                                <a href="{{ route('agency.tourbooking.services.edit', $service->id) }}" class="crancy-btn"><i class="fa fa-edit"></i> {{ __('translate.Edit Service') }}</a>
                                                <a href="{{ route('agency.tourbooking.services.index') }}" class="crancy-btn"><i class="fa fa-list"></i> {{ __('translate.Service List') }}</a>
                                            </div>
                                        </div>

                                        <div class="row mg-top-30">
                                            <div class="col-12">
                                                <div class="accordion" id="itineraryAccordion">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne">
                                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                {{ __('translate.Add New Itinerary Item') }}
                                                            </button>
                                                        </h2>
                                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#itineraryAccordion">
                                                            <div class="accordion-body">
                                                                <form action="{{ route('agency.tourbooking.services.itinerary.store', $service->id) }}" method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Title') }} *</label>
                                                                                <input class="crancy__item-input" type="text" name="title" value="{{ old('title') }}" required>
                                                                                @error('title')
                                                                                    <span class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Day') }} *</label>
                                                                                <input class="crancy__item-input" type="number" min="1" name="day" value="{{ old('day', $service->itineraryItems->count() + 1) }}" required>
                                                                                @error('day')
                                                                                    <span class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Duration') }}</label>
                                                                                <input class="crancy__item-input" type="text" name="duration" value="{{ old('duration') }}" placeholder="e.g. 2 hours, Full day">
                                                                                @error('duration')
                                                                                    <span class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 col-md-12 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Description') }} *</label>
                                                                                <textarea class="crancy__item-input summernote" name="description" rows="5">{{ old('description') }}</textarea>
                                                                                @error('description')
                                                                                    <span class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Image') }}</label>
                                                                                <input class="crancy__item-input" type="file" name="image" accept="image/*">
                                                                                @error('image')
                                                                                    <span class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Meals Included') }}</label>
                                                                                <select class="crancy__item-input select2" name="meals_included[]" multiple="multiple">
                                                                                    <option value="breakfast" {{ old('meals_included') && in_array('breakfast', old('meals_included')) ? 'selected' : '' }}>{{ __('translate.Breakfast') }}</option>
                                                                                    <option value="lunch" {{ old('meals_included') && in_array('lunch', old('meals_included')) ? 'selected' : '' }}>{{ __('translate.Lunch') }}</option>
                                                                                    <option value="dinner" {{ old('meals_included') && in_array('dinner', old('meals_included')) ? 'selected' : '' }}>{{ __('translate.Dinner') }}</option>
                                                                                    <option value="snacks" {{ old('meals_included') && in_array('snacks', old('meals_included')) ? 'selected' : '' }}>{{ __('translate.Snacks') }}</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Status') }}</label>
                                                                                <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                                                    <label class="crancy__item-switch">
                                                                                        <input name="status" type="checkbox" checked>
                                                                                        <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 col-md-12 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Location') }}</label>
                                                                                <input class="crancy__item-input" type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Eiffel Tower, Paris">
                                                                                @error('location')
                                                                                    <span class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12 mg-top-30">
                                                                            <button type="submit" class="crancy-btn">{{ __('translate.Add Itinerary Item') }}</button>
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
                                                <h4 class="crancy-product-card__title">{{ __('translate.Itinerary Items') }}</h4>

                                                @forelse($service->itineraryItems->sortBy('day') as $item)
                                                <div class="card mg-top-20">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ __('translate.Day') }} {{ $item->day }}: {{ $item->title }}</strong>
                                                            @if($item->duration)
                                                                <span class="badge bg-info ms-2">{{ $item->duration }}</span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                                                <i class="fa fa-edit"></i> {{ __('translate.Edit') }}
                                                            </a>
                                                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
                                                                <i class="fa fa-trash"></i> {{ __('translate.Delete') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            @if($item->image)
                                                            <div class="col-md-3">
                                                                <img src="{{ asset($item->image) }}" alt="{{ $item->title }}" class="img-fluid rounded">
                                                            </div>
                                                            <div class="col-md-9">
                                                            @else
                                                            <div class="col-12">
                                                            @endif
                                                                <div class="itinerary-content">
                                                                    {!! $item->description !!}
                                                                </div>

                                                                @if($item->location)
                                                                <div class="mt-3">
                                                                    <strong><i class="fa fa-map-marker"></i> {{ __('translate.Location') }}:</strong> {{ $item->location }}
                                                                </div>
                                                                @endif

                                                                @if($item->meals_included && count(json_decode($item->meals_included)) > 0)
                                                                <div class="mt-2">
                                                                    <strong><i class="fa fa-utensils"></i> {{ __('translate.Meals') }}:</strong>
                                                                    @foreach(json_decode($item->meals_included) as $meal)
                                                                        <span class="badge bg-success ms-1">{{ ucfirst($meal) }}</span>
                                                                    @endforeach
                                                                </div>
                                                                @endif

                                                                <div class="mt-2">
                                                                    <strong>{{ __('translate.Status') }}:</strong>
                                                                    @if($item->status)
                                                                        <span class="badge bg-success">{{ __('translate.Active') }}</span>
                                                                    @else
                                                                        <span class="badge bg-danger">{{ __('translate.Inactive') }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Edit Modal -->
                                                <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel{{ $item->id }}">{{ __('translate.Edit Itinerary Item') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('agency.tourbooking.services.itinerary.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="crancy__item-form--group">
                                                                                <label class="crancy__item-label">{{ __('translate.Title') }} *</label>
                                                                                <input class="crancy__item-input" type="text" name="title" value="{{ $item->title }}" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div class="crancy__item-form--group">
                                                                                <label class="crancy__item-label">{{ __('translate.Day') }} *</label>
                                                                                <input class="crancy__item-input" type="number" min="1" name="day" value="{{ $item->day }}" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div class="crancy__item-form--group">
                                                                                <label class="crancy__item-label">{{ __('translate.Duration') }}</label>
                                                                                <input class="crancy__item-input" type="text" name="duration" value="{{ $item->duration }}">
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 col-md-12 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Description') }} *</label>
                                                                                <textarea class="crancy__item-input summernote" name="description" rows="5">{{ $item->description }}</textarea>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Image') }}</label>
                                                                                @if($item->image)
                                                                                    <div class="mb-2">
                                                                                        <img src="{{ asset($item->image) }}" alt="{{ $item->title }}" style="max-width: 150px; max-height: 100px;" class="img-thumbnail">
                                                                                    </div>
                                                                                @endif
                                                                                <input class="crancy__item-input" type="file" name="image" accept="image/*">
                                                                                <small class="text-muted">{{ __('translate.Leave empty to keep current image') }}</small>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Meals Included') }}</label>
                                                                                <select class="crancy__item-input select2" name="meals_included[]" multiple="multiple">
                                                                                    @php
                                                                                        $meals = $item->meals_included ? json_decode($item->meals_included) : [];
                                                                                    @endphp
                                                                                    <option value="breakfast" {{ in_array('breakfast', $meals) ? 'selected' : '' }}>{{ __('translate.Breakfast') }}</option>
                                                                                    <option value="lunch" {{ in_array('lunch', $meals) ? 'selected' : '' }}>{{ __('translate.Lunch') }}</option>
                                                                                    <option value="dinner" {{ in_array('dinner', $meals) ? 'selected' : '' }}>{{ __('translate.Dinner') }}</option>
                                                                                    <option value="snacks" {{ in_array('snacks', $meals) ? 'selected' : '' }}>{{ __('translate.Snacks') }}</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Status') }}</label>
                                                                                <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                                                    <label class="crancy__item-switch">
                                                                                        <input name="status" type="checkbox" {{ $item->status ? 'checked' : '' }}>
                                                                                        <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 col-md-12 col-12">
                                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                                <label class="crancy__item-label">{{ __('translate.Location') }}</label>
                                                                                <input class="crancy__item-input" type="text" name="location" value="{{ $item->location }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="crancy-btn crancy-btn__default" data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                                                                    <button type="submit" class="crancy-btn">{{ __('translate.Update') }}</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel{{ $item->id }}">{{ __('translate.Confirm Delete') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ __('translate.Are you sure you want to delete this itinerary item?') }}
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="crancy-btn crancy-btn__default" data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                                                                <form action="{{ route('agency.tourbooking.services.itinerary.destroy', $item->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="crancy-btn crancy-btn__danger">{{ __('translate.Delete') }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @empty
                                                <div class="alert alert-info mg-top-20">
                                                    {{ __('translate.No itinerary items found') }}. {{ __('translate.Add your first itinerary item using the form above') }}.
                                                </div>
                                                @endforelse
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
    (function($) {
        "use strict"
        $(document).ready(function () {
            $('.summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            $('.select2').select2({
                placeholder: "{{ __('translate.Select meals included') }}",
                allowClear: true
            });
        });
    })(jQuery);
</script>
@endpush
