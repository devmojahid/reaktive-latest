@extends('agency.master_layout')
@section('title')
    <title>{{ __('translate.Services List') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Services List') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Services List') }}</p>
@endsection

@section('body-content')
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="crancy-table crancy-table--v3 mg-top-30">

                                <div class="crancy-customer-filter">
                                    <div
                                        class="crancy-header__form crancy-header__form--customer create_new_btn_inline_box">
                                        <h4 class="crancy-product-card__title">{{ __('translate.All Services') }}</h4>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ route('agency.tourbooking.services.create') }}"
                                                class="crancy-btn"><i class="fa fa-plus"></i>
                                                {{ __('translate.Add New Service') }}</a>
                                        </div>
                                    </div>
                                </div>

                                <div id="crancy-table__main_wrapper" class=" dt-bootstrap5 no-footer">
                                    <table class="crancy-table__main crancy-table__main-v3  no-footer" id="dataTable">
                                        <thead class="crancy-table__head">
                                            <tr>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Image') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Title') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Type') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Location') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Price') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Status') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="crancy-table__body">
                                            @foreach ($services as $service)
                                                <tr class="odd">
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        @if ($service->thumbnail && $service->thumbnail->file_path)
                                                            <img src="{{ asset($service->thumbnail->file_path) }}"
                                                                alt="{{ $service->translation->title ?? $service->title }}"
                                                                width="80">
                                                        @else
                                                            <img src="{{ asset('admin/img/img-placeholder.jpg') }}"
                                                                alt="No image" width="80">
                                                        @endif
                                                    </td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        {{ Str::limit($service->translation->title ?? $service->title, 50) }}
                                                    </td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        {{ $service->serviceType->name ?? 'N/A' }}</td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        {{ $service->location ?? 'N/A' }}</td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">

                                                        @if ($service->is_per_person)
                                                            @if ($service->price_per_person)
                                                                {{ currency($service->price_per_person) }}
                                                                ({{ __('translate.Per Person') }})
                                                                <br>
                                                            @endif

                                                            @if ($service->child_price)
                                                                {{ currency($service->child_price) }}
                                                                ({{ __('translate.Children Price') }})
                                                            @endif
                                                        @else
                                                            @if ($service->price_display)
                                                                {!! $service->price_display !!} ({{ __('translate.Full Price') }})
                                                            @else
                                                                {{ __('translate.N/A') }}
                                                            @endif
                                                        @endif

                                                    </td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        @if ($service->status)
                                                            <span
                                                                class="crancy-badge crancy-badge-success">{{ __('translate.Active') }}</span>
                                                        @else
                                                            <span
                                                                class="crancy-badge crancy-badge-danger">{{ __('translate.Inactive') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        <a href="{{ route('agency.tourbooking.services.edit', ['service' => $service->id, 'lang_code' => admin_lang()]) }}"
                                                            class="crancy-action__btn crancy-action__edit crancy-btn"><i
                                                                class="fa fa-edit"></i>
                                                            {{ __('translate.Edit') }}
                                                        </a>

                                                        <a onclick="itemDeleteConfrimation({{ $service->id }})"
                                                            href="javascript:;" data-bs-toggle="modal"
                                                            data-bs-target="#exampleModal"
                                                            class="crancy-btn delete_danger_btn"><i
                                                                class="fas fa-trash"></i>
                                                        </a>

                                                        <div class="dropdown" style="display: inline;">
                                                            <button class="crancy-action__btn" type="button"
                                                                style="width: 40px;"
                                                                id="dropdownMenuButton{{ $service->id }}"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fa fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu"
                                                                aria-labelledby="dropdownMenuButton{{ $service->id }}">
                                                                <li><a class="dropdown-item"
                                                                        href="{{ route('agency.tourbooking.services.itineraries', $service->id) }}">{{ __('translate.Itineraries') }}</a>
                                                                </li>
                                                                @if ($service->is_per_person)
                                                                    <li><a class="dropdown-item"
                                                                            href="{{ route('agency.tourbooking.services.extra-charges', $service->id) }}">{{ __('translate.Extra Charges') }}</a>
                                                                    </li>
                                                                @endif
                                                                <li><a class="dropdown-item"
                                                                        href="{{ route('agency.tourbooking.services.availability', $service->id) }}">{{ __('translate.Availability') }}</a>
                                                                </li>
                                                                <li><a class="dropdown-item"
                                                                        href="{{ route('agency.tourbooking.services.media', $service->id) }}">{{ __('translate.Media Gallery') }}</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('translate.Delete Confirmation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('translate.Are you realy want to delete this item?') }}</p>
            </div>
            <div class="modal-footer">
                <form action="" id="item_delect_confirmation" class="delet_modal_form" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('translate.Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('translate.Yes, Delete') }}</button>

                </form>
            </div>
        </div>
    </div>
</div>
{{-- @endsection --}}

@push('js_section')
    <script>
        "use strict"

        function itemDeleteConfrimation(id) {
            $("#item_delect_confirmation").attr("action", '{{ url('agency/tourbooking/services/') }}' + "/" + id)
        }
    </script>
@endpush
