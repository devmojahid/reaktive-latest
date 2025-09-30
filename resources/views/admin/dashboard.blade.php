@extends('admin.master_layout')

@section('title')
    <title>{{ __('translate.Dashboard') }}</title>
@endsection

@section('body-header')
    <div class="page-heading">
        <h3 class="crancy-header__title m-0">{{ __('translate.Dashboard') }}</h3>
        <p class="crancy-header__text">{{ __('Admin') }} >> {{ __('translate.Dashboard') }}</p>
    </div>
@endsection

@push('style_section')
    <link rel="stylesheet" href="{{ asset('backend/css/charts.min.css') }}">
    <style>
    .g-3 {
        padding-top: 20px;
    
    }
    </style>
@endpush

@section('body-content')
    <!-- Admin Dashboard -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <!-- Dashboard Inner -->
                        <div class="crancy-dsinner">

                            {{-- ===== KPIs / Stat Cards ===== --}}
                            <div class="row g-3 g-xxl-4 align-items-stretch">
                                

                                {{-- Total Sale --}}
                                <div class="col-xxl-3 col-md-6 col-12">
            
                                    <article class="crancy-ecom-card crancy-ecom-card__v2 stat-card">
                                        <div class="stat-card__inner">
                                            <div class="stat-card__icon">@include('svg.total_sale')</div>
                                            <div class="stat-card__content">
                                                <div class="stat-card__label"><b>{{ __('translate.Total Sale') }}</b></div>
                                                <div class="stat-card__value">
                                                    <span class="stat-pill">{{ currency($total_income) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                {{-- Admin Earnings --}}
                                <div class="col-xxl-3 col-md-6 col-12">
                                    <article class="crancy-ecom-card crancy-ecom-card__v2 stat-card">
                                        <div class="stat-card__inner">
                                            <div class="stat-card__icon">@include('svg.net_earning')</div>
                                            <div class="stat-card__content">
                                                <div class="stat-card__label"><b>{{ __('translate.Admin Earnings') }}</b></div>
                                                <div class="stat-card__value">
                                                    <span class="stat-pill">{{ currency($total_commission) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                {{-- Seller Earnings --}}
                                <div class="col-xxl-3 col-md-6 col-12">
                                    <article class="crancy-ecom-card crancy-ecom-card__v2 stat-card">
                                        <div class="stat-card__inner">
                                            <div class="stat-card__icon">@include('svg.instructor_earning')</div>
                                            <div class="stat-card__content">
                                                <div class="stat-card__label"><b>{{ __('translate.Seller Earnings') }}</b></div>
                                                <div class="stat-card__value">
                                                    <span class="stat-pill">{{ currency($net_income) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                {{-- Total Sold --}}
                                <div class="col-xxl-3 col-md-6 col-12">
                                    <article class="crancy-ecom-card crancy-ecom-card__v2 stat-card">
                                        <div class="stat-card__inner">
                                            <div class="stat-card__icon">@include('svg.total_sold')</div>
                                            <div class="stat-card__content">
                                                <div class="stat-card__label"><b>{{ __('translate.Total Sold') }}</b></div>
                                                <div class="stat-card__value">
                                                    <span class="stat-pill">{{ $total_sold }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            </div>

                            {{-- ===== Chart Card ===== --}}
                            @php
                                // Dacă vrei taburi de filtrare pentru chart, trimite din controller:
                                // $chart_week = [...]; $label_week = [...];
                                // $chart_month = [...]; $label_month = [...];
                                // $chart_year = [...]; $label_year = [...];
                                $hasFilters = isset($chart_week) || isset($chart_month) || isset($chart_year);
                            @endphp

                            <div class="row crancy-gap-30">
                                <div class="col-12">
                                    <section class="charts-main charts-home-one mg-top-30 glass-card">
                                        <div class="charts-main__heading d-flex align-items-center justify-content-between mg-btm-12">
                                            <h4 class="charts-main__title m-0">{{ __('translate.Booking Statistics') }}</h4>

                                            @if($hasFilters)
                                                <nav class="seg-pills" role="tablist" aria-label="Dataset range">
                                                    @isset($chart_week)
                                                        <button class="seg-pill is-active" data-dataset="week" type="button">This Week</button>
                                                    @endisset
                                                    @isset($chart_month)
                                                        <button class="seg-pill {{ !isset($chart_week) ? 'is-active' : '' }}" data-dataset="month" type="button">This Month</button>
                                                    @endisset
                                                    @isset($chart_year)
                                                        <button class="seg-pill {{ (!isset($chart_week) && !isset($chart_month)) ? 'is-active' : '' }}" data-dataset="year" type="button">This Year</button>
                                                    @endisset
                                                </nav>
                                            @endif
                                        </div>

                                        <div class="charts-main__one">
                                            <div class="crancy-chart__inside crancy-chart__three">
                                                <canvas id="myChart_recent_statics" height="320" aria-label="Bookings area chart" role="img"></canvas>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>

                            {{-- ===== Latest Bookings ===== --}}
                            <section class="crancy-table crancy-table--v3 mg-top-30 glass-card">
                                <header class="crancy-customer-filter">
                                    <div class="crancy-customer-filter__single crancy-customer-filter__single--csearch d-flex items-center justify-between create_new_btn_box">
                                        <div class="crancy-header__form crancy-header__form--customer create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title m-0">{{ __('translate.Latest Bookings') }}</h4>
                                        </div>
                                    </div>
                                </header>

                                <div id="crancy-table__main_wrapper" class="dt-bootstrap5 no-footer">
                                    <table class="crancy-table__main crancy-table__main-v3 no-footer" id="dataTable">
                                        <thead class="crancy-table__head">
                                            <tr>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Booking Code') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Service Title') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Total Amount') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Location') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Status') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="crancy-table__body">
                                            @foreach ($bookings as $booking)
                                                <tr>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">#{{ $booking->booking_code ?? 'N/A' }}</td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">{{ Str::limit($booking->service->title, 60) }}</td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">{{ currency($booking->total) }}</td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">{{ $booking?->service?->location ?? 'N/A' }}</td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        <span class="crancy-badge crancy-table__status--paid">{{ $booking->booking_status }}</span>
                                                    </td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        <a href="{{ route('admin.tourbooking.bookings.show', $booking) }}" class="crancy-action__btn crancy-action__edit crancy-btn">
                                                            <i class="fas fa-eye"></i> {{ __('translate.Details') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                        </div>
                        <!-- /Dashboard Inner -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Delete Modal (unchanged) --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('translate.Delete Confirmation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"><p>{{ __('translate.Are you realy want to delete this item?') }}</p></div>
            <div class="modal-footer">
                <form action="" id="item_delect_confirmation" class="delet_modal_form" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('translate.Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('translate.Yes, Delete') }}</button>
                </form>
            </div>
        </div></div>
    </div>
@endsection

@push('js_section')
    <script src="{{ asset('backend/js/charts.js') }}"></script>
    <script>
        "use strict";

        // ===== Base dataset (existing behaviour) =====
        let purchase_data = JSON.parse(@json($data));
        let date_lable    = JSON.parse(@json($lable));

        // ===== Optional alt datasets for pills (send them from controller to enable) =====
        const DATASETS = {};
        @isset($chart_week)
            DATASETS.week = {
                labels: JSON.parse(@json($label_week ?? [])),
                data:   JSON.parse(@json($chart_week))
            };
        @endisset
        @isset($chart_month)
            DATASETS.month = {
                labels: JSON.parse(@json($label_month ?? [])),
                data:   JSON.parse(@json($chart_month))
            };
        @endisset
        @isset($chart_year)
            DATASETS.year = {
                labels: JSON.parse(@json($label_year ?? [])),
                data:   JSON.parse(@json($chart_year))
            };
        @endisset

        const ctx = document.getElementById('myChart_recent_statics').getContext('2d');

// Gradient portocaliu pe brand
const area = ctx.createLinearGradient(0, 0, 0, 400);
area.addColorStop(0, 'rgba(255, 66, 0, 0.22)');
area.addColorStop(1, 'rgba(255, 66, 0, 0.02)');

const chart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: date_lable,
    datasets: [{
      label: 'Sells',
      data: purchase_data,
      backgroundColor: area,
      borderColor: '#ff4200',   // <- brand
      borderWidth: 3,
      tension: 0.35,
      pointRadius: 2,
      fill: true
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      x: { ticks:{ color:'#6B7280' }, grid:{ display:false, drawBorder:false } },
      y: { ticks:{ color:'#6B7280' }, grid:{ drawBorder:false, color:'#E5E7EB', borderDash:[5,5] } }
    },
    plugins: {
      legend: { display:false },
      tooltip: {
        backgroundColor:'#fff', titleColor:'#111827', bodyColor:'#374151',
        borderColor:'#E5E7EB', borderWidth:1, padding:10, cornerRadius:10
      }
    }
  }
});


        // ===== Pills switching (only if extra datasets exist) =====
        document.querySelectorAll('.seg-pill').forEach(btn=>{
            btn.addEventListener('click', ()=>{
                const key = btn.getAttribute('data-dataset');
                if (!DATASETS[key]) return;

                // UI state
                document.querySelectorAll('.seg-pill').forEach(b=>b.classList.remove('is-active'));
                btn.classList.add('is-active');

                // Swap data
                chart.data.labels = DATASETS[key].labels;
                chart.data.datasets[0].data = DATASETS[key].data;
                chart.update();
            });
        });

        // Delete action helper
        window.itemDeleteConfrimation = function(id){
            document.getElementById("item_delect_confirmation")
                .setAttribute("action", '{{ url('admin/course-enrollment-delete/') }}' + "/" + id);
        }
    </script>
@endpush
