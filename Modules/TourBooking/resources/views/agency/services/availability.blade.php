@extends('agency.master_layout')
@section('title')
    <title>{{ __('translate.Availability') }} - {{ $service->translation->title ?? $service->title }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Availability') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Services') }} >>
        {{ __('translate.Availability') }}</p>
@endsection

@push('style_section')
    <link rel="stylesheet" href="{{ asset('global/select2/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .availability-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .availability-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px;
        }

        .availability-card .card-body {
            padding: 20px;
        }

        .availability-date-badge {
            font-size: 14px;
            padding: 5px 10px;
            background: #4e73df;
            color: white;
            border-radius: 20px;
            margin-right: 10px;
        }

        .available-spots {
            background-color: #e8f5e9;
            border-radius: 4px;
            padding: 5px 10px;
            color: #2e7d32;
            display: inline-block;
            font-weight: 500;
        }

        .special-price {
            background-color: #fff3e0;
            border-radius: 4px;
            padding: 5px 10px;
            color: #e65100;
            display: inline-block;
            font-weight: 500;
        }

        .not-available {
            background-color: #ffebee;
            border-radius: 4px;
            padding: 5px 10px;
            color: #c62828;
            display: inline-block;
            font-weight: 500;
        }

        .crancy__item-form--currency {
            position: relative;
            display: flex;
            align-items: center;
        }

        .crancy__item-form--currency .crancy__item-input {
            width: 100%;
            padding-right: 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 40px 10px 12px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .crancy__currency-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            z-index: 2;
        }

        .crancy__currency-icon span {
            font-size: 14px;
            color: #666;
            font-weight: 500;
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
                                            <h4 class="crancy-product-card__title">{{ __('translate.Availability for') }}:
                                                {{ $service->translation->title ?? $service->title }}</h4>
                                            <div>
                                                <a href="{{ route('agency.tourbooking.services.edit', ['service' => $service->id, 'lang_code' => admin_lang()]) }}"
                                                    class="crancy-btn"><i class="fa fa-edit"></i>
                                                    {{ __('translate.Edit Service') }}</a>
                                                <a href="{{ route('agency.tourbooking.services.index') }}"
                                                    class="crancy-btn"><i class="fa fa-list"></i>
                                                    {{ __('translate.Service List') }}</a>
                                            </div>
                                        </div>

                                        <div class="row mg-top-30">
                                            <div class="col-12">
                                                <div class="accordion" id="availabilityAccordion">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                                aria-expanded="true" aria-controls="collapseOne">
                                                                {{ __('translate.Add New Availability') }}
                                                            </button>
                                                        </h2>
                                                        <div id="collapseOne" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingOne"
                                                            data-bs-parent="#availabilityAccordion">
                                                            <div class="accordion-body">
                                                                <form
                                                                    action="{{ route('agency.tourbooking.services.availability.store', $service->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-4 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Date') }}
                                                                                    *</label>
                                                                                <input class="crancy__item-input datepicker"
                                                                                    type="text" name="date"
                                                                                    value="{{ old('date') }}"
                                                                                    placeholder="YYYY-MM-DD" required>
                                                                                @error('date')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-2 col-md-4 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Start Time') }}</label>
                                                                                <input class="crancy__item-input timepicker"
                                                                                    type="text" name="start_time"
                                                                                    value="{{ old('start_time') }}"
                                                                                    placeholder="HH:MM">
                                                                                @error('start_time')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-2 col-md-4 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.End Time') }}</label>
                                                                                <input class="crancy__item-input timepicker"
                                                                                    type="text" name="end_time"
                                                                                    value="{{ old('end_time') }}"
                                                                                    placeholder="HH:MM">
                                                                                @error('end_time')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-2 col-md-3 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Available Spots') }}</label>
                                                                                <input class="crancy__item-input"
                                                                                    type="number" name="available_spots"
                                                                                    value="{{ old('available_spots') }}"
                                                                                    min="0">
                                                                                @error('available_spots')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Special Price') }}</label>
                                                                                <div class="crancy__item-form--currency">
                                                                                    <input class="crancy__item-input"
                                                                                        type="number" step="0.01"
                                                                                        name="special_price"
                                                                                        value="{{ old('special_price') }}"
                                                                                        min="0">
                                                                                    <div class="crancy__currency-icon">
                                                                                        <span>{{ config('settings.currency_icon', '$') }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                @error('special_price')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-9 col-md-6 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Notes') }}</label>
                                                                                <textarea class="crancy__item-input" name="notes" rows="3">{{ old('notes') }}</textarea>
                                                                                @error('notes')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-6 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Is Available') }}</label>
                                                                                <div
                                                                                    class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                                                    <label class="crancy__item-switch">
                                                                                        <input name="is_available"
                                                                                            type="checkbox" checked
                                                                                            value="1">
                                                                                        <span
                                                                                            class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12 mg-top-30">
                                                                            <button type="submit"
                                                                                class="crancy-btn">{{ __('translate.Add Availability') }}</button>
                                                                            <button type="button" class="crancy-btn"
                                                                                id="btnBulkAdd">{{ __('translate.Add Multiple Dates') }}</button>
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
                                                <h4 class="crancy-product-card__title">
                                                    {{ __('translate.Existing Availability') }}</h4>

                                                <div class="table-responsive mg-top-20">
                                                    <table class="table" id="availabilityTable">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('translate.Date') }}</th>
                                                                <th>{{ __('translate.Time') }}</th>
                                                                <th>{{ __('translate.Status') }}</th>
                                                                <th>{{ __('translate.Available Spots') }}</th>
                                                                <th>{{ __('translate.Special Price') }}</th>
                                                                <th>{{ __('translate.Notes') }}</th>
                                                                <th>{{ __('translate.Actions') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($service->availabilities->sortBy('date') as $availability)
                                                                <tr>
                                                                    <td>{{ \Carbon\Carbon::parse($availability->date)->format('M d, Y') }}
                                                                    </td>
                                                                    <td>
                                                                        @if ($availability->start_time)
                                                                            {{ \Carbon\Carbon::parse($availability->start_time)->format('H:i') }}
                                                                            @if ($availability->end_time)
                                                                                -
                                                                                {{ \Carbon\Carbon::parse($availability->end_time)->format('H:i') }}
                                                                            @endif
                                                                        @else
                                                                            {{ __('translate.All Day') }}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($availability->is_available)
                                                                            <span
                                                                                class="badge bg-success">{{ __('translate.Available') }}</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-danger">{{ __('translate.Not Available') }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($availability->available_spots)
                                                                            {{ $availability->available_spots }}
                                                                        @else
                                                                            <span
                                                                                class="text-muted">{{ __('translate.Unlimited') }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($availability->special_price)
                                                                            {{ config('settings.currency_icon', '$') }}{{ $availability->special_price }}
                                                                        @else
                                                                            <span
                                                                                class="text-muted">{{ __('translate.Default') }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($availability->notes)
                                                                            <span data-bs-toggle="tooltip"
                                                                                title="{{ $availability->notes }}">
                                                                                {{ Str::limit($availability->notes, 30) }}
                                                                            </span>
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="crancy-btn"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#editModal{{ $availability->id }}">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>
                                                                        <button type="button"
                                                                            class="crancy-btn delete_danger_btn"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#deleteModal{{ $availability->id }}">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>

                                                                <!-- Edit Modal -->
                                                                <div class="modal fade"
                                                                    id="editModal{{ $availability->id }}" tabindex="-1"
                                                                    aria-labelledby="editModalLabel{{ $availability->id }}"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog modal-lg">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="editModalLabel{{ $availability->id }}">
                                                                                    {{ __('translate.Edit Availability') }}
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <form
                                                                                action="{{ route('agency.tourbooking.services.availability.update', $availability->id) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <div class="modal-body">
                                                                                    <div class="row">
                                                                                        <div
                                                                                            class="col-lg-3 col-md-4 col-12">
                                                                                            <div
                                                                                                class="crancy__item-form--group">
                                                                                                <label
                                                                                                    class="crancy__item-label">{{ __('translate.Date') }}
                                                                                                    *</label>
                                                                                                <input
                                                                                                    class="crancy__item-input datepicker"
                                                                                                    type="text"
                                                                                                    name="date"
                                                                                                    value="{{ $availability->date }}"
                                                                                                    placeholder="YYYY-MM-DD"
                                                                                                    required>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div
                                                                                            class="col-lg-2 col-md-4 col-12">
                                                                                            <div
                                                                                                class="crancy__item-form--group">
                                                                                                <label
                                                                                                    class="crancy__item-label">{{ __('translate.Start Time') }}</label>
                                                                                                <input
                                                                                                    class="crancy__item-input timepicker"
                                                                                                    type="text"
                                                                                                    name="start_time"
                                                                                                    value="{{ $availability->start_time ? \Carbon\Carbon::parse($availability->start_time)->format('H:i') : '' }}"
                                                                                                    placeholder="HH:MM">
                                                                                            </div>
                                                                                        </div>

                                                                                        <div
                                                                                            class="col-lg-2 col-md-4 col-12">
                                                                                            <div
                                                                                                class="crancy__item-form--group">
                                                                                                <label
                                                                                                    class="crancy__item-label">{{ __('translate.End Time') }}</label>
                                                                                                <input
                                                                                                    class="crancy__item-input timepicker"
                                                                                                    type="text"
                                                                                                    name="end_time"
                                                                                                    value="{{ $availability->end_time ? \Carbon\Carbon::parse($availability->end_time)->format('H:i') : '' }}"
                                                                                                    placeholder="HH:MM">
                                                                                            </div>
                                                                                        </div>

                                                                                        <div
                                                                                            class="col-lg-2 col-md-3 col-12">
                                                                                            <div
                                                                                                class="crancy__item-form--group">
                                                                                                <label
                                                                                                    class="crancy__item-label">{{ __('translate.Available Spots') }}</label>
                                                                                                <input
                                                                                                    class="crancy__item-input"
                                                                                                    type="number"
                                                                                                    name="available_spots"
                                                                                                    value="{{ $availability->available_spots }}"
                                                                                                    min="0">
                                                                                            </div>
                                                                                        </div>

                                                                                        <div
                                                                                            class="col-lg-3 col-md-3 col-12">
                                                                                            <div
                                                                                                class="crancy__item-form--group">
                                                                                                <label
                                                                                                    class="crancy__item-label">{{ __('translate.Special Price') }}</label>
                                                                                                <div
                                                                                                    class="crancy__item-form--currency">
                                                                                                    <input
                                                                                                        class="crancy__item-input"
                                                                                                        type="number"
                                                                                                        step="0.01"
                                                                                                        name="special_price"
                                                                                                        value="{{ $availability->special_price }}"
                                                                                                        min="0">
                                                                                                    <div
                                                                                                        class="crancy__currency-icon">
                                                                                                        <span>{{ config('settings.currency_icon', '$') }}</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div
                                                                                            class="col-lg-9 col-md-6 col-12">
                                                                                            <div
                                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                                <label
                                                                                                    class="crancy__item-label">{{ __('translate.Notes') }}</label>
                                                                                                <textarea class="crancy__item-input" name="notes" rows="3">{{ $availability->notes }}</textarea>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div
                                                                                            class="col-lg-3 col-md-6 col-12">
                                                                                            <div
                                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                                <label
                                                                                                    class="crancy__item-label">{{ __('translate.Is Available') }}</label>
                                                                                                <div
                                                                                                    class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                                                                    <label
                                                                                                        class="crancy__item-switch">
                                                                                                        <input
                                                                                                            name="is_available"
                                                                                                            type="checkbox"
                                                                                                            {{ $availability->is_available ? 'checked' : '' }}
                                                                                                            value="1">
                                                                                                        <span
                                                                                                            class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                                                                    </label>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button"
                                                                                        class="crancy-btn crancy-btn__default"
                                                                                        data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                                                                                    <button type="submit"
                                                                                        class="crancy-btn">{{ __('translate.Update') }}</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Delete Modal -->
                                                                <div class="modal fade"
                                                                    id="deleteModal{{ $availability->id }}"
                                                                    tabindex="-1"
                                                                    aria-labelledby="deleteModalLabel{{ $availability->id }}"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="deleteModalLabel{{ $availability->id }}">
                                                                                    {{ __('translate.Confirm Delete') }}
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                {{ __('translate.Are you sure you want to delete this availability date?') }}
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                    class="crancy-btn crancy-btn__default"
                                                                                    data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                                                                                <form
                                                                                    action="{{ route('agency.tourbooking.services.availability.destroy', $availability->id) }}"
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
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center">
                                                                        {{ __('translate.No availability dates found. Add your first date using the form above.') }}
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
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
        </div>
    </section>

    <!-- Bulk Add Modal -->
    <div class="modal fade" id="bulkAddModal" tabindex="-1" aria-labelledby="bulkAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkAddModalLabel">{{ __('translate.Add Multiple Dates') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('agency.tourbooking.services.availability.store', $service->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="bulk_add" value="1">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="crancy__item-form--group">
                                    <label class="crancy__item-label">{{ __('translate.Start Date') }} *</label>
                                    <input class="crancy__item-input datepicker" type="text" name="date"
                                        placeholder="YYYY-MM-DD" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="crancy__item-form--group">
                                    <label class="crancy__item-label">{{ __('translate.End Date') }} *</label>
                                    <input class="crancy__item-input datepicker" type="text" name="end_date"
                                        placeholder="YYYY-MM-DD" required>
                                </div>
                            </div>

                            <div class="col-md-12 mg-top-form-20">
                                <div class="crancy__item-form--group">
                                    <label class="crancy__item-label">{{ __('translate.Days of Week') }}</label>
                                    <div class="d-flex flex-wrap">
                                        <div class="form-check me-3 mb-2">
                                            <input class="form-check-input" type="checkbox" name="days[]"
                                                value="monday" id="monday" checked>
                                            <label class="form-check-label"
                                                for="monday">{{ __('translate.Monday') }}</label>
                                        </div>
                                        <div class="form-check me-3 mb-2">
                                            <input class="form-check-input" type="checkbox" name="days[]"
                                                value="tuesday" id="tuesday" checked>
                                            <label class="form-check-label"
                                                for="tuesday">{{ __('translate.Tuesday') }}</label>
                                        </div>
                                        <div class="form-check me-3 mb-2">
                                            <input class="form-check-input" type="checkbox" name="days[]"
                                                value="wednesday" id="wednesday" checked>
                                            <label class="form-check-label"
                                                for="wednesday">{{ __('translate.Wednesday') }}</label>
                                        </div>
                                        <div class="form-check me-3 mb-2">
                                            <input class="form-check-input" type="checkbox" name="days[]"
                                                value="thursday" id="thursday" checked>
                                            <label class="form-check-label"
                                                for="thursday">{{ __('translate.Thursday') }}</label>
                                        </div>
                                        <div class="form-check me-3 mb-2">
                                            <input class="form-check-input" type="checkbox" name="days[]"
                                                value="friday" id="friday" checked>
                                            <label class="form-check-label"
                                                for="friday">{{ __('translate.Friday') }}</label>
                                        </div>
                                        <div class="form-check me-3 mb-2">
                                            <input class="form-check-input" type="checkbox" name="days[]"
                                                value="saturday" id="saturday" checked>
                                            <label class="form-check-label"
                                                for="saturday">{{ __('translate.Saturday') }}</label>
                                        </div>
                                        <div class="form-check me-3 mb-2">
                                            <input class="form-check-input" type="checkbox" name="days[]"
                                                value="sunday" id="sunday" checked>
                                            <label class="form-check-label"
                                                for="sunday">{{ __('translate.Sunday') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mg-top-form-20">
                                <div class="crancy__item-form--group">
                                    <label class="crancy__item-label">{{ __('translate.Start Time') }}</label>
                                    <input class="crancy__item-input timepicker" type="text" name="start_time"
                                        placeholder="HH:MM">
                                </div>
                            </div>
                            <div class="col-md-6 mg-top-form-20">
                                <div class="crancy__item-form--group">
                                    <label class="crancy__item-label">{{ __('translate.End Time') }}</label>
                                    <input class="crancy__item-input timepicker" type="text" name="end_time"
                                        placeholder="HH:MM">
                                </div>
                            </div>

                            <div class="col-md-6 mg-top-form-20">
                                <div class="crancy__item-form--group">
                                    <label class="crancy__item-label">{{ __('translate.Available Spots') }}</label>
                                    <input class="crancy__item-input" type="number" name="available_spots"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-6 mg-top-form-20">
                                <div class="crancy__item-form--group">
                                    <label class="crancy__item-label">{{ __('translate.Special Price') }}</label>
                                    <div class="crancy__item-form--currency">
                                        <input class="crancy__item-input" type="number" step="0.01"
                                            name="special_price" min="0">
                                        <div class="crancy__currency-icon">
                                            <span>{{ config('settings.currency_icon', '$') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mg-top-form-20">
                                <div class="crancy__item-form--group">
                                    <label class="crancy__item-label">{{ __('translate.Is Available') }}</label>
                                    <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                        <label class="crancy__item-switch">
                                            <input name="is_available" type="checkbox" checked value="1">
                                            <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mg-top-form-20">
                                <div class="crancy__item-form--group">
                                    <label class="crancy__item-label">{{ __('translate.Notes') }}</label>
                                    <textarea class="crancy__item-input" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="crancy-btn crancy-btn__default"
                            data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                        <button type="submit" class="crancy-btn">{{ __('translate.Add Dates') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js_section')
    <script src="{{ asset('global/select2/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (function($) {
            "use strict"
            $(document).ready(function() {
                // Initialize datepicker
                $(".datepicker").flatpickr({
                    dateFormat: "Y-m-d",
                    allowInput: true
                });

                // Initialize timepicker
                $(".timepicker").flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true
                });

                // Initialize tooltips
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Initialize DataTable
                $('#availabilityTable').DataTable({
                    responsive: true,
                    ordering: true,
                    paging: true,
                    searching: true,
                    info: true,
                });

                // Bulk Add Button
                $('#btnBulkAdd').on('click', function() {
                    $('#bulkAddModal').modal('show');
                });
            });
        })(jQuery);
    </script>
@endpush
