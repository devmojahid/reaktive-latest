@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Service Pickup Points') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Service Pickup Points Management') }}</h3>
    <p class="crancy-header__text">
        {{ __('translate.Manage Pickup Points') }} >> {{ $service->title }}
    </p>
@endsection

@push('style_section')
<link rel="stylesheet" href="{{ asset('global/select2/select2.min.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  /* === Card framework (ca la edit/create) === */
  .section-card + .section-card{ margin-top:24px; }
  .crancy-card__head{ padding:18px 20px; border-bottom:1px solid var(--border); }
  .crancy-card__title{ margin:0; font-weight:700; font-size:var(--fs-xl); color:var(--g-900); }
  .crancy-card__body{ padding:20px; }

  .create_new_btn_inline_box{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
  }

  /* Currency adornment */
  .crancy__item-form--currency{ position:relative; display:flex; align-items:center; }
  .crancy__item-form--currency .crancy__item-input{ padding-right:44px; }
  .crancy__currency-icon{
    position:absolute; right:12px; top:50%; transform:translateY(-50%);
    pointer-events:none; font-weight:700; color:#111827; opacity:.8;
  }

  /* Map Styles */
  #map-container { height: 400px; width: 100%; margin: 20px 0; border-radius: 8px; overflow: hidden; }
  .pickup-point-popup { max-width: 250px; }
  .pickup-point-popup h6 { margin: 0 0 8px 0; color: #333; }
  .pickup-point-popup p { margin: 0 0 4px 0; font-size: 13px; }
  .pickup-point-popup .charge { font-weight: bold; color: #e74c3c; }
  .pickup-point-popup .free { font-weight: bold; color: #27ae60; }

  .pickup-flex{ display:flex; align-items:center; gap:6px; }
  .status-badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500; }
  .status-active { background: #d4edda; color: #155724; }
  .status-inactive { background: #f8d7da; color: #721c24; }
  .default-badge { background: #cce5ff; color: #004085; padding: 2px 6px; border-radius: 8px; font-size: 11px; }
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
                  <h4 class="crancy-card__title">{{ __('translate.Service Pickup Points') }}</h4>
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
                <div id="map-container"></div>
              </div>
            </div>

            {{-- ===== Existing Pickup Points Table ===== --}}
            <div class="crancy-card section-card">
              <div class="crancy-card__head">
                <h4 class="crancy-card__title">{{ __('translate.Configured Pickup Points') }}</h4>
              </div>
              <div class="crancy-card__body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>{{ __('translate.Name') }}</th>
                        <th>{{ __('translate.Address') }}</th>
                        <th>{{ __('translate.Coordinates') }}</th>
                        <th>{{ __('translate.Extra Charge') }}</th>
                        <th>{{ __('translate.Status') }}</th>
                        <th>{{ __('translate.Action') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($service->pickupPoints as $pickup)
                        <tr>
                          <td>
                            {{ $pickup->name }}
                            @if($pickup->is_default)
                              <span class="default-badge">{{ __('translate.Default') }}</span>
                            @endif
                          </td>
                          <td>{{ Str::limit($pickup->address, 50) }}</td>
                          <td>{{ $pickup->latitude }}, {{ $pickup->longitude }}</td>
                          <td>{{ $pickup->formatted_extra_charge }}</td>
                          <td>
                            @if ($pickup->status)
                              <span class="status-badge status-active">{{ __('translate.Active') }}</span>
                            @else
                              <span class="status-badge status-inactive">{{ __('translate.Inactive') }}</span>
                            @endif
                          </td>
                          <td class="pickup-flex">
                            <button type="button"
                              class="btn btn-sm btn-primary edit-pickup"
                              data-id="{{ $pickup->id }}"
                              data-name="{{ $pickup->name }}"
                              data-description="{{ $pickup->description }}"
                              data-address="{{ $pickup->address }}"
                              data-latitude="{{ $pickup->latitude }}"
                              data-longitude="{{ $pickup->longitude }}"
                              data-extra-charge="{{ $pickup->extra_charge }}"
                              data-charge-type="{{ $pickup->charge_type }}"
                              data-is-default="{{ $pickup->is_default ? '1' : '0' }}"
                              data-status="{{ $pickup->status ? '1' : '0' }}"
                              data-notes="{{ $pickup->notes }}"
                              data-bs-toggle="modal"
                              data-bs-target="#editPickupModal">
                              <i class="fa fa-edit"></i>
                            </button>
                            <button type="button"
                              class="btn btn-sm btn-danger delete-pickup"
                              data-id="{{ $pickup->id }}"
                              data-name="{{ $pickup->name }}">
                              <i class="fa fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="6" class="text-center text-muted">{{ __('translate.No pickup points configured') }}</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            {{-- ===== Add Pickup Point ===== --}}
            <div class="crancy-card section-card">
              <div class="crancy-card__head">
                <h4 class="crancy-card__title">{{ __('translate.Add Pickup Point') }}</h4>
              </div>
              <div class="crancy-card__body">
                <form action="{{ route('admin.tourbooking.services.pickup-points.store', $service) }}" method="POST">
                  @csrf
                  <div class="row">
                    <div class="col-md-6">
                      <label class="crancy__item-label">{{ __('translate.Name') }} *</label>
                      <input type="text" name="name" class="crancy__item-input" required>
                    </div>
                    <div class="col-md-6">
                      <label class="crancy__item-label">{{ __('translate.Address') }} *</label>
                      <input type="text" name="address" class="crancy__item-input" required>
                    </div>
                  </div>

                  <div class="row mg-top-20">
                    <div class="col-md-6">
                      <label class="crancy__item-label">{{ __('translate.Latitude') }} *</label>
                      <input type="number" step="any" name="latitude" id="add_latitude" class="crancy__item-input" required>
                    </div>
                    <div class="col-md-6">
                      <label class="crancy__item-label">{{ __('translate.Longitude') }} *</label>
                      <input type="number" step="any" name="longitude" id="add_longitude" class="crancy__item-input" required>
                    </div>
                  </div>

                  <div class="row mg-top-20">
                    <div class="col-md-6">
                      <label class="crancy__item-label">{{ __('translate.Extra Charge') }}</label>
                      <div class="crancy__item-form--currency">
                        <input type="number" step="0.01" min="0" name="extra_charge" class="crancy__item-input" placeholder="0.00">
                        <div class="crancy__currency-icon"><span>{{ config('settings.currency_icon', '$') }}</span></div>
                      </div>
                    </div>
                    <div class="col-md-6 d-none">
                      <label class="crancy__item-label">{{ __('translate.Charge Type') }}</label>
                      <select name="charge_type" class="crancy__item-input">
                        <option value="flat">{{ __('translate.Flat Rate') }}</option>
                        <option value="per_person">{{ __('translate.Per Person') }}</option>
                        <option value="per_adult">{{ __('translate.Per Adult') }}</option>
                        <option value="per_child">{{ __('translate.Per Child') }}</option>
                      </select>
                    </div>
                  </div>

                  <div class="row mg-top-20">
                    <div class="col-md-6">
                      <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                        <label class="crancy__item-switch">
                          <input type="checkbox" name="is_default" value="1">
                          <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                        </label>
                        <span class="crancy__item-label">{{ __('translate.Default Pickup Point') }}</span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                        <label class="crancy__item-switch">
                          <input type="checkbox" name="status" value="1" checked>
                          <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                        </label>
                        <span class="crancy__item-label">{{ __('translate.Status') }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="row mg-top-20">
                    <div class="col-12">
                      <label class="crancy__item-label">{{ __('translate.Description') }}</label>
                      <textarea name="description" class="crancy__item-input" rows="3"></textarea>
                    </div>
                  </div>

                  <div class="row mg-top-20">
                    <div class="col-12">
                      <label class="crancy__item-label">{{ __('translate.Notes') }}</label>
                      <textarea name="notes" class="crancy__item-input" rows="2"></textarea>
                    </div>
                  </div>

                  <div class="row mg-top-20">
                    <div class="col-12">
                      <button type="submit" class="crancy-btn">{{ __('translate.Add Pickup Point') }}</button>
                      <button type="button" id="use-current-location" class="crancy-btn crancy-btn-outline">
                        <i class="fa fa-location-arrow"></i> {{ __('translate.Use Current Location') }}
                      </button>
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

{{-- ===== Edit Pickup Point Modal ===== --}}
<div class="modal fade" id="editPickupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('translate.Edit Pickup Point') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editPickupForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <label class="crancy__item-label">{{ __('translate.Name') }} *</label>
              <input type="text" name="name" id="edit_name" class="crancy__item-input" required>
            </div>
            <div class="col-md-6">
              <label class="crancy__item-label">{{ __('translate.Address') }} *</label>
              <input type="text" name="address" id="edit_address" class="crancy__item-input" required>
            </div>
          </div>

          <div class="row mg-top-20">
            <div class="col-md-6">
              <label class="crancy__item-label">{{ __('translate.Latitude') }} *</label>
              <input type="number" step="any" name="latitude" id="edit_latitude" class="crancy__item-input" required>
            </div>
            <div class="col-md-6">
              <label class="crancy__item-label">{{ __('translate.Longitude') }} *</label>
              <input type="number" step="any" name="longitude" id="edit_longitude" class="crancy__item-input" required>
            </div>
          </div>

          <div class="row mg-top-20">
            <div class="col-md-6">
              <label class="crancy__item-label">{{ __('translate.Extra Charge') }}</label>
              <div class="crancy__item-form--currency">
                <input type="number" step="0.01" min="0" name="extra_charge" id="edit_extra_charge" class="crancy__item-input" placeholder="0.00">
                <div class="crancy__currency-icon"><span>{{ config('settings.currency_icon', '$') }}</span></div>
              </div>
            </div>
            <div class="col-md-6 d-none">
              <label class="crancy__item-label">{{ __('translate.Charge Type') }}</label>
              <select name="charge_type" id="edit_charge_type" class="crancy__item-input">
                <option value="flat">{{ __('translate.Flat Rate') }}</option>
                <option value="per_person">{{ __('translate.Per Person') }}</option>
                <option value="per_adult">{{ __('translate.Per Adult') }}</option>
                <option value="per_child">{{ __('translate.Per Child') }}</option>
              </select>
            </div>
          </div>

          <div class="row mg-top-20">
            <div class="col-md-6">
              <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                <label class="crancy__item-switch">
                  <input type="checkbox" name="is_default" id="edit_is_default" value="1">
                  <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                </label>
                <span class="crancy__item-label">{{ __('translate.Default Pickup Point') }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                <label class="crancy__item-switch">
                  <input type="checkbox" name="status" id="edit_status" value="1">
                  <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                </label>
                <span class="crancy__item-label">{{ __('translate.Status') }}</span>
              </div>
            </div>
          </div>

          <div class="row mg-top-20">
            <div class="col-12">
              <label class="crancy__item-label">{{ __('translate.Description') }}</label>
              <textarea name="description" id="edit_description" class="crancy__item-input" rows="3"></textarea>
            </div>
          </div>

          <div class="row mg-top-20">
            <div class="col-12">
              <label class="crancy__item-label">{{ __('translate.Notes') }}</label>
              <textarea name="notes" id="edit_notes" class="crancy__item-input" rows="2"></textarea>
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

{{-- ===== Delete Confirmation Modal ===== --}}
<div class="modal fade" id="deletePickupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('translate.Delete Pickup Point') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>{{ __('translate.Are you sure you want to delete pickup point') }} <span id="deleteName"></span>?</p>
      </div>
      <div class="modal-footer">
        <form id="deletePickupForm" method="POST">
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function($){
  "use strict";

  $(function(){
    // Initialize map
    const map = L.map('map-container').setView([{{ $service->latitude ?? '40.7128' }}, {{ $service->longitude ?? '-74.0060' }}], 10);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add existing pickup points to map
    const pickupPoints = @json($service->pickupPoints->toArray());
    pickupPoints.forEach(function(pickup) {
      const icon = L.divIcon({
        className: 'custom-pickup-marker',
        html: pickup.status ? 
          '<i class="fa fa-map-marker" style="color: #e74c3c; font-size: 24px;"></i>' :
          '<i class="fa fa-map-marker" style="color: #95a5a6; font-size: 24px;"></i>',
        iconSize: [25, 25],
        iconAnchor: [12, 24]
      });

      const marker = L.marker([pickup.latitude, pickup.longitude], {icon: icon}).addTo(map);

      const extraCharge = pickup.extra_charge > 0 ? 
        `<p class="charge">Price: ${pickup.extra_charge}</p>` :
        `<p class="free">{{ __('translate.Free') }}</p>`;

      marker.bindPopup(`
        <div class="pickup-point-popup">
          <h6>${pickup.name} ${pickup.is_default ? '<span class="default-badge">{{ __('translate.Default') }}</span>' : ''}</h6>
          <p><i class="fa fa-map-marker"></i> ${pickup.address}</p>
          ${extraCharge}
          ${pickup.description ? `<p><i class="fa fa-info-circle"></i> ${pickup.description}</p>` : ''}
        </div>
      `);
    });

    // Map click to set coordinates
    map.on('click', function(e) {
      $('#add_latitude').val(e.latlng.lat.toFixed(8));
      $('#add_longitude').val(e.latlng.lng.toFixed(8));
    });

    // Use current location
    $('#use-current-location').on('click', function() {
      if (navigator.geolocation) {
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __('translate.Getting Location') }}...');
        
        navigator.geolocation.getCurrentPosition(function(position) {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          
          $('#add_latitude').val(lat.toFixed(8));
          $('#add_longitude').val(lng.toFixed(8));
          
          map.setView([lat, lng], 15);
          
          $('#use-current-location').prop('disabled', false).html('<i class="fa fa-location-arrow"></i> {{ __('translate.Use Current Location') }}');
        }, function() {
          alert('{{ __('translate.Unable to get your location') }}');
          $('#use-current-location').prop('disabled', false).html('<i class="fa fa-location-arrow"></i> {{ __('translate.Use Current Location') }}');
        });
      } else {
        alert('{{ __('translate.Geolocation is not supported by this browser') }}');
      }
    });

    // Edit pickup point
    $('.edit-pickup').on('click', function() {
      const id = $(this).data('id');
      const name = $(this).data('name');
      const description = $(this).data('description');
      const address = $(this).data('address');
      const latitude = $(this).data('latitude');
      const longitude = $(this).data('longitude');
      const extraCharge = $(this).data('extra-charge');
      const chargeType = $(this).data('charge-type');
      const isDefault = $(this).data('is-default') == '1';
      const status = $(this).data('status') == '1';
      const notes = $(this).data('notes');

      $('#edit_name').val(name);
      $('#edit_description').val(description);
      $('#edit_address').val(address);
      $('#edit_latitude').val(latitude);
      $('#edit_longitude').val(longitude);
      $('#edit_extra_charge').val(extraCharge);
      $('#edit_charge_type').val(chargeType);
      $('#edit_is_default').prop('checked', isDefault);
      $('#edit_status').prop('checked', status);
      $('#edit_notes').val(notes);

      const url = @json(route('admin.tourbooking.services.pickup-points.update', ['service'=>$service->id,'pickupPoint'=>':id']));
      $('#editPickupForm').attr('action', url.replace(':id', id));
    });

    // Delete pickup point
    $('.delete-pickup').on('click', function() {
      const id = $(this).data('id');
      const name = $(this).data('name');
      $('#deleteName').text(name);

      const url = @json(route('admin.tourbooking.services.pickup-points.destroy', ['service'=>$service->id,'pickupPoint'=>':id']));
      $('#deletePickupForm').attr('action', url.replace(':id', id));
      $('#deletePickupModal').modal('show');
    });

  });
})(jQuery);
</script>
@endpush
