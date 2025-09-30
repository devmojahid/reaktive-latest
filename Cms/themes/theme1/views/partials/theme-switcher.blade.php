<div class="theme-switcher">
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="theme-options">
                <span>@themetrans('theme_switcher_label') </span>
                <a href="{{ route('theme.switch', 'theme1') }}" class="{{ Theme::current() == 'theme1' ? 'active' : '' }}">@themetrans('theme_light')</a> | 
                <a href="{{ route('theme.switch', 'theme2') }}" class="{{ Theme::current() == 'theme2' ? 'active' : '' }}">@themetrans('theme_dark')</a>
            </div>
        </div>
    </div>
</div>

<style>
    .theme-switcher {
        padding: 5px 0;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    
    .theme-options {
        font-size: 14px;
    }
    
    .theme-options a {
        margin: 0 5px;
        color: #6c757d;
        text-decoration: none;
    }
    
    .theme-options a.active {
        font-weight: bold;
        color: #0d6efd;
    }
</style> 