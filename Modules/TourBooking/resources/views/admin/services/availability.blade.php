@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Service Availability') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Service Availability Management') }}</h3>
    <p class="crancy-header__text">
        {{ __('translate.Manage Availability') }} >> {{ $service->title }}
    </p>
@endsection

@push('style_section')
<link rel="stylesheet" href="{{ asset('global/select2/select2.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
  /* === Card framework (ca la edit/create) === */
  .section-card + .section-card{ margin-top:24px; }
  .crancy-card__head{ padding:18px 20px; border-bottom:1px solid var(--border); }
  .crancy-card__title{ margin:0; font-weight:700; font-size:var(--fs-xl); color:var(--g-900); }
  .crancy-card__body{ padding:20px; }

  .create_new_btn_inline_box{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
  }

  /* === Age Categories === */
  .agecat-card{
    border:1px solid var(--border); border-radius:16px;
    padding:16px; background:#fff; box-shadow:var(--shadow-1);
    margin:16px 0;
  }
  .agecat-card .d-flex.align-items-center.justify-content-between{ gap:12px; }
  .agecat-card .form-check{
    margin-left:auto; display:inline-flex; align-items:center; gap:8px; white-space:nowrap;
  }
  .agecat-card .form-check-input{ margin-top:0; }
  .agecat-card.disabled{ opacity:.55; }

  /* Currency adornment */
  .crancy__item-form--currency{ position:relative; display:flex; align-items:center; }
  .crancy__item-form--currency .crancy__item-input{ padding-right:44px; }
  .crancy__currency-icon{
    position:absolute; right:12px; top:50%; transform:translateY(-50%);
    pointer-events:none; font-weight:700; color:#111827; opacity:.8;
  }

  /* Availability bits */
  .availability-calendar{ margin-top:20px; }
  .availability-legend{ display:flex; align-items:center; gap:20px; margin-bottom:20px; }
  .legend-item{ display:flex; align-items:center; gap:6px; }
  .legend-color{ width:20px; height:20px; border-radius:3px; }
  .legend-available{ background:#4caf50; }
  .legend-unavailable{ background:#f44336; }
  .legend-limited{ background:#ff9800; }

  .date-range-select{ margin-bottom:20px; padding:15px; background:#f9f9f9; border-radius:12px; border:1px solid #e0e0e0; }
  .date-range-title{ font-weight:600; margin-bottom:10px; }
  .selected-dates{ margin-top:15px; padding:10px; background:#f0f7ff; border:1px dashed #c0d6f9; border-radius:10px; display:none; }

  .availability-flex{ display:flex; align-items:center; gap:6px; }

  .visually-hidden{ display:none !important; }
  .flatpickr-months .flatpickr-month{ height:108px; }
</style>
@endpush

@section('body-content')
<section class="crancy-adashboard crancy-show">
  <div class="container container__bscreen">
      <br>
    <div class="row">
        <br>
      <div class="col-12">
        <div class="crancy-body">
          <div class="crancy-dsinner">

            {{-- ===== Header Card ===== --}}
            <div class="crancy-card section-card">
              <div class="crancy-card__head">
                <div class="create_new_btn_inline_box">
                  <h4 class="crancy-card__title">{{ __('translate.Service Availability') }}</h4>
                  <div class="d-flex gap-2">
                    <a href="{{ route('admin.tourbooking.services.edit', $service) }}" class="crancy-btn">
                      <i class="fa fa-edit"></i> {{ __('translate.Edit Service') }}
                    </a>
                    <a href="{{ route('admin.tourbooking.services.index') }}" class="crancy-btn">
                      <i class="fa fa-list"></i> {{ __('translate.Service List') }}
                    </a>
                  </div>
                </div>
              </div>
              <div class="crancy-card__body">

                {{-- Legendă --}}
                <div class="availability-legend">
                  <div class="legend-item"><span class="legend-color legend-available"></span><span>{{ __('translate.Available') }}</span></div>
                  <div class="legend-item"><span class="legend-color legend-unavailable"></span><span>{{ __('translate.Unavailable') }}</span></div>
                  <div class="legend-item"><span class="legend-color legend-limited"></span><span>{{ __('translate.Limited Spots') }}</span></div>
                </div>

                {{-- Bulk range select --}}
                <div class="date-range-select">
                  <h5 class="date-range-title">{{ __('translate.Bulk Date Selection') }}</h5>
                  <div class="row">
                    <div class="col-md-6">
                      <label class="crancy__item-label">{{ __('translate.Start Date') }}</label>
                      <input type="text" id="startDate" class="crancy__item-input" placeholder="{{ __('translate.Select start date') }}">
                    </div>
                    <div class="col-md-6">
                      <label class="crancy__item-label">{{ __('translate.End Date') }}</label>
                      <input type="text" id="endDate" class="crancy__item-input" placeholder="{{ __('translate.Select end date') }}">
                    </div>
                  </div>

                  <div class="row mg-top-20">
                    <div class="col-md-6">
                      <label class="crancy__item-label">{{ __('translate.Days of Week') }}</label>
                      <div class="mt-2">
                        @php $days = ['sun','mon','tue','wed','thu','fri','sat']; @endphp
                        @foreach($days as $i => $d)
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="day-{{ $d }}" value="{{ $i }}" checked>
                            <label class="form-check-label" for="day-{{ $d }}">{{ __(ucfirst($d)) }}</label>
                          </div>
                        @endforeach
                      </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end justify-content-md-end">
                      <div class="d-flex gap-2">
                        <button id="generateDatesBtn" class="crancy-btn" type="button">{{ __('translate.Generate Dates') }}</button>
                        <button id="clearSelectionBtn" class="crancy-btn crancy-btn-danger" type="button" style="display:none;">{{ __('translate.Clear Selection') }}</button>
                      </div>
                    </div>
                  </div>

                  <div class="selected-dates" id="selectedDatesContainer">
                    <p class="m-0">{{ __('translate.Selected') }}: <b id="selectedDatesCount">0</b> {{ __('translate.dates') }}</p>
                    <div class="mt-2">
                      <button id="bulkManageBtn" class="crancy-btn" data-bs-toggle="modal" data-bs-target="#bulkManageModal" disabled>
                        {{ __('translate.Manage Selected Dates') }}
                      </button>
                    </div>
                  </div>
                </div>

                {{-- Calendar --}}
                <div id="availabilityCalendar" class="availability-calendar"></div>
              </div>
            </div>

            {{-- ===== Existing Availabilities Table ===== --}}
            <div class="crancy-card section-card">
              <div class="crancy-card__head">
                <h4 class="crancy-card__title">{{ __('translate.Configured Availabilities') }}</h4>
              </div>
              <div class="crancy-card__body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>{{ __('translate.Date') }}</th>
                        <th>{{ __('translate.Start Time') }}</th>
                        <th>{{ __('translate.End Time') }}</th>
                        <th>{{ __('translate.Status') }}</th>
                        <th>{{ __('translate.Available Spots') }}</th>
                        <th>{{ __('translate.Age-cat Pricing') }}</th>
                        <th>{{ __('translate.Notes') }}</th>
                        <th>{{ __('translate.Action') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($service->availabilities as $availability)
                        @php
                          $ac = is_array($availability->age_categories ?? null) ? $availability->age_categories : [];
                          $anyAge = false;
                          foreach ($ac as $v) { if (!empty($v['enabled'])) { $anyAge = true; break; } }

                          $summary = [];
                          if ($anyAge) {
                            foreach (['adult','child','baby','infant'] as $k) {
                              if (data_get($ac,"$k.enabled")) {
                                $p = data_get($ac,"$k.price");
                                if ($p !== null && $p !== '') {
                                  $summary[] = ucfirst($k).': '.currency($p);
                                }
                              }
                            }
                          }
                        @endphp
                        <tr>
                          <td>{{ \Carbon\Carbon::parse($availability->date)->format('d M Y') }}</td>
                          <td>{{ $availability->start_time ? \Carbon\Carbon::parse($availability->start_time)->format('H:i') : 'N/A' }}</td>
                          <td>{{ $availability->end_time ? \Carbon\Carbon::parse($availability->end_time)->format('H:i') : 'N/A' }}</td>
                          <td>
                            @if ($availability->is_available)
                              <span class="badge bg-success">{{ __('translate.Available') }}</span>
                            @else
                              <span class="badge bg-danger">{{ __('translate.Unavailable') }}</span>
                            @endif
                          </td>
                          <td>
                            @if (!is_null($availability->available_spots))
                              {{ $availability->available_spots }}
                            @else
                              <span class="text-muted">{{ __('translate.Unlimited') }}</span>
                            @endif
                          </td>
                          <td>
                            @if ($anyAge && count($summary))
                              {{ implode(', ', $summary) }}
                            @else
                              <span class="text-muted">{{ __('translate.Standard') }}</span>
                            @endif
                          </td>
                          <td>
                            @if ($availability->notes)
                              {{ \Illuminate\Support\Str::limit($availability->notes, 30) }}
                            @else
                              <span class="text-muted">-</span>
                            @endif
                          </td>
                          <td class="availability-flex">
                            <button type="button"
                              class="btn btn-sm btn-primary edit-availability"
                              data-id="{{ $availability->id }}"
                              data-date="{{ $availability->date }}"
                              data-start-time="{{ $availability->start_time ? \Carbon\Carbon::parse($availability->start_time)->format('H:i') : '' }}"
                              data-end-time="{{ $availability->end_time ? \Carbon\Carbon::parse($availability->end_time)->format('H:i') : '' }}"
                              data-is-available="{{ $availability->is_available ? '1' : '0' }}"
                              data-available-spots="{{ $availability->available_spots }}"
                              data-age-categories='@json($availability->age_categories ?? [])'
                              data-notes="{{ $availability->notes }}"
                              data-bs-toggle="modal"
                              data-bs-target="#editAvailabilityModal">
                              <i class="fa fa-edit"></i>
                            </button>
                            <button type="button"
                              class="btn btn-sm btn-danger delete-availability"
                              data-id="{{ $availability->id }}"
                              data-date="{{ \Carbon\Carbon::parse($availability->date)->format('d M Y') }}">
                              <i class="fa fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="8" class="text-center text-muted">{{ __('translate.No availabilities configured') }}</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            {{-- ===== Add Single Availability ===== --}}
            @php
              $ageLabels = [
                'adult'  => __('Adult'),
                'child'  => __('Child'),
                'baby'   => __('Baby'),
                'infant' => __('Infant'),
              ];
              $minDefaults = ['infant'=>0,'baby'=>0,'child'=>2,'adult'=>18];
              $maxDefaults = ['infant'=>1,'baby'=>2,'child'=>12,'adult'=>120];
            @endphp

            <div class="crancy-card section-card">
              <div class="crancy-card__head">
                <h4 class="crancy-card__title">{{ __('translate.Add Single Date Availability') }}</h4>
              </div>
              <div class="crancy-card__body">
                <form action="{{ route('admin.tourbooking.services.availability.store', $service) }}" method="POST">
                  @csrf
                  <div class="row">
                    <div class="col-md-4">
                      <label class="crancy__item-label">{{ __('translate.Date') }} *</label>
                      <input type="text" name="date" class="crancy__item-input datepicker" required>
                    </div>
                    <div class="col-md-4">
                      <label class="crancy__item-label">{{ __('translate.Start Time') }}</label>
                      <input type="text" name="start_time" class="crancy__item-input timepicker">
                    </div>
                    <div class="col-md-4">
                      <label class="crancy__item-label">{{ __('translate.End Time') }}</label>
                      <input type="text" name="end_time" class="crancy__item-input timepicker">
                    </div>
                  </div>

                  <div class="row mg-top-20">
                    <div class="col-md-4">
                      <label class="crancy__item-label">{{ __('translate.Available Spots') }}</label>
                      <input type="number" name="available_spots" class="crancy__item-input" min="1" placeholder="{{ __('translate.Leave empty for unlimited') }}">
                    </div>
                  </div>

                  {{-- Age Categories & Pricing (Add) --}}
                  <div id="ageCatsAdd" class="mg-top-20">
                    <h5 class="mb-2">{{ __('Age Categories & Pricing') }}</h5>
                    <br>
                    <p class="text-muted" style="margin-top:-6px">{{ __('Enable categories that apply and set per-person pricing for the selected date.') }}</p>

                    @foreach($ageLabels as $key => $label)
                      <div class="agecat-card" data-scope="add">
                        <div class="d-flex align-items-center justify-content-between">
                          <label class="crancy__item-label mb-0">{{ $label }}</label>
                          <div class="form-check">
                            <input class="form-check-input toggle-agecat"
                                   type="checkbox"
                                   id="add_agecat_{{ $key }}_enabled"
                                   name="age_categories[{{ $key }}][enabled]"
                                   value="1"
                                   {{ $key === 'adult' ? 'checked' : '' }}
                                   data-target="#add_fields_{{ $key }}">
                            <label class="form-check-label" for="add_agecat_{{ $key }}_enabled">{{ __('Enable Category') }}</label>
                          </div>
                        </div>

                        <div id="add_fields_{{ $key }}" class="row mt-3">
                          <div class="col-lg-3 col-md-6 col-12">
                            <label class="crancy__item-label">{{ __('Count') }}</label>
                            <input class="crancy__item-input" type="number" min="0"
                                   name="age_categories[{{ $key }}][count]"
                                   value="{{ $key === 'adult' ? 1 : 0 }}"
                                   placeholder="{{ $key === 'adult' ? '1' : '0' }}">
                          </div>
                          <div class="col-lg-3 col-md-6 col-12">
                            <label class="crancy__item-label">{{ __('Price (per person)') }}</label>
                            <div class="crancy__item-form--currency">
                              <input class="crancy__item-input" type="number" step="0.01" min="0"
                                     name="age_categories[{{ $key }}][price]" placeholder="0.00">
                              <div class="crancy__currency-icon"><span>{{ config('settings.currency_icon', '$') }}</span></div>
                            </div>
                          </div>
                          <div class="col-lg-3 col-md-6 col-12">
                            <label class="crancy__item-label">{{ __('Min Age (years)') }}</label>
                            <input class="crancy__item-input" type="number" min="0" step="1"
                                   name="age_categories[{{ $key }}][min_age]"
                                   value="{{ isset($minDefaults[$key]) ? $minDefaults[$key] : 0 }}">
                          </div>
                          <div class="col-lg-3 col-md-6 col-12">
                            <label class="crancy__item-label">{{ __('Max Age (years)') }}</label>
                            <input class="crancy__item-input" type="number" min="0" step="1"
                                   name="age_categories[{{ $key }}][max_age]"
                                   value="{{ isset($maxDefaults[$key]) ? $maxDefaults[$key] : 0 }}">
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="row mg-top-20">
                    <div class="col-md-4">
                      <label class="crancy__item-label d-block">{{ __('translate.Status') }}</label>
                      <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                        <label class="crancy__item-switch">
                          <input type="checkbox" name="is_available" id="is_available" value="1" checked>
                          <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                        </label>
                      </div>
                    </div>
                    <div class="col-12">
                      <label class="crancy__item-label">{{ __('translate.Notes') }}</label>
                      <textarea name="notes" class="crancy__item-input" rows="3"></textarea>
                    </div>
                  </div>

                  <div class="row mg-top-20">
                    <div class="col-12">
                      <button type="submit" class="crancy-btn">{{ __('translate.Add Availability') }}</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

          </div>{{-- /.crancy-dsinner --}}
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ===== Edit Availability Modal (FĂRĂ câmpuri legacy price) ===== --}}
<div class="modal fade" id="editAvailabilityModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('translate.Edit Availability') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editAvailabilityForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <label class="crancy__item-label">{{ __('translate.Date') }}</label>
              <input type="text" id="edit_date" class="crancy__item-input datepicker" disabled>
            </div>
            <div class="col-md-4">
              <label class="crancy__item-label">{{ __('translate.Start Time') }}</label>
              <input type="text" name="start_time" id="edit_start_time" class="crancy__item-input timepicker">
            </div>
            <div class="col-md-4">
              <label class="crancy__item-label">{{ __('translate.End Time') }}</label>
              <input type="text" name="end_time" id="edit_end_time" class="crancy__item-input timepicker">
            </div>
          </div>

          <div class="row mg-top-20">
            <div class="col-md-4">
              <label class="crancy__item-label">{{ __('translate.Available Spots') }}</label>
              <input type="number" name="available_spots" id="edit_available_spots" class="crancy__item-input" min="1" placeholder="{{ __('translate.Leave empty for unlimited') }}">
            </div>
          </div>

          {{-- Age Categories & Pricing (Edit) --}}
          @php
            $editLabels = ['adult'=>__('Adult'),'child'=>__('Child'),'baby'=>__('Baby'),'infant'=>__('Infant')];
          @endphp
          <div id="ageCatsEdit" class="mg-top-20">
            <h6 class="mb-2">{{ __('Age Categories & Pricing') }}</h6>
            @foreach($editLabels as $key => $label)
              <div class="agecat-card" data-scope="edit" data-key="{{ $key }}">
                <div class="d-flex align-items-center justify-content-between">
                  <label class="crancy__item-label mb-0">{{ $label }}</label>
                  <div class="form-check">
                    <input class="form-check-input toggle-agecat"
                           type="checkbox"
                           id="edit_agecat_{{ $key }}_enabled"
                           name="age_categories[{{ $key }}][enabled]"
                           value="1"
                           data-target="#edit_fields_{{ $key }}">
                    <label class="form-check-label" for="edit_agecat_{{ $key }}_enabled">{{ __('Enable Category') }}</label>
                  </div>
                </div>

                <div id="edit_fields_{{ $key }}" class="row mt-3">
                  <div class="col-lg-3 col-md-6 col-12">
                    <label class="crancy__item-label">{{ __('Count') }}</label>
                    <input class="crancy__item-input" type="number" min="0"
                           name="age_categories[{{ $key }}][count]" placeholder="0">
                  </div>
                  <div class="col-lg-3 col-md-6 col-12">
                    <label class="crancy__item-label">{{ __('Price (per person)') }}</label>
                    <div class="crancy__item-form--currency">
                      <input class="crancy__item-input" type="number" step="0.01" min="0"
                             name="age_categories[{{ $key }}][price]" placeholder="0.00">
                      <div class="crancy__currency-icon"><span>{{ config('settings.currency_icon', '$') }}</span></div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6 col-12">
                    <label class="crancy__item-label">{{ __('Min Age (years)') }}</label>
                    <input class="crancy__item-input" type="number" min="0" step="1"
                           name="age_categories[{{ $key }}][min_age]"
                           value="{{ isset($minDefaults[$key]) ? $minDefaults[$key] : 0 }}">
                  </div>
                  <div class="col-lg-3 col-md-6 col-12">
                    <label class="crancy__item-label">{{ __('Max Age (years)') }}</label>
                    <input class="crancy__item-input" type="number" min="0" step="1"
                           name="age_categories[{{ $key }}][max_age]"
                           value="{{ isset($maxDefaults[$key]) ? $maxDefaults[$key] : 0 }}">
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="row mg-top-20">
            <div class="col-md-4">
              <label class="crancy__item-label d-block">{{ __('translate.Status') }}</label>
              <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                <label class="crancy__item-switch">
                  <input type="checkbox" name="is_available" id="edit_is_available" value="1">
                  <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                </label>
              </div>
            </div>
            <div class="col-12">
              <label class="crancy__item-label">{{ __('translate.Notes') }}</label>
              <textarea name="notes" id="edit_notes" class="crancy__item-input" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('translate.Update') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ===== Bulk Manage Modal (FĂRĂ câmpuri legacy price) ===== --}}
<div class="modal fade" id="bulkManageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('translate.Bulk Manage Availability') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="bulkManageForm" action="{{ route('admin.tourbooking.services.availability.store', $service) }}" method="POST">
        @csrf
        <input type="hidden" name="bulk" value="1">
        {{-- dates[] vor fi injectate din JS --}}
        <div class="modal-body">
          <div class="alert alert-info">
            <p class="m-0">
              {{ __('translate.You are about to configure availability for') }}
              <strong id="bulkDateCount">0</strong> {{ __('translate.dates') }}.
            </p>
          </div>

          <div class="row mg-top-20">
            <div class="col-md-6">
              <label class="crancy__item-label">{{ __('translate.Start Time') }}</label>
              <input type="text" name="start_time" id="bulk_start_time" class="crancy__item-input timepicker">
            </div>
            <div class="col-md-6">
              <label class="crancy__item-label">{{ __('translate.End Time') }}</label>
              <input type="text" name="end_time" id="bulk_end_time" class="crancy__item-input timepicker">
            </div>
          </div>

          <div class="row mg-top-20">
            <div class="col-md-6">
              <label class="crancy__item-label">{{ __('translate.Available Spots') }}</label>
              <input type="number" name="available_spots" id="bulk_available_spots" class="crancy__item-input" min="1" placeholder="{{ __('translate.Leave empty for unlimited') }}">
            </div>
          </div>

          {{-- Age Categories & Pricing (Bulk) --}}
          <div id="ageCatsBulk" class="mg-top-20">
            <h6 class="mb-2">{{ __('Age Categories & Pricing') }}</h6>
            @foreach($editLabels as $key => $label)
              <div class="agecat-card" data-scope="bulk">
                <div class="d-flex align-items-center justify-content-between">
                  <label class="crancy__item-label mb-0">{{ $label }}</label>
                  <div class="form-check">
                    <input class="form-check-input toggle-agecat"
                           type="checkbox"
                           id="bulk_agecat_{{ $key }}_enabled"
                           name="age_categories[{{ $key }}][enabled]"
                           value="1"
                           data-target="#bulk_fields_{{ $key }}">
                    <label class="form-check-label" for="bulk_agecat_{{ $key }}_enabled">{{ __('Enable Category') }}</label>
                  </div>
                </div>
                <div id="bulk_fields_{{ $key }}" class="row mt-3">
                  <div class="col-lg-3 col-md-6 col-12">
                    <label class="crancy__item-label">{{ __('Count') }}</label>
                    <input class="crancy__item-input" type="number" min="0" name="age_categories[{{ $key }}][count]" placeholder="0">
                  </div>
                  <div class="col-lg-3 col-md-6 col-12">
                    <label class="crancy__item-label">{{ __('Price (per person)') }}</label>
                    <div class="crancy__item-form--currency">
                      <input class="crancy__item-input" type="number" step="0.01" min="0" name="age_categories[{{ $key }}][price]" placeholder="0.00">
                      <div class="crancy__currency-icon"><span>{{ config('settings.currency_icon', '$') }}</span></div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6 col-12">
                    <label class="crancy__item-label">{{ __('Min Age (years)') }}</label>
                    <input class="crancy__item-input" type="number" min="0" step="1"
                           name="age_categories[{{ $key }}][min_age]"
                           value="{{ isset($minDefaults[$key]) ? $minDefaults[$key] : 0 }}">
                  </div>
                  <div class="col-lg-3 col-md-6 col-12">
                    <label class="crancy__item-label">{{ __('Max Age (years)') }}</label>
                    <input class="crancy__item-input" type="number" min="0" step="1"
                           name="age_categories[{{ $key }}][max_age]"
                           value="{{ isset($maxDefaults[$key]) ? $maxDefaults[$key] : 0 }}">
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="row mg-top-20">
            <div class="col-md-6">
              <label class="crancy__item-label d-block">{{ __('translate.Status') }}</label>
              <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                <label class="crancy__item-switch">
                  <input type="checkbox" name="is_available" id="bulk_is_available" value="1" checked>
                  <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <label class="crancy__item-label">{{ __('translate.Notes') }}</label>
              <textarea name="notes" id="bulk_notes" class="crancy__item-input" rows="3"></textarea>
            </div>
          </div>
        </div>{{-- /.modal-body --}}
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('translate.Apply to All Selected Dates') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ===== Delete Confirmation Modal ===== --}}
<div class="modal fade" id="deleteAvailabilityModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('translate.Delete Availability') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>{{ __('translate.Are you sure you want to delete availability for') }} <span id="deleteDate"></span>?</p>
      </div>
      <div class="modal-footer">
        <form id="deleteAvailabilityForm" method="POST">
          @csrf
          @method('DELETE')
          <div class="d-flex gap-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
            <button type="submit" class="btn btn-danger">{{ __('translate.Delete') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js_section')
<script src="{{ asset('global/select2/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
(function($){
  "use strict";

  $(function(){

    // ===== Flatpickr =====
    $(".datepicker").flatpickr({ dateFormat: "Y-m-d", minDate: "today" });
    $(".timepicker").flatpickr({
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      time_24hr: true,
      allowInput: true,
      onReady: function(sel, str, inst){
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "flatpickr-clear-btn";
        btn.textContent = "Clear";
        btn.style.marginLeft = "10px";
        btn.onclick = function(){ inst.clear(); };
        inst.calendarContainer.appendChild(btn);
      }
    });

    // ===== Date range pickers =====
    const startDatePicker = flatpickr("#startDate", { dateFormat: "Y-m-d", minDate: "today" });
    const endDatePicker   = flatpickr("#endDate",   { dateFormat: "Y-m-d", minDate: "today" });

    // ===== Selected dates state =====
    let selectedDates = [];

    // ===== Generate dates =====
    $("#generateDatesBtn").on('click', function(){
      const s = startDatePicker.selectedDates[0];
      const e = endDatePicker.selectedDates[0];
      if(!s || !e){ alert(@json(__('translate.Please select both start and end dates'))); return; }

      const keys = ['sun','mon','tue','wed','thu','fri','sat'];
      const days = [];
      for(let i=0;i<=6;i++){ if($('#day-'+keys[i]).is(':checked')) days.push(i); }
      if(!days.length){ alert(@json(__('translate.Please select at least one day of week'))); return; }

      selectedDates = [];
      const cur = new Date(s);
      while(cur <= e){
        if(days.includes(cur.getDay())){
          selectedDates.push(cur.toISOString().split('T')[0]);
        }
        cur.setDate(cur.getDate()+1);
      }
      updateSelectedDatesUI();
    });

    $("#clearSelectionBtn").on('click', function(){
      selectedDates = [];
      updateSelectedDatesUI();
    });

    function updateSelectedDatesUI(){
      const count = selectedDates.length;
      $("#selectedDatesCount").text(count);
      $("#bulkDateCount").text(count);

      if(count > 0){
        $("#selectedDatesContainer").show();
        $("#clearSelectionBtn").show();
        $("#bulkManageBtn").prop('disabled', false);

        // Replace previous hidden inputs
        $('#bulkManageForm input[name="dates[]"]').remove();
        selectedDates.forEach(d=>{
          $('<input>',{type:'hidden',name:'dates[]',value:d}).appendTo('#bulkManageForm');
        });
      }else{
        $("#selectedDatesContainer").hide();
        $("#clearSelectionBtn").hide();
        $("#bulkManageBtn").prop('disabled', true);
      }
    }

    // ===== FullCalendar =====
    const calendarEl = document.getElementById('availabilityCalendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,dayGridWeek' },
      events: [
        @foreach ($service->availabilities as $availability)
          @php
            $spots   = $availability->available_spots;
            $isAvail = (bool) $availability->is_available;
            $hasSpots = is_null($spots) ? true : ((int) $spots > 0);
            $color = $isAvail ? ($hasSpots ? '#4caf50' : '#ff9800') : '#f44336';
            $title = $isAvail
                ? (is_null($spots) ? __('translate.Available') : ($spots.' '.__('translate.spots')))
                : __('translate.Unavailable');
          @endphp
          {
            title: @json($title),
            start: @json($availability->date),
            color: @json($color),
            extendedProps: { availabilityId: @json($availability->id) }
          },
        @endforeach
      ],
      eventClick: function(info){
        const id = info.event.extendedProps.availabilityId;
        const btn = $(`.edit-availability[data-id="${id}"]`);
        if(btn.length) btn.trigger('click');
      }
    });
    calendar.render();

    // ====== Delete flow ======
    $('.delete-availability').on('click', function(){
      const id   = $(this).data('id');
      const date = $(this).data('date');
      $('#deleteDate').text(date);

      const url = @json(route('admin.tourbooking.services.availability.destroy', ['service'=>$service->id,'availability'=>':id']));
      $('#deleteAvailabilityForm').attr('action', url.replace(':id', id));
      $('#deleteAvailabilityModal').modal('show');
    });

    // ====== Edit flow (prefill) ======
    $('.edit-availability').on('click', function(){
      const id      = $(this).data('id');
      const date    = $(this).data('date');
      const st      = $(this).data('start-time');
      const et      = $(this).data('end-time');
      const ia      = $(this).data('is-available') == '1';
      const spots   = $(this).data('available-spots') ?? '';
      const notes   = $(this).data('notes') ?? '';
      const agecats = $(this).data('age-categories') || {};

      $('#edit_date').val(date);
      $('#edit_start_time').val(st);
      $('#edit_end_time').val(et);
      $('#edit_available_spots').val(spots);
      $('#edit_is_available').prop('checked', ia);
      $('#edit_notes').val(notes);

      // Reset all edit age-cat to defaults (unchecked + inputs empty/disabled)
      $('#ageCatsEdit .agecat-card').each(function(){
        const $card = $(this);
        const chk = $card.find('.toggle-agecat');
        const target = $(chk.data('target'));
        chk.prop('checked', false);
        ensureHiddenZero(chk);
        target.find('input').val('').prop('disabled', true);
        $card.addClass('disabled');
      });

      // Prefill from payload
      try{
        Object.keys(agecats || {}).forEach(function(k){
          const data = agecats[k] || {};
          const chk = $('#edit_agecat_'+k+'_enabled');
          if(!chk.length) return;

          const targetSel = chk.data('target');
          const $card = chk.closest('.agecat-card');
          const $fields = $(targetSel);

          const enabled = !!(data.enabled || data.enabled === 1 || data.enabled === '1');
          chk.prop('checked', enabled);
          ensureHiddenZero(chk);
          $card.toggleClass('disabled', !enabled);
          $fields.find('input').prop('disabled', !enabled);

          if(data.count !== undefined)   $fields.find(`[name="age_categories[${k}][count]"]`).val(data.count);
          if(data.price !== undefined)   $fields.find(`[name="age_categories[${k}][price]"]`).val(data.price);
          if(data.min_age !== undefined) $fields.find(`[name="age_categories[${k}][min_age]"]`).val(data.min_age);
          if(data.max_age !== undefined) $fields.find(`[name="age_categories[${k}][max_age]"]`).val(data.max_age);
        });
      }catch(e){}

      const url = @json(route('admin.tourbooking.services.availability.update', ['service'=>$service->id,'availability'=>':id']));
      $('#editAvailabilityForm').attr('action', url.replace(':id', id));
    });

    // ====== Age Categories helpers ======
    function ensureHiddenZero($chk){
      const name = $chk.attr('name');
      const esc  = name.replace(/([\[\]])/g,'\\$1');
      if(!$chk.prev(`input[type=hidden][name="${esc}"]`).length){
        $('<input>',{type:'hidden',name:name,value:0}).insertBefore($chk);
      }
    }

    function setAgecatState($chk){
      const enabled  = $chk.is(':checked');
      const $card    = $chk.closest('.agecat-card');
      const target   = $chk.data('target');
      const $fields  = $(target);
      $card.toggleClass('disabled', !enabled);
      $fields.find('input').prop('disabled', !enabled);
    }

    // Initialize Add/Edit/Bulk scopes
    ['#ageCatsAdd','#ageCatsEdit','#ageCatsBulk'].forEach(function(scopeSel){
      const $scope = $(scopeSel);
      if(!$scope.length) return;

      $scope.find('.toggle-agecat').each(function(){
        const $chk = $(this);
        ensureHiddenZero($chk);
        setAgecatState($chk);
      });

      $scope.on('change','.toggle-agecat', function(){
        setAgecatState($(this));
      });
    });

  });
})(jQuery);
</script>
@endpush
