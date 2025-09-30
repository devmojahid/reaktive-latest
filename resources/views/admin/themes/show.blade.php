@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Theme Details') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Theme Details') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Manage Themes') }} >> {{ ucfirst($theme) }}</p>
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
                                    <div class="crancy-theme-detail">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="crancy-theme-detail__img">
                                                    <img src="{{ $screenshot }}" alt="{{ $themeInfo['name'] ?? ucfirst($theme) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="crancy-theme-detail__content">
                                                    <div class="crancy-theme-detail__head">
                                                        <h3 class="crancy-theme-detail__title">{{ $themeInfo['name'] ?? ucfirst($theme) }}</h3>
                                                        @if(Theme::getActive() == $theme)
                                                            <span class="crancy-badge crancy-badge__success">{{ __('translate.Active') }}</span>
                                                        @endif
                                                    </div>
                                                    <p class="crancy-theme-detail__desc">{{ $themeInfo['description'] ?? 'No description available' }}</p>
                                                    
                                                    <div class="crancy-theme-detail__meta">
                                                        <div class="crancy-theme-detail__meta-item">
                                                            <span class="crancy-theme-detail__meta-label">{{ __('translate.Version') }}:</span>
                                                            <span class="crancy-theme-detail__meta-value">{{ $themeInfo['version'] ?? '1.0.0' }}</span>
                                                        </div>
                                                        <div class="crancy-theme-detail__meta-item">
                                                            <span class="crancy-theme-detail__meta-label">{{ __('translate.Author') }}:</span>
                                                            <span class="crancy-theme-detail__meta-value">{{ $themeInfo['author'] ?? 'Unknown' }}</span>
                                                        </div>
                                                        @if(isset($themeInfo['url']) && $themeInfo['url'])
                                                            <div class="crancy-theme-detail__meta-item">
                                                                <span class="crancy-theme-detail__meta-label">{{ __('translate.Website') }}:</span>
                                                                <a href="{{ $themeInfo['url'] }}" target="_blank" class="crancy-theme-detail__meta-value">{{ $themeInfo['url'] }}</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="crancy-theme-detail__actions mg-top-30">
                                                        @if(Theme::getActive() != $theme)
                                                            <form action="{{ route('admin.themes.activate', $theme) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="crancy-btn crancy-btn__md">{{ __('translate.Activate Theme') }}</button>
                                                            </form>
                                                            
                                                            <form action="{{ route('admin.themes.destroy', $theme) }}" method="POST" class="d-inline theme-delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="crancy-btn crancy-btn__md crancy-btn__red">{{ __('translate.Delete Theme') }}</button>
                                                            </form>
                                                        @endif
                                                        
                                                        <a href="{{ route('admin.themes.index') }}" class="crancy-btn crancy-btn__md crancy-btn__secondary">{{ __('translate.Back to Themes') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if(isset($themeInfo['required_plugins']) && count($themeInfo['required_plugins']) > 0)
                                            <div class="crancy-theme-requirements mg-top-40">
                                                <h4 class="crancy-theme-requirements__title">{{ __('translate.Required Plugins') }}</h4>
                                                <div class="crancy-theme-requirements__list">
                                                    <ul>
                                                        @foreach($themeInfo['required_plugins'] as $plugin)
                                                            <li>{{ $plugin }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
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
                
                if (confirm("Are you sure you want to delete this theme? This action cannot be undone.")) {
                    this.submit();
                }
            });
        });
    })(jQuery);
</script>
@endpush