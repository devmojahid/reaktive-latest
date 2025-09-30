@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Create Theme') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Create Theme') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Manage Themes') }} >> {{ __('translate.Create Theme') }}</p>
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
                                        <h3 class="crancy-theme-detail__title">{{ __('translate.Create New Theme') }}</h3>
                                        <p class="crancy-theme-detail__desc">{{ __('translate.Use the command below to create a new theme with the theme generator.') }}</p>
                                        
                                        <div class="crancy-theme-detail__command mg-top-30">
                                            <div class="crancy-theme-detail__command-content">
                                                <pre><code>php artisan theme:create theme_name --author="Your Name" --description="Your theme description"</code></pre>
                                            </div>
                                            <div class="crancy-theme-detail__command-copy">
                                                <button id="copy-command" class="crancy-btn crancy-btn__sm">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M8 4V16C8 17.1046 8.89543 18 10 18H18C19.1046 18 20 17.1046 20 16V7.41421C20 6.88378 19.7893 6.37507 19.4142 6L16 2.58579C15.6249 2.21071 15.1162 2 14.5858 2H10C8.89543 2 8 2.89543 8 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M16 18V20C16 21.1046 15.1046 22 14 22H6C4.89543 22 4 21.1046 4 20V8C4 6.89543 4.89543 6 6 6H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    {{ __('translate.Copy') }}
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="crancy-theme-detail__info mg-top-30">
                                            <h4 class="crancy-theme-detail__info-title">{{ __('translate.Theme Structure') }}</h4>
                                            <ul class="crancy-theme-detail__info-list">
                                                <li>
                                                    <span class="crancy-theme-detail__info-label">cms/themes/your_theme/</span>
                                                    <span class="crancy-theme-detail__info-value">{{ __('translate.Root directory of your theme') }}</span>
                                                </li>
                                                <li>
                                                    <span class="crancy-theme-detail__info-label">assets/</span>
                                                    <span class="crancy-theme-detail__info-value">{{ __('translate.Theme assets (CSS, JS, images)') }}</span>
                                                </li>
                                                <li>
                                                    <span class="crancy-theme-detail__info-label">functions/</span>
                                                    <span class="crancy-theme-detail__info-value">{{ __('translate.Theme functions and helpers') }}</span>
                                                </li>
                                                <li>
                                                    <span class="crancy-theme-detail__info-label">layouts/</span>
                                                    <span class="crancy-theme-detail__info-value">{{ __('translate.Theme layout templates') }}</span>
                                                </li>
                                                <li>
                                                    <span class="crancy-theme-detail__info-label">partials/</span>
                                                    <span class="crancy-theme-detail__info-value">{{ __('translate.Reusable template parts') }}</span>
                                                </li>
                                                <li>
                                                    <span class="crancy-theme-detail__info-label">views/</span>
                                                    <span class="crancy-theme-detail__info-value">{{ __('translate.Theme template files') }}</span>
                                                </li>
                                                <li>
                                                    <span class="crancy-theme-detail__info-label">config.php</span>
                                                    <span class="crancy-theme-detail__info-value">{{ __('translate.Theme configuration') }}</span>
                                                </li>
                                                <li>
                                                    <span class="crancy-theme-detail__info-label">theme.json</span>
                                                    <span class="crancy-theme-detail__info-value">{{ __('translate.Theme metadata') }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        
                                        <div class="crancy-theme-detail__actions mg-top-30">
                                            <a href="{{ route('admin.themes.index') }}" class="crancy-btn crancy-btn__md crancy-btn__secondary">{{ __('translate.Back to Themes') }}</a>
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
        "use strict";
        
        $(document).ready(function() {
            // Copy command to clipboard
            $('#copy-command').on('click', function() {
                const commandText = $('pre code').text();
                
                // Create a temporary textarea element to copy from
                const $temp = $('<textarea>');
                $('body').append($temp);
                $temp.val(commandText).select();
                document.execCommand('copy');
                $temp.remove();
                
                // Show copied message
                const originalText = $(this).text();
                $(this).text('Copied!');
                
                // Reset button text after 2 seconds
                setTimeout(function() {
                    $('#copy-command').html(`
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 4V16C8 17.1046 8.89543 18 10 18H18C19.1046 18 20 17.1046 20 16V7.41421C20 6.88378 19.7893 6.37507 19.4142 6L16 2.58579C15.6249 2.21071 15.1162 2 14.5858 2H10C8.89543 2 8 2.89543 8 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 18V20C16 21.1046 15.1046 22 14 22H6C4.89543 22 4 21.1046 4 20V8C4 6.89543 4.89543 6 6 6H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Copy
                    `);
                }, 2000);
            });
        });
    })(jQuery);
</script>
@endpush 