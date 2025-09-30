{{-- resources/views/admin/partials/sidebar.blade.php --}}
@php
    // Helpers pentru gruparea routelor
    $isWithdraw   = Route::is('admin.withdraw-methods.*') || Route::is('admin.withdraw-list.*');
    $isAgency     = Route::is('admin.seller-list') || Route::is('admin.pending-seller') || Route::is('admin.seller-show') || Route::is('admin.seller-joining-request') || Route::is('admin.seller-joining-detail');
    $isUsersA     = Route::is('admin.user-list') || Route::is('admin.pending-user') || Route::is('admin.user-show');
    $isOrders     = Route::is('admin.orders') || Route::is('admin.order') || Route::is('admin.active-orders') || Route::is('admin.reject-orders') || Route::is('admin.delivered-orders') || Route::is('admin.complete-orders') || Route::is('admin.pending-payment-orders');
    $isProducts   = Route::is('admin.product.index') || Route::is('admin.product.create') || Route::is('admin.product.edit') || Route::is('admin.brand.*') || Route::is('admin.category.*') || Route::is('admin.sub-category.*');
    $isBlog       = Route::is('admin.blog.*') || Route::is('admin.blog-category.*') || Route::is('admin.comment-list') || Route::is('admin.show-comment');
    $isPages      = Route::is('admin.terms-conditions') || Route::is('admin.privacy-policy') || Route::is('admin.faq.*') || Route::is('admin.custom-page.*') || Route::is('admin.contact-us');
    $isContent    = Route::is('admin.front-end.frontend-section') || Route::is('admin.front-end.section') || Route::is('admin.testimonial.*') || Route::is('admin.partner.*') || Route::is('admin.footer');
    $isLanguages  = Route::is('admin.language.*') || Route::is('admin.theme-language');
    $isEmailCfg   = Route::is('admin.email-setting') || Route::is('admin.email-template') || Route::is('admin.edit-email-template');
    $isWebsite    = Route::is('admin.cookie-consent') || Route::is('admin.error-image') || Route::is('admin.login-image') || Route::is('admin.breadcrumb') || Route::is('admin.social-login') || Route::is('admin.default-avatar') || Route::is('admin.maintenance-mode') || Route::is('admin.admin-login-image');
@endphp

@if (! function_exists('pt_t'))
    @php
        /** Fallback de traducere: dacă cheia nu există, afișează $fallback */
        function pt_t(string $key, string $fallback){
            $v = __($key);
            return $v === $key ? $fallback : $v;
        }
    @endphp
@endif

<style>
    :root { --pt-brand: var(--theme-color, #ff4200); --pt-bg:#ffffff; --pt-ink:#2a2f3a; --pt-muted:#5d6a83; --pt-sep:#eef0f4; }
    .pt-sidebar {position:sticky; top:0; height:100vh; background:var(--pt-bg); border-right:1px solid var(--pt-sep); padding:18px 14px; overflow-y:auto;}
    .pt-brand {display:flex; align-items:center; gap:10px; margin:4px 4px 18px;}
    .pt-brand__logo {width:38px; height:38px; border-radius:10px; background:color-mix(in srgb, var(--pt-brand) 14%, #fff); display:grid; place-items:center;}
    .pt-brand__logo svg{color:var(--pt-brand);}
    .pt-brand__name {font-weight:700; letter-spacing:.2px; color:var(--pt-ink);}
    .pt-section {font-size:.78rem; letter-spacing:.06em; text-transform:uppercase; color:var(--pt-muted); padding:12px 10px 6px;}
    .pt-nav {list-style:none; padding:0; margin:0;}
    .pt-item {margin:4px 6px;}
    .pt-link {display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:12px; color:var(--pt-ink); text-decoration:none; position:relative; transition:.2s ease;}
    .pt-link:hover {background:#f9f8ff;}
    .pt-icon {flex:0 0 auto; width:34px; height:34px; border-radius:10px; display:grid; place-items:center; background:#f6f7fb; color:#77809a;}
    .pt-text {font-weight:600; font-size:.98rem;}
    .pt-item.is-active > .pt-link {background:color-mix(in srgb, var(--pt-brand) 11%, #fff); color:var(--pt-brand);}
    .pt-item.is-active .pt-icon {background:color-mix(in srgb, var(--pt-brand) 18%, #fff); color:var(--pt-brand);}
    .pt-caret {margin-left:auto; width:18px; height:18px; display:grid; place-items:center; color:#9aa4b2;}
    .pt-collapse {padding-left:46px; border-left:2px solid #f0f2f7; margin-left:20px;}
    .pt-collapse .pt-link {padding:8px 10px; border-radius:10px; font-weight:500;}
    .pt-sep-line {height:1px; background:var(--pt-sep); margin:14px 8px;}
    .pt-bottom {padding:12px 8px 18px; color:var(--pt-muted); font-size:.86rem;}
    @media (max-width: 1024px){
        .pt-sidebar {position:fixed; z-index:1040; transform:translateX(-102%); transition:.25s ease;}
        .pt-sidebar.is-open {transform:none; box-shadow:0 20px 60px rgba(0,0,0,.12);}
    }
    
    
    
</style>

<aside class="pt-sidebar" id="adminSidebar">
   

    <ul class="pt-nav">
        <li class="pt-item {{ Route::is('admin.dashboard') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.dashboard') }}">
                <span class="pt-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                    </svg>
                </span>
                <span class="pt-text">{{ __('translate.Dashboard') }}</span>
            </a>
        </li>
    </ul>

    {{-- Meniuri modulare din tourbooking --}}
    @include('tourbooking::admin.sidebar')

    <div class="pt-section">{{ pt_t('translate.Operations','Operations') }}</div>
    <ul class="pt-nav">
        <li class="pt-item {{ $isWithdraw ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-withdraw" aria-expanded="{{ $isWithdraw ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="2" y="6" width="20" height="12" rx="3"/><path d="M16 12h4"/></svg></span>
                <span class="pt-text">{{ __('translate.Manage Withdraw') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isWithdraw ? 'show' : '' }}" id="pt-withdraw" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.withdraw-methods.index') }}">{{ __('translate.Withdraw Method') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.withdraw-list.index') }}">{{ __('translate.Withdraw List') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item {{ $isAgency ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-agency" aria-expanded="{{ $isAgency ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M10 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><rect x="3" y="7" width="18" height="13" rx="3"/><circle cx="12" cy="13" r="2"/><path d="M8 19a4 4 0 0 1 8 0"/></svg></span>
                <span class="pt-text">{{ __('translate.Manage Agency') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isAgency ? 'show' : '' }}" id="pt-agency" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.seller-list') }}">{{ __('translate.Agency List') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.seller-joining-request') }}">{{ __('translate.Join Request') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item {{ $isUsersA ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-users-a" aria-expanded="{{ $isUsersA ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="3"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a3 3 0 0 1 0 5.75"/></svg></span>
                <span class="pt-text">{{ __('translate.Manage User') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isUsersA ? 'show' : '' }}" id="pt-users-a" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.user-list') }}">{{ __('translate.User List') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.pending-user') }}">{{ __('translate.Pending User') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item {{ Route::is('admin.contact-message') || Route::is('admin.show-message') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.contact-message') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg></span>
                <span class="pt-text">{{ __('translate.Contact Message') }}</span>
            </a>
        </li>

        <li class="pt-item {{ Route::is('admin.support-tickets') || Route::is('admin.support-ticket') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.support-tickets') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 9a3 3 0 0 1 0-6h18a3 3 0 0 1 0 6"/><path d="M3 15a3 3 0 0 0 0 6h18a3 3 0 0 0 0-6"/><path d="M12 3v18"/></svg></span>
                <span class="pt-text">{{ __('translate.Support Ticket') }}</span>
            </a>
        </li>
    </ul>

    <div class="pt-sep-line"></div>

 <!--   <div class="pt-section">{{ pt_t('translate.Product & Review','Product & Review') }}</div>
    <ul class="pt-nav">
        <li class="pt-item {{ $isOrders ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-orders" aria-expanded="{{ $isOrders ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39A2 2 0 0 0 9.62 16H19a2 2 0 0 0 2-1.72L23 7H6"/></svg></span>
                <span class="pt-text">{{ __('translate.Manage Order') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isOrders ? 'show' : '' }}" id="pt-orders" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.orders') }}">{{ __('translate.All Orders') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.active-orders') }}">{{ __('translate.Active Orders') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.reject-orders') }}">{{ __('translate.Rejected Orders') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.delivered-orders') }}">{{ __('translate.Delivered Orders') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.complete-orders') }}">{{ __('translate.Complete Orders') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.pending-payment-orders') }}">{{ __('translate.Pending Payment Order') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item {{ $isProducts ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-products" aria-expanded="{{ $isProducts ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M21 16V8a2 2 0 0 0-1-1.73L13 2.27a2 2 0 0 0-2 0L4 6.27A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.27 6.96 12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg></span>
            <span class="pt-text">{{ __('translate.Manage Product') }}</span>
            <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isProducts ? 'show' : '' }}" id="pt-products" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.product.create') }}">{{ __('translate.Create Product') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.product.index') }}">{{ __('translate.Product List') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.category.index') }}">{{ __('translate.Category List') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.brand.index') }}">{{ __('translate.Brand List') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item {{ Route::is('admin.product.review.list') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.product.review.list') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m12 17.27 6.18 3.73-1.64-7.03L21 9.24l-7.19-.62L12 2 10.19 8.62 3 9.24l4.46 4.73L5.82 21z"/></svg></span>
                <span class="pt-text">{{ __('translate.Review List') }}</span>
            </a>
        </li>

        <li class="pt-item {{ Route::is('admin.shipping-method.index') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.shipping-method.index') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M10 17h4V5H3v12h3"/><path d="M14 17h2a3 3 0 1 0 6 0h-1v-5h-4l-2-3z"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="19.5" cy="17.5" r="2.5"/></svg></span>
                <span class="pt-text">{{ __('translate.Shipping') }}</span>
            </a>
        </li>
    </ul>  -->

    <div class="pt-section">{{ pt_t('translate.Team & Users','Team & Users') }}</div>
    <ul class="pt-nav">
        <li class="pt-item {{ Route::is('admin.team.*') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.team.index') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M17 21v-2a4 4 0 0 0-4-4h-3a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="3"/><circle cx="19" cy="8" r="2.5"/></svg></span>
                <span class="pt-text">{{ __('translate.Manage Team') }}</span>
            </a>
        </li>

        <li class="pt-item {{ $isUsersA ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-users-b" aria-expanded="{{ $isUsersA ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8 7a4 4 0 1 0 8 0 4 4 0 0 0-8 0Z"/><path d="M3 21a9 9 0 1 1 18 0"/></svg></span>
                <span class="pt-text">{{ __('translate.Manage Users') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isUsersA ? 'show' : '' }}" id="pt-users-b" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.user-list') }}">{{ __('translate.User List') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.pending-user') }}">{{ __('translate.Pending User') }}</a></li>
                </ul>
            </div>
        </li>
    </ul>

    <div class="pt-section">{{ pt_t('translate.CMS & Blogs','CMS & Blogs') }}</div>
    <ul class="pt-nav">
        <li class="pt-item {{ $isBlog ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-blog" aria-expanded="{{ $isBlog ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 19a2 2 0 0 1-2-2V5h14v12a2 2 0 0 1-2 2H4Z"/><path d="M22 7v10a2 2 0 0 1-2 2h-4"/><path d="M8 13h4M8 9h8M8 17h4"/></svg></span>
                <span class="pt-text">{{ __('translate.Manage Blog') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isBlog ? 'show' : '' }}" id="pt-blog" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.blog-category.create') }}">{{ __('translate.Create Categroy') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.blog-category.index') }}">{{ __('translate.Categroy List') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.blog.create') }}">{{ __('translate.Create Blog') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.blog.index') }}">{{ __('translate.Blog List') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.comment-list') }}">{{ __('translate.Comment List') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item {{ $isPages ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-pages" aria-expanded="{{ $isPages ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8 2h7l5 5v13a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><path d="M13 2v6h6"/></svg></span>
                <span class="pt-text">{{ __('translate.Manage Pages') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isPages ? 'show' : '' }}" id="pt-pages" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.contact-us', ['lang_code' => admin_lang()]) }}">{{ __('translate.Contact Us') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.terms-conditions', ['lang_code' => admin_lang()]) }}">{{ __('translate.Terms and Conditions') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.privacy-policy', ['lang_code' => admin_lang()]) }}">{{ __('translate.Privacy Policy') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.faq.index') }}">{{ __('translate.FAQ') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.custom-page.index') }}">{{ __('translate.Custom Page') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item {{ $isContent ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-content" aria-expanded="{{ $isContent ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="5" rx="2"/><rect x="13" y="10" width="8" height="11" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/></svg></span>
                <span class="pt-text">{{ __('translate.Manage Content') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isContent ? 'show' : '' }}" id="pt-content" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.front-end.frontend-section') }}">{{ __('translate.Frontend Section') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.footer', ['lang_code' => admin_lang()]) }}">{{ __('translate.Footer Info') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.testimonial.index') }}">{{ __('translate.Testimonial') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.partner.index') }}">{{ __('translate.Partner') }}</a></li>
                </ul>
            </div>
        </li>
    </ul>

    <div class="pt-section">{{ pt_t('translate.Setting & Configuration','Settings & Configuration') }}</div>
    <ul class="pt-nav">
        <li class="pt-item {{ Route::is('admin.general-setting') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.general-setting') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/></svg></span>
                <span class="pt-text">{{ __('translate.Setting') }}</span>
            </a>
        </li>

        <li class="pt-item {{ Route::is('admin.multi-currency.*') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.multi-currency.index') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M2.05 12h19.9M12 2.05a15.9 15.9 0 0 1 0 19.9M12 2.05a15.9 15.9 0 0 0 0 19.9"/></svg></span>
                <span class="pt-text">{{ __('translate.Multi Currency') }}</span>
            </a>
        </li>

        <li class="pt-item {{ $isLanguages ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-lang" aria-expanded="{{ $isLanguages ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 5h12M9 3v2a9 9 0 0 1-7 7"/><path d="m9 12 3-3 3 3"/><path d="M12 7a9 9 0 1 0 9 9"/></svg></span>
                <span class="pt-text">{{ __('translate.Language') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isLanguages ? 'show' : '' }}" id="pt-lang" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.language.index') }}">{{ __('translate.Languages') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.theme-language', ['lang_code' => 'en']) }}">{{ __('translate.Theme Languages') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item {{ $isEmailCfg ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-email" aria-expanded="{{ $isEmailCfg ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
                <span class="pt-text">{{ __('translate.Email Configuration') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isEmailCfg ? 'show' : '' }}" id="pt-email" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.email-setting') }}">{{ __('translate.Configuration') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.email-template') }}">{{ __('translate.Email Template') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item {{ $isWebsite ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-website" aria-expanded="{{ $isWebsite ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></span>
                <span class="pt-text">{{ __('translate.Website Setup') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ $isWebsite ? 'show' : '' }}" id="pt-website" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.cookie-consent') }}">{{ __('translate.Cookie Consent') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.error-image') }}">{{ __('translate.Error Page') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.login-image') }}">{{ __('translate.Login Page') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.admin-login-image') }}">{{ __('translate.Admin Login') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.breadcrumb') }}">{{ __('translate.Breadcrumb Image') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.social-login') }}">{{ __('translate.Social Login') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.default-avatar') }}">{{ __('translate.Default Avatar') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.maintenance-mode') }}">{{ __('translate.Maintenance mode') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item {{ Route::is('admin.seo-setting') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.seo-setting') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg></span>
                <span class="pt-text">{{ __('translate.SEO Setup') }}</span>
            </a>
        </li>

        <li class="pt-item {{ Route::is('admin.paymentgateway') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.paymentgateway') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 9h20"/></svg></span>
                <span class="pt-text">{{ __('translate.Payment Method') }}</span>
            </a>
        </li>

        <!--<li class="pt-item {{ Route::is('admin.themes.index') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.themes.index') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/></svg></span>
                <span class="pt-text">{{ __('translate.Theme Management') }}</span>
            </a>
        </li> -->

        <li class="pt-item {{ Route::is('admin.menus.index') ? 'is-active' : '' }}">
            <a class="pt-link" href="{{ route('admin.menus.index') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8 6h13M8 12h13M8 18h13"/><path d="M3 6h.01M3 12h.01M3 18h.01"/></svg></span>
                <span class="pt-text">{{ __('translate.Menu Management') }}</span>
            </a>
        </li>
    </ul>

    <div class="pt-section">{{ pt_t('translate.Others','Others') }}</div>
    <ul class="pt-nav">
        <li class="pt-item {{ Route::is('admin.newsletter-list') || Route::is('admin.newsletter-email') ? 'is-active' : '' }}">
            <a class="pt-link" data-bs-toggle="collapse" href="#pt-news" aria-expanded="{{ Route::is('admin.newsletter-list') || Route::is('admin.newsletter-email') ? 'true' : 'false' }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m4 12 8 5 8-5"/></svg></span>
                <span class="pt-text">{{ __('translate.Newsletter') }}</span>
                <span class="pt-caret"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
            </a>
            <div class="collapse pt-collapse {{ Route::is('admin.newsletter-list') || Route::is('admin.newsletter-email') ? 'show' : '' }}" id="pt-news" data-bs-parent="#adminSidebar">
                <ul class="pt-nav">
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.newsletter-list') }}">{{ __('translate.Subscriber List') }}</a></li>
                    <li class="pt-item"><a class="pt-link" href="{{ route('admin.newsletter-email') }}">{{ __('translate.Send Mail') }}</a></li>
                </ul>
            </div>
        </li>

        <li class="pt-item">
            <a class="pt-link" href="{{ route('admin.cache-clear') }}">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 3v6h-6"/></svg></span>
                <span class="pt-text">{{ __('translate.Cache Clear') }}</span>
            </a>
        </li>

        <li class="pt-item">
            <a class="pt-link" href="javascript:;" onclick="event.preventDefault(); document.getElementById('admin-sidebar-logout').submit();">
                <span class="pt-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg></span>
                <span class="pt-text">{{ __('translate.Logout') }}</span>
            </a>
        </li>
        <form id="admin-sidebar-logout" action="{{ route('admin.logout') }}" method="POST" class="d-none">@csrf</form>
    </ul>

    <div class="pt-bottom">{{ __('translate.Version') }} : {{ $general_setting->app_version }}</div>
</aside>
