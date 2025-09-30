@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Manage Themes') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Themes') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Manage & Update Themes') }}</p>
@endsection

@section('body-content')
    <!-- crancy Dashboard -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12">
                                    <div class="crancy-notice__inner crancy-notice__inner--admin mg-top-20">
                                        <div class="crancy-notice__content">
                                            <h3 class="crancy-notice__title">{{ __('translate.Themes Management') }}</h3>
                                            <p class="crancy-notice__text">
                                                {{ __('translate.Manage and update your website themes') }}</p>
                                            <div class="crancy-btn-group mg-top-20">
                                                <a href="{{ route('admin.themes.create') }}"
                                                    class="crancy-btn crancy-btn__icon">
                                                    <span class="crancy-btn__icon-circle">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M12 4V20M20 12H4" stroke="currentColor"
                                                                stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg>
                                                    </span>
                                                    {{ __('translate.Create Theme') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mg-top-30">
                                @if (count($themes) > 0)
                                    @foreach ($themes as $theme_name => $theme_info)
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="crancy-theme-card bg-white crancy-shadow mb-4 br-4">
                                                <div class="crancy-theme-card__img">
                                                    @if (file_exists(public_path('backend/img/theme/' . $theme_name . '.png')))
                                                        <img src="{{ asset('backend/img/theme/' . $theme_name . '.png') }}"
                                                            alt="{{ $theme_info['name'] ?? $theme_name }}">
                                                    @else
                                                        <img src="{{ asset('backend/img/placeholder-image.jpg') }}"
                                                            alt="{{ $theme_info['name'] ?? $theme_name }}">
                                                    @endif
                                                </div>
                                                <div class="crancy-theme-card__content p-4">
                                                    <div class="crancy-theme-card__head">
                                                        <h4 class="crancy-theme-card__title">
                                                            {{ $theme_info['name'] ?? ucfirst($theme_name) }}</h4>
                                                        @if ($active_theme == $theme_name)
                                                            <span
                                                                class="crancy-badge crancy-badge__success">{{ __('translate.Active') }}</span>
                                                        @endif
                                                    </div>
                                                    <p class="crancy-theme-card__text">
                                                        {{ $theme_info['description'] ?? 'No description available' }}</p>
                                                    <div class="crancy-theme-card__meta">
                                                        <span>{{ __('translate.Version') }}:
                                                            {{ $theme_info['version'] ?? '1.0.0' }}</span>
                                                        <span>{{ __('translate.Author') }}:
                                                            {{ $theme_info['author'] ?? 'Unknown' }}</span>
                                                    </div>
                                                    <div class="crancy-theme-card__btn mt-3 d-flex gap-3">
                                                        <a href="{{ route('admin.themes.show', $theme_name) }}"
                                                            class="crancy-btn crancy-btn__sm crancy-btn__secondary">{{ __('translate.Details') }}</a>

                                                        @if ($active_theme != $theme_name)
                                                            <div>
                                                                <form
                                                                    action="{{ route('admin.themes.activate', $theme_name) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="crancy-btn crancy-btn__sm">{{ __('translate.Activate') }}</button>
                                                                </form>
                                                            </div>

                                                            <div>
                                                                <form
                                                                    action="{{ route('admin.themes.destroy', $theme_name) }}"
                                                                    method="POST" class="d-inline theme-delete-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="crancy-btn crancy-btn__sm crancy-btn__red">{{ __('translate.Delete') }}</button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <div class="crancy-empty-state">
                                            <div class="crancy-empty-state__img">
                                                <img src="{{ asset('backend/images/empty-themes.svg') }}" alt="No Themes">
                                            </div>
                                            <h3 class="crancy-empty-state__title">{{ __('translate.No Themes Found') }}
                                            </h3>
                                            <p class="crancy-empty-state__text">
                                                {{ __('translate.You have not created any themes yet. Click the button below to create your first theme.') }}
                                            </p>
                                            <a href="{{ route('admin.themes.create') }}"
                                                class="crancy-btn crancy-btn__md">{{ __('translate.Create Theme') }}</a>
                                        </div>
                                    </div>
                                @endif
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
            "use strict";

            $(document).ready(function() {
                // Confirm before deleting a theme
                $('.theme-delete-form').on('submit', function(e) {
                    e.preventDefault();

                    if (confirm(
                            "Are you sure you want to delete this theme? This action cannot be undone."
                        )) {
                        this.submit();
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
