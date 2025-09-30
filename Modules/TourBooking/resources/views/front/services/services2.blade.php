@php
    $enum_languages = App\Enums\Language::cases();
@endphp

@extends('layout_inner_page')

@section('title')
    <title>Services</title>
    <meta name="title" content="Services">
    <meta name="description" content="Services">
@endsection

@section('front-content')
    @include('breadcrumb')

    <div x-data="data">
@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

// 1) Prefer $serviceTypes (de obicei conține TOATE tipurile), altfel cade pe serviceTypeTab()
$serviceTypesForPills = (isset($serviceTypes) && count($serviceTypes))
    ? ($serviceTypes instanceof Collection ? $serviceTypes : collect($serviceTypes))
    : serviceTypeTab();

// 1b) Fallback pentru icon/image dacă lipsesc pe obiectele venite din $serviceTypes
if ($serviceTypesForPills->count()) {
    $ids = $serviceTypesForPills->pluck('id')->all();
    $iconsById  = DB::table('service_types')->whereIn('id', $ids)->pluck('icon', 'id')->toArray();
    $imagesById = DB::table('service_types')->whereIn('id', $ids)->pluck('image','id')->toArray();

    // atașăm icon/image dacă sunt null
    $serviceTypesForPills = $serviceTypesForPills->map(function($t) use ($iconsById, $imagesById) {
        if (empty($t->icon)  && isset($iconsById[$t->id]))  { $t->icon  = $iconsById[$t->id]; }
        if (empty($t->image) && isset($imagesById[$t->id])) { $t->image = $imagesById[$t->id]; }
        return $t;
    });
}

// 2) Count excursii pe tip
$typeCountsForPills = [];
if ($serviceTypesForPills->count()) {
    $ids = $serviceTypesForPills->pluck('id')->all();
    $typeCountsForPills = DB::table('services')
        ->select('service_type_id', DB::raw('COUNT(*) as c'))
        ->whereIn('service_type_id', $ids)
        ->where('status', 1)
        ->groupBy('service_type_id')
        ->pluck('c', 'service_type_id')
        ->toArray();
}

// 3) Sort descrescător după count (nu elimină nimic)
$serviceTypesForPills = $serviceTypesForPills
    ->sortByDesc(fn($t) => (int)($typeCountsForPills[$t->id] ?? 0))
    ->values();
@endphp


    <!-- tg-booking-form-area-start -->
    <div id="hb-search" class="tg-booking-form-area tg-booking-form-grid-space pb-50">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">

            {{-- SEARCH BAR --}}
            <div class="hb-shell" x-data="hbHeroSearch()">
              <div class="hb-grid">
                {{-- Where --}}
                <div class="hb-field">
                  <span class="hb-ico">
                    <svg width="18" height="18" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <path d="M12.33 6.707C12.33 11.232 6.555 15.111 6.555 15.111S.777 11.232.777 6.707a5.777 5.777 0 1111.553 0z" stroke="currentColor" stroke-width="1.2"/>
                      <circle cx="6.555" cy="6.707" r="2" stroke="currentColor" stroke-width="1.2"/>
                    </svg>
                  </span>
                  <div class="flex-1">
                    <div class="hb-ph" x-text="destination || 'Where are you going . . .'"></div>
                  </div>
                  <ul class="hb-dd" x-ref="destList">
                    @foreach (($destinations ?? []) as $d)
                      <li @click="selectDestination('{{ $d->id }}','{{ addslashes($d->name) }}')">
                        <i class="fa-regular fa-location-dot"></i><span>{{ $d->name }}</span>
                      </li>
                    @endforeach
                  </ul>
                </div>

                {{-- When (single date) --}}
                <div class="hb-field">
                  <span class="hb-ico"><i class="fa-regular fa-calendar"></i></span>
                  <div class="flex-1">
                    <input id="hb-date" class="hb-input" x-model="check_in_check_out" type="text" placeholder="When?" autocomplete="off">
                  </div>
                </div>

                {{-- Languages --}}
                <div class="hb-field">
                  <span class="hb-ico"><i class="fa-regular fa-globe"></i></span>
                  <div class="flex-1">
                    <div class="hb-ph" x-text="language || 'All Languages'"></div>
                  </div>
                  <ul class="hb-dd" x-ref="langList">
                    <li @click="selectLanguage('')"><span>All Languages</span></li>
                    @foreach ($enum_languages as $language)
                      <li @click="selectLanguage('{{ addslashes($language->name) }}')">
                        <span>{{ $language->name }}</span>
                      </li>
                    @endforeach
                  </ul>
                </div>

                {{-- Search --}}
                <div><button class="hb-btn" @click="submitForm()">Search</button></div>
              </div>
            </div>

                {{-- SERVICE TYPE PILLS (scroll + multi-select + sort desc by count) --}}
            @if($serviceTypesForPills->count())
              <div class="hb-pills mt-3" id="hbPills">
                <button class="hb-nav left" type="button" aria-label="Prev" data-hb-prev>
                  <i class="fa-solid fa-angle-left"></i>
                </button>

                <div class="hb-viewport" data-hb-viewport>
                  <div class="hb-track" data-hb-track>
                    @foreach ($serviceTypesForPills as $type)
                      @php
                        $idStr = (string) $type->id;
                        $count = (int)($typeCountsForPills[$idStr] ?? 0);

                        // 1) icon brut din map-ul DB sau fallback la proprietatea modelului
                        $rawIcon = (string)($iconsById[$type->id] ?? ($type->icon ?? ''));

                        // 2) curățăm dacă în DB s-a salvat întregul <i class="..."></i>
                        $ico = strip_tags(trim($rawIcon));
                        $ico = preg_replace('/\s+/', ' ', $ico);

                        if ($ico !== '') {
                            // FA5 -> FA6
                            $ico = preg_replace('/\bfas\b/i', 'fa-solid',  $ico);
                            $ico = preg_replace('/\bfar\b/i', 'fa-regular', $ico);
                            $ico = preg_replace('/\bfab\b/i', 'fa-brands',  $ico);
                            $ico = preg_replace('/\bfal\b/i', 'fa-light',   $ico);

                            // FA4: "fa fa-..." -> "fa-solid fa-..."
                            $ico = preg_replace('/(^|\s)fa\s+(?=fa-[a-z0-9-]+)/i', '$1fa-solid ', $ico);

                            // dacă lipsește un stil FA6, adăugăm fa-solid
                            if (!preg_match('/\bfa-(solid|regular|brands|light|thin|duotone|sharp)\b/i', $ico)) {
                                $ico = 'fa-solid ' . $ico;
                            }
                        }
                      @endphp
                      <button
                        type="button"
                        class="hb-pill"
                        :class="{'active': (filters.service_type_ids || []).map(String).includes('{{ $idStr }}')}"
                        :aria-pressed="(filters.service_type_ids || []).map(String).includes('{{ $idStr }}')"
                        @click.stop="togglePill('{{ $idStr }}')"
                        title="{{ $type->name }}"
                      >
@if(!empty($type->image))
  <img src="{{ asset($type->image) }}" alt="{{ $type->name }}">
@elseif(!empty($ico))
  <i class="{{ $ico }}"></i>
@else
  <i class="fa-solid fa-tag"></i>
@endif
                        <div>
                          <b>{{ $type->name }}</b><br>
                          <small>Excursions: {{ $count }}</small>
                        </div>
                      </button>
                    @endforeach
                  </div>
                </div>

                <button class="hb-nav right" type="button" aria-label="Next" data-hb-next>
                  <i class="fa-solid fa-angle-right"></i>
                </button>
              </div>
            @endif

          </div>
        </div>
      </div>
    </div>
    <!-- tg-booking-form-area-end -->

    <!-- tg-listing-grid-area-start -->
    <div class="tg-listing-grid-area mb-85">
      <div class="container">
        <div class="row">
          <div class="col-xl-3 col-lg-4 order-last order-lg-first">
            <div class="tg-filter-sidebar mb-40 top-sticky">
              <div class="tg-filter-item">

                <div>
                  <div class="d-flex justify-content-between align-items-center mb-10">
                    <h4 class="tg-filter-title mb-0">Search</h4>
                    <a class="tg-filter-reset" x-show="isFilterChanged || isBookingFilterChanged"
                       @click="resetFilters()" href="javascript:void(0);">Reset All</a>
                  </div>
                  <div class="tg-filter-search-form">
                    <div class="p-relative">
                      <input class="input" x-model.debounce="filters.search" type="text" placeholder="Search here...">
                      <button class="buttons" type="submit">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <g clip-path="url(#clip0_397_1228)">
                            <path d="M13.2218 13.2222L10.5188 10.5192M12.1959 6.48705C12.1959 9.6402 9.63977 12.1963 6.48662 12.1963C3.33348 12.1963 0.777344 9.6402 0.777344 6.48705C0.777344 3.3339 3.33348 0.777771 6.48662 0.777771C9.63977 0.777771 12.1959 3.3339 12.1959 6.48705Z" stroke="#353844" stroke-width="1.575" stroke-linecap="round" stroke-linejoin="round"/>
                          </g>
                          <defs><clipPath id="clip0_397_1228"><rect width="14" height="14" fill="white"/></clipPath></defs>
                        </svg>
                      </button>
                    </div>
                  </div>
                  <span class="tg-filter-border mt-30 mb-25"></span>
                </div>

                <div x-data="{ showPropertyType: false }">
                  <h4 class="tg-filter-title mb-15">Property Type</h4>
                  <div class="tg-filter-list">
                    <ul>
                      @foreach ($serviceTypes as $key => $serviceType)
                        <li x-show="showPropertyType || {{ $key }} < 4" x-transition>
                          <div class="checkbox d-flex">
                            <input value="{{ $serviceType?->id }}" x-model="filters.service_type_ids" class="tg-checkbox" type="checkbox" id="australia_{{ $key }}">
                            <label for="australia_{{ $key }}" class="tg-label">{{ $serviceType?->name }}</label>
                          </div>
                        </li>
                      @endforeach
                    </ul>
                  </div>

                  @if (count($serviceTypes) > 4)
                    <div class="tg-filter-seemore mt-2 cp select-none" @click="showPropertyType = !showPropertyType">
                      <span class="plus"><i :class="showPropertyType ? 'fa-solid fa-minus' : 'fa-sharp fa-solid fa-plus'"></i></span>
                      <span class="more" x-text="showPropertyType ? 'See Less' : 'See More'"></span>
                    </div>
                  @endif

                  <span class="tg-filter-border mt-25 mb-25"></span>
                </div>

                <div class="tg-filter-price-input">
                  <h4 class="tg-filter-title mb-20">Price By Filter</h4>
                  <div class="d-flex align-items-center">
                    <input class="input no-arrow" x-model="filters.min_price" type="number" placeholder="Min Price">
                    <span class="dvdr">
                      <svg width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 2H12" stroke="#353844" stroke-width="3" stroke-linecap="round" />
                      </svg>
                    </span>
                    <input class="input no-arrow" x-model="filters.max_price" type="number" placeholder="Max Price">
                  </div>
                </div>
                <span class="tg-filter-border mt-25 mb-25"></span>

                <div x-data="{ showAmenity: false }">
                  <h4 class="tg-filter-title mb-15">Amenities</h4>
                  <div class="tg-filter-list">
                    <ul>
                      @foreach ($amenities as $key => $amenity)
                        <li x-show="showAmenity || {{ $key }} < 4" x-transition>
                          <div class="checkbox d-flex">
                            <input value="{{ $amenity?->translation?->id }}" x-model="filters.amenity_ids" class="tg-checkbox" type="checkbox" id="amenity_{{ $key }}">
                            <label for="amenity_{{ $key }}" class="tg-label">{{ $amenity?->translation?->name }}</label>
                          </div>
                        </li>
                      @endforeach
                    </ul>
                  </div>

                  @if (count($amenities) > 4)
                    <div class="tg-filter-seemore mt-2 cp select-none" @click="showAmenity = !showAmenity">
                      <span class="plus"><i :class="showAmenity ? 'fa-solid fa-minus' : 'fa-sharp fa-solid fa-plus'"></i></span>
                      <span class="more" x-text="showAmenity ? 'See Less' : 'See More'"></span>
                    </div>
                  @endif

                  <span class="tg-filter-border mt-25 mb-25"></span>
                </div>

                <h4 class="tg-filter-title mb-15">Top Reviews</h4>
                <div class="tg-filter-list">
                  <ul>
                    @for ($i = 5; $i >= 1; $i--)
                      <li>
                        <div class="checkbox d-flex">
                          <input id="rating_{{ $i }}" x-model="filters.ratings" class="tg-checkbox" type="checkbox" value="{{ $i }}" name="filter_ratings[]">
                          <div class="tg-filter-review">
                            <label for="rating_{{ $i }}">
                              @for ($j = 1; $j <= 5; $j++)
                                @if ($j <= $i)
                                  <span><i class="fa-solid fa-star-sharp"></i></span>
                                @else
                                  <span class="bad-review"><i class="fa-light fa-star-sharp"></i></span>
                                @endif
                              @endfor
                            </label>
                          </div>
                        </div>
                      </li>
                    @endfor
                  </ul>
                </div>
                <span class="tg-filter-border mt-25 mb-25"></span>

                <div x-data="{ showMoreLanguages: false }">
                  <h4 class="tg-filter-title mb-15">Language</h4>
                  <div class="tg-filter-list">
                    <ul>
                      @foreach ($languages as $key => $language)
                        <li x-show="showMoreLanguages || {{ $key }} < 4" x-transition>
                          <div class="checkbox d-flex">
                            <input value="{{ $language?->name }}" x-model="filters.languages" class="tg-checkbox" type="checkbox" id="language_{{ $key }}">
                            <label for="language_{{ $key }}" class="tg-label">{{ $language?->value }}</label>
                          </div>
                        </li>
                      @endforeach
                    </ul>
                  </div>

                  @if (count($languages) > 4)
                    <div class="tg-filter-seemore mt-2 cp select-none" @click="showMoreLanguages = !showMoreLanguages">
                      <span class="plus"><i :class="showMoreLanguages ? 'fa-solid fa-minus' : 'fa-sharp fa-solid fa-plus'"></i></span>
                      <span class="more" x-text="showMoreLanguages ? 'See Less' : 'See More'"></span>
                    </div>
                  @endif
                </div>

              </div>
            </div>
          </div>
          <div class="col-xl-9 col-lg-8">
            <div class="tg-listing-item-box-wrap ml-10">
              <div class="tg-listing-box-filter mb-15">
                <div class="row align-items-center">
                  <div class="col-lg-5 col-md-5 mb-15">
                    <div class="tg-listing-box-number-found">
                      <span class="custom_pagination_count"></span>
                    </div>
                  </div>
                  <div class="col-lg-7 col-md-7 mb-15">
                    <div class="tg-listing-box-view-type d-flex justify-content-end align-items-center">
                      <div class="tg-listing-sort">
                        <span>Sort by:</span>
                        <a href="#">
                          <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.469 3.279a.75.75 0 0 0 1.06 0l.72-.72V12.75a.75.75 0 1 0 1.5 0V2.56l.72.72a.75.75 0 1 0 1.06-1.06l-2-2a.75.75 0 0 0-1.06 0l-2 2a.75.75 0 0 0 0 1.06ZM3.749 12.939l.72-.72a.75.75 0 1 1 1.06 1.06l-2 2a.75.75 0 0 1-1.06 0l-2-2A.75.75 0 1 1 1.53 12.22l.72.72V2.75a.75.75 0 0 1 1.5 0v10.189Z" fill="currentColor"/>
                          </svg>
                        </a>
                      </div>
                      <div class="tg-listing-select-price ml-10">
                        <select id="sortSelect" class="select" name="sort_by">
                          <option value="default">Default</option>
                          <option value="latest">Latest</option>
                          <option value="oldest">Oldest</option>
                          <option value="price_low">Price Low</option>
                          <option value="price_high">Price High</option>
                          <option value="trending">Trending</option>
                          <option value="popular">Popular</option>
                          <option value="location_asc">Location A-Z</option>
                          <option value="location_desc">Location Z-A</option>
                        </select>
                      </div>
                      <div class="d-none d-sm-block">
                        <div class="tg-listing-box-view ml-10 d-flex">
                          <div class="list-switch-item">
                            <button @click="isListView = false" class="grid-view active">
                              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 1H1V8H8V1Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19 1H12V8H19V1Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19 12H12V19H19V12Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 12H1V19H8V12Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
                            </button>
                          </div>
                          <div class="list-switch-item ml-5">
                            <button @click="isListView = true" class="list-view">
                              <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 1H19M6 7H19M6 13H19M1 1H1.01M1 7H1.01M1 13H1.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div id="filter_data"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- tg-listing-grid-area-end -->
  </div>
@endsection

@push('js_section')
  <script src="{{ asset('global/datetimerange/moment.min.js') }}"></script>
  <script src="{{ asset('global/datetimerange/daterangepicker.js') }}"></script>

  <script>
    (function($){ "use strict";
      $(document).ready(function(){
        $(".timepicker").flatpickr({ enableTime:true, noCalendar:true, dateFormat:"H:i", time_24hr:true });
      });
    })(jQuery);
  </script>

  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('data', () => ({
        page: 1,
        isListView: false,
        style: 'style1',

        // Booking form
        defaultBookingForm: {
          destination_id:'', destination:'Where are you going . . .',
          checkIn:'', checkOut:'', check_in_check_out:'',
          rooms:0, adults:0, children:0,
          language:'All Languages', language_id:'All Languages',
        },
        bookingForm: {
          destination_id:`{{ request('destination_id', '') }}`,
          destination:`{{ request('destination', '') }}`,
          checkIn:`{{ request('checkIn', '') }}`,
          checkOut:`{{ request('checkOut', '') }}`,
          check_in_check_out:`{{ request('check_in_check_out', '') }}`,
          rooms:`{{ request('rooms', '') }}`,
          adults:`{{ request('adults', '') }}`,
          children:`{{ request('children', '') }}`,
          language:`{{ request('language', 'All Languages') }}`,
          language_id:`{{ request('language_id', 'All Languages') }}`,
        },

        // Filters
        filters: {
          search:`{{ request('search', '') }}`,
          service_type_ids:{!! json_encode(array_values((array)request('service_type_ids', request()->filled('service_type_id') ? [request('service_type_id')] : []))) !!},
          max_price:`{{ request('max_price', '') }}`,
          min_price:`{{ request('min_price', '') }}`,
          amenity_ids:{!! json_encode(request('amenity_ids', [])) !!},
          languages:{!! json_encode(request('languages', [])) !!},
          sort_by:`{{ request('sort_by', '') }}`,
          ratings:{!! json_encode(request('ratings', [])) !!},
          trip_id:`{{ request('trip_id', '') }}`
        },
        defaultFilters: { search:'', service_type_ids:[], max_price:'', min_price:'', amenity_ids:[], languages:[], sort_by:'', ratings:[], trip_id:'' },

        // State helpers
        get isFilterChanged(){ return JSON.stringify(this.filters) !== JSON.stringify(this.defaultFilters); },
        get isBookingFilterChanged(){ return JSON.stringify(this.bookingForm) !== JSON.stringify(this.defaultBookingForm); },

        // Pills: doar modificăm array-ul; fetch vine din watcher-ul pe filters
        togglePill(id){
          const sid = String(id);
          const arr = (this.filters.service_type_ids || []).map(String);
          const i = arr.indexOf(sid);
          if (i > -1) this.filters.service_type_ids.splice(i, 1);
          else this.filters.service_type_ids.push(sid);
        },

        updateURL(value){
          const base = location.protocol + '//' + location.host + location.pathname;
          const qs = [];
          for (const [k,v] of Object.entries(value)){
            if (Array.isArray(v) && v.length) v.forEach(it=> qs.push(`${encodeURIComponent(k)}%5B%5D=${encodeURIComponent(it)}`));
            else if (v!==null && v!==undefined && v!=='') qs.push(`${encodeURIComponent(k)}=${encodeURIComponent(v)}`);
          }
          history.pushState({path: base + (qs.length?`?${qs.join('&')}`:'')}, '', base + (qs.length?`?${qs.join('&')}`:''));
        },

        resetFilters(){
          this.filters = JSON.parse(JSON.stringify(this.defaultFilters));
          this.bookingForm = { destination_id:'', destination:'', checkIn:'', checkOut:'', check_in_check_out:'', rooms:'', adults:'', children:'' };
          $('#check_in_check_out').val('').attr('placeholder','Select date range');
          window.selectedDates = 'Select date range';
          this.$nextTick(()=>{ $('#sortSelect').val('default').niceSelect('update'); this.syncDateDisplay(); });
        },

        // ===== Date utils (nicio schimbare funcțională) =====
        get displayDateRange(){ const d=this.getSelectedDates(); if(d&&d.checkIn&&d.checkOut) return d.check_in_check_out||`${d.checkIn} - ${d.checkOut}`; if(this.bookingForm.checkIn&&this.bookingForm.checkOut) return this.bookingForm.check_in_check_out||`${this.bookingForm.checkIn} - ${this.bookingForm.checkOut}`; return ''; },
        get hasDatesSelected(){ const d=this.getSelectedDates(); return (d&&d.checkIn&&d.checkOut)||(this.bookingForm.checkIn&&this.bookingForm.checkOut); },
        syncDateDisplay(){ const el=document.getElementById('check_in_check_out'); if(!el) return; const d=this.getSelectedDates(); if(d&&d.checkIn&&d.checkOut){ el.value=d.check_in_check_out||`${d.checkIn} - ${d.checkOut}`; el.setAttribute('placeholder',''); } else if(this.bookingForm.checkIn&&this.bookingForm.checkOut){ el.value=this.bookingForm.check_in_check_out||`${this.bookingForm.checkIn} - ${this.bookingForm.checkOut}`; el.setAttribute('placeholder',''); } else { el.value=''; el.setAttribute('placeholder','Select date range'); } },
        initDateRangePicker(){
          const self=this;
          $('#check_in_check_out').daterangepicker({ locale:{format:'MM/DD/YYYY',cancelLabel:'Clear'}, autoApply:false, autoUpdateInput:false, opens:'left', showDropdowns:true, minDate:moment() });
          $('#check_in_check_out').on('apply.daterangepicker', function(ev, picker){
            const s=picker.startDate.format('MM/DD/YYYY'), e=picker.endDate.format('MM/DD/YYYY'), r=s+' - '+e;
            $(this).val(r).attr('placeholder','');
            self.bookingForm.checkIn=s; self.bookingForm.checkOut=e; self.bookingForm.check_in_check_out=r;
            this.setAttribute('data-start', s); this.setAttribute('data-end', e);
            window.selectedDates = { checkIn:s, checkOut:e, check_in_check_out:r };
            self.$nextTick(()=> self.syncDateDisplay());
          });
          $('#check_in_check_out').on('cancel.daterangepicker', ()=>{ $('#check_in_check_out').val('').attr('placeholder','Select date range'); self.clearDates(); self.syncDateDisplay(); });
          this.$nextTick(()=> this.syncDateDisplay());
        },
        clearDates(){ this.bookingForm.checkIn=''; this.bookingForm.checkOut=''; this.bookingForm.check_in_check_out=''; window.selectedDates='Select date range'; const el=document.getElementById('check_in_check_out'); if(el){ el.value=''; el.setAttribute('placeholder','Select date range'); el.removeAttribute('data-start'); el.removeAttribute('data-end'); } },
        setDates(ci,co){ if(ci&&co){ this.bookingForm.checkIn=ci; this.bookingForm.checkOut=co; this.bookingForm.check_in_check_out=`${ci} - ${co}`; window.selectedDates={checkIn:ci,checkOut:co,check_in_check_out:`${ci} - ${co}`}; this.syncDateDisplay(); } },
        extractDatesFromInput(){ const el=document.getElementById('check_in_check_out'); if(!el) return; const v=el.value; if(v&&v.includes(' - ')){ const d=v.split(' - '); this.bookingForm.checkIn=d[0].trim(); this.bookingForm.checkOut=d[1].trim(); this.bookingForm.check_in_check_out=v; } else if(!v){ this.clearDates(); } },
        getSelectedDates(){ if(window.selectedDates) return window.selectedDates; if(this.bookingForm.checkIn&&this.bookingForm.checkOut) return {checkIn:this.bookingForm.checkIn,checkOut:this.bookingForm.checkOut,check_in_check_out:this.bookingForm.check_in_check_out}; const el=document.getElementById('check_in_check_out'); if(el){ const v=el.value, ds=el.getAttribute('data-start'), de=el.getAttribute('data-end'); if(ds&&de) return {checkIn:ds,checkOut:de,check_in_check_out:ds+' - '+de}; if(v&&v.includes(' - ')){ const d=v.split(' - '); return {checkIn:d[0].trim(),checkOut:d[1].trim(),check_in_check_out:v}; } } return null; },
        selectCheckInCheckOut(date){ if(date){ this.bookingForm.check_in_check_out=date; if(date.includes(' - ')){ const d=date.split(' - '); this.bookingForm.checkIn=d[0].trim(); this.bookingForm.checkOut=d[1].trim(); } } else { this.extractDatesFromInput(); } this.syncDateDisplay(); },

        // Dest & Lang
        selectDestination(id,name){ this.bookingForm.destination_id=id; this.bookingForm.destination=name; this.closeDestinationDropdown(); },
        selectLanguage(idOrName, name){ this.bookingForm.language_id=idOrName; this.bookingForm.language=name || idOrName; this.closeLanguageDropdown(); },
        closeDestinationDropdown(){ $('.tg-booking-quantity-toggle').removeClass('active'); $('.tg-booking-quantity-active').removeClass('tg-list-open'); },
        closeLanguageDropdown(){ $('.tg-booking-quantity-toggle').removeClass('active'); $('.tg-booking-quantity-active').removeClass('tg-list-open'); },

        // Submit redirect
        submitBookingForm(){
          const d=this.getSelectedDates();
          let ci=this.bookingForm.checkIn, co=this.bookingForm.checkOut, cc=this.bookingForm.check_in_check_out;
          if(d && (!ci||!co)){ ci=d.checkIn; co=d.checkOut; cc=d.check_in_check_out; }
          if(!ci || !co){ alert('Please select check-in and check-out dates'); return; }
          const params=new URLSearchParams({ destination:this.bookingForm.destination, destination_id:this.bookingForm.destination_id, checkIn:ci, checkOut:co, check_in_check_out:cc, rooms:this.bookingForm.rooms, adults:this.bookingForm.adults, children:this.bookingForm.children });
          window.location.href = `{{ route('front.tourbooking.services') }}?` + params.toString();
        },

        // AJAX
        fetchServices(){
          const that=this; this.loadingOverlay("show");
          const d=this.getSelectedDates();
          const req={ ...this.filters, ...this.bookingForm, page:this.page, isListView:this.isListView, style:this.style };
          if(d){ req.checkIn=d.checkIn; req.checkOut=d.checkOut; req.check_in_check_out=d.check_in_check_out; }
          $.ajax({
            url:`{{ route('front.tourbooking.services.load.ajax') }}`, method:'GET', data:req,
            success:(res)=>{ $('#filter_data').html(res.view); $('.custom_pagination_count').html(res.customPaginationCount); },
            error:(xhr)=>console.error(xhr),
            complete:()=>that.loadingOverlay("hide")
          });
        },

        searchServices(){
          this.page=1; this.fetchServices();
          const d=this.getSelectedDates(); const urlData={ ...this.bookingForm, ...this.filters };
          if(d){ urlData.checkIn=d.checkIn; urlData.checkOut=d.checkOut; urlData.check_in_check_out=d.check_in_check_out; }
          this.updateURL(urlData);
        },

        initializeAll(){
          $(document).on('click', '.pagination a', (e)=>{ e.preventDefault(); const page=$(e.target).attr('href').split('page=')[1]; this.page=page; this.fetchServices(); $("html, body").animate({scrollTop:0},500); });
          this.$nextTick(()=>{
            $('#sortSelect').niceSelect();
            $('#sortSelect').on('change', (e)=>{ this.filters.sort_by = e.target.value; });
            $('#sortSelect').val(this.filters.sort_by || 'default').niceSelect('update');
          });
          this.loadingOverlay("show"); this.fetchServices();
        },

        loadingOverlay(action='show', target=false){
          const o={size:50,maxSize:50,minSize:50};
          if(target && typeof target==='string') $(target).LoadingOverlay(action,o);
          else $.LoadingOverlay(action,o);
        },

        init(){
          // un singur loc unde facem fetch + update URL
          this.$watch('filters', () => {
            this.page = 1;
            this.fetchServices();
            this.updateURL({ ...this.bookingForm, ...this.filters });
          });

          this.initializeAll();

          this.$nextTick(() => {
            if(document.getElementById('check_in_check_out')){
              this.initDateRangePicker();
              if(this.bookingForm.checkIn && this.bookingForm.checkOut){
                this.setDates(this.bookingForm.checkIn, this.bookingForm.checkOut);
              }
            }
            hbInitPillsScroll(); // scroll pentru pills (cu click fix)
          });
        }
      }));
    });

    // HERO search (scoped)
    function hbHeroSearch(){
      return {
        destination:'', destination_id:'', check_in:'', check_out:'', check_in_check_out:'', language:'',
        init(){
          this.$el.querySelectorAll('.hb-field').forEach(f=>{
            f.addEventListener('click',(e)=>{ const d=f.querySelector('[x-ref="destList"]'); const l=f.querySelector('[x-ref="langList"]'); if(d) d.classList.toggle('open'); if(l) l.classList.toggle('open'); e.stopPropagation(); });
          });
          document.addEventListener('click', ()=>{ this.$refs?.destList?.classList?.remove('open'); this.$refs?.langList?.classList?.remove('open'); });

          const $date = $('#hb-date');
          if($date.length){
            $date.daterangepicker({ singleDatePicker:true, showDropdowns:true, autoApply:true, autoUpdateInput:true, opens:'left', drops:'down', minDate:moment(), locale:{ format:'MMM DD, YYYY' } });
            $date.on('apply.daterangepicker',(ev,picker)=>{ const disp=picker.startDate.format('MMM DD, YYYY'); const iso=picker.startDate.format('MM/DD/YYYY'); $date.val(disp); this.check_in=iso; this.check_out=iso; this.check_in_check_out=`${iso} - ${iso}`; });
            $date.on('cancel.daterangepicker',()=>{ $date.val(''); this.check_in=''; this.check_out=''; this.check_in_check_out=''; });
          }
        },
        selectDestination(id,name){ this.destination_id=id; this.destination=name; this.$refs?.destList?.classList?.remove('open'); },
        selectLanguage(lang){ this.language = lang || ''; this.$refs?.langList?.classList?.remove('open'); },
        submitForm(){
          const params = new URLSearchParams({ destination:this.destination, destination_id:this.destination_id, checkIn:this.check_in, checkOut:this.check_out, check_in_check_out:this.check_in_check_out, language:this.language || 'All Languages' });
          window.location.href = `{{ route('front.tourbooking.services') }}?` + params.toString();
        }
      }
    }

    // Scroll pentru pills — NU mai capturăm imediat pointerul; click-ul pe pill rămâne valid
    
  function hbInitPillsScroll(){
    const wrap = document.getElementById('hbPills');
    if (!wrap) return;

    const vp   = wrap.querySelector('[data-hb-viewport]');
    const track= wrap.querySelector('[data-hb-track]');
    const prev = wrap.querySelector('[data-hb-prev]');
    const next = wrap.querySelector('[data-hb-next]');
    const STEP = 320, THRESH = 6;

    // butoanele sunt mereu vizibile; doar le dezactivăm când nu e de scroll
    const updateNav = () => {
      if (!vp || !track) return;

      // limitele „start” / „end”
      const atStart = Math.ceil(vp.scrollLeft) <= 0;
      const atEnd   = Math.ceil(vp.scrollLeft + vp.clientWidth) >= Math.ceil(vp.scrollWidth) - 1;

      prev?.removeAttribute('class'); // nu mai folosim .hidden
      next?.removeAttribute('class');

      prev?.setAttribute('class', 'hb-nav left');
      next?.setAttribute('class', 'hb-nav right');

      // dezactivează/activează
      if (prev) prev.toggleAttribute('disabled', atStart);
      if (next) next.toggleAttribute('disabled', atEnd);
    };

    const scheduleUpdate = () => requestAnimationFrame(() => requestAnimationFrame(updateNav));

    // click pe săgeți
    prev?.addEventListener('click', () => {
      vp.scrollBy({ left: -STEP, behavior: 'smooth' });
    });
    next?.addEventListener('click', () => {
      vp.scrollBy({ left:  STEP, behavior: 'smooth' });
    });

    // drag-to-scroll cu prag ca să nu mănânce click-ul pe pill
    let down=false, dragging=false, startX=0, startLeft=0, pointerId=null;
    vp.addEventListener('pointerdown', e=>{
      down=true; dragging=false; startX=e.clientX; startLeft=vp.scrollLeft; pointerId=e.pointerId;
    });
    vp.addEventListener('pointermove', e=>{
      if(!down) return;
      const dx = e.clientX - startX;
      if(!dragging){
        if(Math.abs(dx) > THRESH){
          dragging = true;
          try { vp.setPointerCapture(pointerId); } catch(_) {}
          vp.style.cursor = 'grabbing';
        } else {
          return;
        }
      }
      vp.scrollLeft = startLeft - dx;
    });
    const endDrag = () => {
      down=false; dragging=false; vp.style.cursor='';
      try { if (pointerId!=null && vp.hasPointerCapture?.(pointerId)) vp.releasePointerCapture(pointerId); } catch(_) {}
      pointerId=null;
      scheduleUpdate();
    };
    vp.addEventListener('pointerup', endDrag);
    vp.addEventListener('pointercancel', endDrag);
    vp.addEventListener('pointerleave', endDrag);

    // wheel -> scroll orizontal
    vp.addEventListener('wheel', (e)=>{
      if(Math.abs(e.deltaY)>0){ e.preventDefault(); vp.scrollLeft += e.deltaY; }
      scheduleUpdate();
    }, { passive:false });

    // re-evaluează la scroll/resize/imagini/fonturi/load
    vp.addEventListener('scroll', updateNav);
    if ('ResizeObserver' in window){
      const ro = new ResizeObserver(scheduleUpdate);
      ro.observe(vp); ro.observe(track);
    } else {
      window.addEventListener('resize', scheduleUpdate);
    }
    for (const img of track.querySelectorAll('img')){
      if (!img.complete){
        img.addEventListener('load', scheduleUpdate, { once:true });
        img.addEventListener('error', scheduleUpdate, { once:true });
      }
    }
    if (document.fonts?.ready){
      document.fonts.ready.then(scheduleUpdate).catch(()=>{});
    }
    window.addEventListener('load', scheduleUpdate);

    // primă evaluare
    scheduleUpdate();
  }


  </script>
@endpush

@push('style_section')
  <link rel="stylesheet" href="{{ asset('global/datetimerange/daterangepicker.css') }}">

  <style>
    .tg-booking-add-input-field { width: 100%; }
    .trip-type-item { display:flex; border:1px solid #e1e1e1; border-radius:6px; padding:10px 17px; gap:10px; white-space:nowrap; }
    .trip-type-item-image img { width:40px; height:40px; }
    .trip-type-item-content h6 { font-size:14px; }
    .trip-type-item-content p { font-size:13px; }
    .trip-type-item.active { border-color: var(--tg-theme-primary); }

    .tg-booking-form-search-btn .bk-search-button { padding:10px 30px; width:100%; display:block; }
    input#check_in_check_out { height:43px; background: var(--tg-grey-3); width:100%; }
    .tg-booking-add-input-field { width:100%; }
    .item_loading { top:20px; position:relative; }
    .list-card.list-card-open .tg-grid-full .tg-listing-card-thumb { max-width:288px; }
    .tg-listing-item-wishlist.active svg { color: var(--tg-theme-primary); }
    .tg-listing-card-currency-amount del { font-weight:500; font-size:14px; line-height:1; color:#dbe6f7; display:block; }
    span.tg-listing-item-price-discount.shape-3 { top:48px; }
    .tg-listing-card-thumb { height:180px; }
    .tg-listing-card-thumb img { height:100%; object-fit:cover; }
    .list-card.list-card-open .tg-listing-card-thumb img { max-width:317px; min-width:317px; }

    /* ===== SCOPE: #hb-search ===== */
    #hb-search .hb-shell{background:#fff;border:1px solid #E6E6E6;border-radius:18px;padding:10px 14px;box-shadow:0 2px 0 rgba(0,0,0,.02);}
    #hb-search .hb-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr) minmax(0,1fr) 190px;align-items:center;gap:8px;}
    @media (max-width:1199.98px){ #hb-search .hb-grid{grid-template-columns:minmax(0,1fr) minmax(0,1fr);} }
    @media (max-width:575.98px){ #hb-search .hb-grid{grid-template-columns:1fr;} }

    #hb-search .hb-field{position:relative;display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:12px;}
    #hb-search .hb-field + .hb-field{border-left:2px solid #E6E6E6;}
    @media (max-width:1199.98px){ #hb-search .hb-field + .hb-field{border-left:0;} }

    #hb-search .hb-ico{opacity:.85;display:inline-flex;align-items:center;justify-content:center;width:18px;}
    #hb-search .hb-ico i{font-size:18px;line-height:18px;}
    #hb-search .hb-ico svg{width:18px;height:18px;}

    #hb-search .hb-ph{font-weight:600;color:#202124;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    #hb-search .hb-input{border:none;background:transparent;width:100%;height:46px;padding:0;font-weight:600;color:#202124;}
    #hb-search .hb-input::placeholder{color:#202124;opacity:1;font-weight:600;}

    #hb-search .hb-dd{position:absolute;top:100%;left:0;right:0;z-index:50;display:none;background:#fff;border:2px solid #E6E6E6;border-radius:12px;margin-top:8px;padding:6px;max-height:320px;overflow:auto;box-shadow:0 6px 24px rgba(0,0,0,.08);}
    #hb-search .hb-dd.open{display:block;}
    #hb-search .hb-dd li{padding:10px 12px;border-radius:10px;cursor:pointer;display:flex;align-items:center;gap:8px;}
    #hb-search .hb-dd li:hover{background:#F6F7F9;}

    #hb-search .hb-btn{width:100%;height:48px;border-radius:14px;background:#ff4200;color:#fff;font-weight:700;border:none;}
    #hb-search .hb-btn:hover{filter:brightness(.95);}

    /* Pills */
    #hb-search .hb-pills{position:relative;margin-top:28px;margin-bottom:12px;}
    #hb-search .hb-viewport{overflow-x:auto;overflow-y:hidden;-webkit-overflow-scrolling:touch;touch-action:pan-x;scroll-behavior:smooth;overscroll-behavior-x:contain;}
    #hb-search .hb-track{display:flex;gap:14px;align-items:center;will-change:transform;padding:2px 6px;}
    #hb-search .hb-pill{flex:0 0 auto;border:1px solid #E6E6E6;border-radius:999px;background:#fff;padding:12px 18px;display:flex;align-items:center;gap:14px;cursor:pointer;transition:border-color .15s ease, box-shadow .15s ease;}
    #hb-search .hb-pill b{font-weight:700;color:#1f1f1f;white-space:nowrap;}
    #hb-search .hb-pill small{color:#6b7280;line-height:1;white-space:nowrap;}
    #hb-search .hb-pill > img,#hb-search .hb-pill > i,#hb-search .hb-pill svg{width:28px;height:28px;}
    #hb-search .hb-pill > i{font-size:28px;line-height:1;display:inline-flex;align-items:start;justify-content:center;min-width:28px;}
    #hb-search .hb-pill > img{object-fit:contain;display:block;}
    /* hover + active cu același border */
    #hb-search .hb-pill:hover,
    #hb-search .hb-pill.active{ border-color:#ff4200; box-shadow:0 0 0 2px rgba(255,66,0,.08); }

    #hb-search .hb-nav{position:absolute;top:50%;transform:translateY(-50%);width:38px;height:38px;border-radius:999px;border:1px solid #e5e7eb;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 10px rgba(0,0,0,.06);z-index:5;}
    #hb-search .hb-nav.left{left:8px;} #hb-search .hb-nav.right{right:8px;}
    #hb-search .hb-nav.hidden{display:none;}
    #hb-search .hb-pills:before,#hb-search .hb-pills:after{content:"";position:absolute;top:0;bottom:0;width:24px;pointer-events:none;background:linear-gradient(to right,#fff,rgba(255,255,255,0));z-index:2;}
    #hb-search .hb-pills:before{left:0;} #hb-search .hb-pills:after{right:0;transform:scaleX(-1);}

    /* DateRangePicker skin (doar aici) */
    #hb-search .daterangepicker{border:1px solid #E6E6E6;border-radius:16px;box-shadow:0 12px 34px rgba(0,0,0,.10);overflow:hidden;font-family:var(--tg-ff-outfit,inherit);}
    #hb-search .daterangepicker.single .drp-calendar{width:auto!important;padding:14px 16px 6px 16px;}
    #hb-search .daterangepicker .calendar-table{border:none;}
    #hb-search .daterangepicker th,#hb-search .daterangepicker td{font-size:14px;color:#353844;width:34px;height:32px;text-align:center;border-radius:10px;}
    #hb-search .daterangepicker td.available:hover,#hb-search .daterangepicker th.available:hover{background:#F6F7F9;}
    #hb-search .daterangepicker td.off{color:#C2C6D0;opacity:.8;}
    #hb-search .daterangepicker td.active,#hb-search .daterangepicker td.active:hover{background:#ff4200;color:#fff!important;}
    #hb-search .daterangepicker .monthselect,#hb-search .daterangepicker .yearselect{border:1px solid #E6E6E6;border-radius:10px;padding:6px 8px;height:auto;font-size:14px;}
    #hb-search .daterangepicker .drp-buttons{padding:10px 14px;border-top:1px solid #F0F0F0;}
    #hb-search .daterangepicker .drp-buttons .btn{border-radius:10px;padding:8px 14px;font-weight:600;}
    #hb-search .daterangepicker .applyBtn{background:#ff4200;border-color:#ff4200;color:#fff;}
    #hb-search .daterangepicker .cancelBtn{display:none!important;}
    #hb-search .daterangepicker:before,#hb-search .daterangepicker:after{display:none;}
    
    
    
    /* Arătăm mereu săgețile; când sunt „disabled” le facem fade și fără click */
#hb-search .hb-nav { display:flex; z-index:10; }        /* puțin mai sus peste fade-urile laterale */
#hb-search .hb-nav[disabled] { opacity:.35; pointer-events:none; }

  </style>
@endpush
