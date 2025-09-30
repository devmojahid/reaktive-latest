@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Frontend Section') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Frontend Section') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Manage Content') }} >> {{ __('translate.Frontend Section') }}</p>
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
                                    <div class="container">
                                        <h4 class="mb-4">{{ __('translate.Theme Settings for') }}: <span class="badge bg-primary">{{ $activeTheme }}</span></h4>

                                        <!-- Tabs for pages -->
                                        <ul class="nav nav-tabs" id="pagesTabs" role="tablist">
                                            @foreach($sectionsByPage as $page => $pageSections)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                            id="{{ $page }}-tab"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#{{ $page }}"
                                                            type="button"
                                                            role="tab"
                                                            aria-controls="{{ $page }}"
                                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                        {{ ucfirst($page) }}
                                                        <span class="badge bg-secondary ms-1">{{ count($pageSections) }}</span>
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>

                                        <!-- Tab content -->
                                        <div class="tab-content" id="pagesTabsContent">
                                            @foreach($sectionsByPage as $page => $pageSections)
                                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                                     id="{{ $page }}"
                                                     role="tabpanel"
                                                     aria-labelledby="{{ $page }}-tab">

                                                    <div class="row mt-4">
                                                        @foreach($pageSections as $key => $section)
                                                            <div class="col-md-4 mb-4">
                                                                <div class="card h-100 section-card {{ isset($section['theme']) ? 'border-primary' : 'border-primary' }}">
                                                                    <div class="card-header bg-{{ isset($section['theme']) ? 'primary' : 'primary' }} text-white d-flex justify-content-between align-items-center">
                                                                        <h5 class="card-title mb-0">{{ $section['name'] }}</h5>
                                                                        @if(isset($section['order']))
                                                                            <span class="badge bg-light text-dark">
                                                                                {{ __('translate.Order') }}: {{ $section['order'] }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                                            <div>
                                                                                @if(isset($section['theme']))
                                                                                    <span class="badge bg-info">{{ $section['theme'] }}</span>
                                                                                @endif
                                                                                @if(isset($section['common']) && $section['common'])
                                                                                    <span class="badge bg-success">{{ __('translate.Common') }}</span>
                                                                                @endif
                                                                            </div>

                                                                            @if(isset($section['content']) && isset($section['element']))
                                                                                <span class="badge bg-warning">
                                                                                    {{ __('translate.Content & Elements') }}
                                                                                </span>
                                                                            @elseif(isset($section['content']))
                                                                                <span class="badge bg-info">
                                                                                    {{ __('translate.Content') }}
                                                                                </span>
                                                                            @elseif(isset($section['element']))
                                                                                <span class="badge bg-secondary">
                                                                                    {{ __('translate.Elements') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>

                                                                        <p class="card-text">
                                                                            @if(isset($section['content']))
                                                                                <small>{{ count($section['content']) }} {{ __('translate.content fields') }}</small>
                                                                            @endif

                                                                            @if(isset($section['element']))
                                                                                <small>{{ count($section['element']) }} {{ __('translate.element fields') }}</small>
                                                                            @endif
                                                                        </p>

                                                                        <a href="{{ route('admin.front-end.section', ['id'=> $key, 'lang_code' => admin_lang()]) }}"
                                                                           class="btn btn-primary mt-2">
                                                                            <i class="fas fa-edit"></i> {{ __('translate.Edit') }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
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

@push('style_section')
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: bold;
    }

    .card-header h5 {
        font-size: 1.1rem;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        max-width: 200px;
    }

    @media (max-width: 768px) {
        .card-header h5 {
            max-width: 150px;
        }
    }
</style>
@endpush
