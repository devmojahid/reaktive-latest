<!DOCTYPE html>
<html class="no-js" lang="zxx">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  @yield('title')

  <link rel="icon" href="{{ asset($general_setting->favicon) }}">

  <!-- CSS existente -->
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('global/datatable/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/slick.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/font-awesome-all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/nice-select.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/reset.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/enrollment.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/overview.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/dev.css') }}">
  <link rel="stylesheet" href="{{ asset('global/toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">

  @stack('style_section')

  <style>
    :root{
      --pt-sb-open: 300px;
      --sbw: var(--pt-sb-open);
      --pt-brand: var(--theme-color, #ff4200);
      --pt-ink:#2a2f3a; --pt-bg:#fff; --pt-sep:#eef0f4;
    }
    body.sb-closed{ --sbw: 0px; }

    /* ===== Sidebar ===== */
    .crancy-smenu{
      position: fixed; inset: 0 auto 0 0;
      width: var(--sbw);
      overflow: hidden;
      transition: width .22s ease;
      z-index: 1040;
      background:#fff;
      border-right:1px solid var(--pt-sep);
      transform:none !important; /* blochează transform-urile vechi */
    }
    body.sb-closed .crancy-smenu{ border-right:0; }

    .admin-menu a{ display:flex; align-items:center; gap:10px; padding:10px 14px; }
    .crancy-menu-icon{ flex:0 0 22px; display:grid; place-items:center; }
    .menu-bar__name{ flex:1 1 auto; min-width:0; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; }

    /* ===== Header ===== */
    .pt-header{
      position: sticky; top:0; z-index:1020;
      background:var(--pt-bg);
      border-bottom:1px solid var(--pt-sep);
      margin-left: var(--sbw);
      width: calc(100% - var(--sbw));
      transition: margin-left .22s ease, width .22s ease;
    }
    .pt-header > .container{ max-width:100% !important; padding-left:0 !important; padding-right:0 !important; }

    .pt-headbar{display:flex; align-items:center; justify-content:space-between; gap:16px; padding:10px 16px;}
    .pt-left{display:flex; align-items:center; gap:12px;}
    .pt-title{font-weight:700; color:var(--pt-ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}

    #ptHeaderToggle{
      display:inline-grid; place-items:center;
      width:42px; height:42px; border-radius:12px;
      border:1px solid var(--pt-sep); background:#fff;
      box-shadow:0 1px 0 rgba(0,0,0,.02); cursor:pointer; transition:.2s;
    }
    #ptHeaderToggle:hover{background:#f9f8ff;}
    #ptHeaderToggle svg{width:18px; height:18px; color:var(--pt-brand); transition:transform .2s;}
    body.sb-closed #ptHeaderToggle svg{ transform:rotate(180deg); }

    .pt-actions{display:flex; align-items:center; gap:12px;}
    .pt-cluster{display:flex; align-items:center; gap:6px; padding:4px; border:1px solid var(--pt-sep); background:#fff; border-radius:14px; box-shadow:0 1px 0 rgba(0,0,0,.02);}
    .pt-ctl{display:inline-grid; place-items:center; width:38px; height:38px; border-radius:10px; border:1px solid transparent; transition:.15s; background:#fafbff;}
    .pt-ctl:hover{border-color:#ffd7c6; background:#fff;}
    .pt-ctl svg{width:20px; height:20px; color:var(--pt-brand);}
    .pt-sep-v{width:1px; height:24px; background:var(--pt-sep); margin:0 2px; border-radius:1px;}
    .pt-account{display:flex; align-items:center; gap:10px;}
    .pt-avatar{width:38px; height:38px; border-radius:50%; overflow:hidden; border:2px solid var(--pt-sep); box-shadow:0 1px 0 rgba(0,0,0,.02);}
    .pt-avatar img{width:100%; height:100%; object-fit:cover; display:block;}

    /* ===== Conținut ===== */
    .crancy-body-area{ margin-left:0 !important; padding-left:0 !important; }
    #ptPage{ margin-left: var(--sbw); transition: margin-left .22s ease; }

    /* elimină gutters moștenite */
    #ptPage, #ptPage .crancy-main, #ptPage .crancy-content, #ptPage .crancy-header__inner,
    #ptPage .crancy-header__middle, #ptPage .crancy-main__content, #ptPage .crancy-wrapper{
      padding-left:0 !important; margin-left:0 !important; border-left:0 !important; box-shadow:none !important;
    }
    #ptPage .container, #ptPage .container-fluid{
      max-width:100% !important; padding-left:0 !important; padding-right:0 !important; margin-left:0 !important;
    }
    #ptPage .row{ margin-left:0 !important; }
    .pt-normalize > *:first-child{ margin-top:0 !important; }

    @media (max-width: 992px){
      body{ --sbw: 0px; }
    }

    html, body { overflow-x:hidden; }

    /* starea închis */
    body.sb-closed .crancy-smenu{ width:0 !important; border-right:0 !important; box-shadow:none !important; pointer-events:none !important; }
    body.sb-closed .pt-header{ margin-left:0 !important; width:100% !important; }
    body.sb-closed #ptPage{ margin-left:0 !important; }

    body.sb-closed #ptPage .container,
    body.sb-closed #ptPage .container-fluid,
    body.sb-closed #ptPage .crancy-main,
    body.sb-closed #ptPage .crancy-content,
    body.sb-closed #ptPage .crancy-wrapper{
      padding-left:0 !important; margin-left:0 !important; border-left:0 !important;
    }

    /* fallback anti-gutter */
    body.sb-closed #ptPage, body.sb-closed #ptPage .crancy-header__inner, body.sb-closed #ptPage .crancy-header__middle{
      padding-left:0 !important; margin-left:0 !important; border-left:0 !important;
    }
    body.sb-closed #ptPage > *, body.sb-closed #ptPage > * > *{
      padding-left:0 !important; margin-left:0 !important; border-left:0 !important;
    }
  </style>
</head>

<body id="crancy-dark-light">
<div class="crancy-body-area">

  <!-- ===== Sidebar ===== -->
  <div class="crancy-smenu" id="CrancyMenu">
    <div class="admin-menu">
      <div class="logo crancy-sidebar-padding pd-right-0">
        <a class="crancy-logo" href="{{ route('admin.dashboard') }}">
          <img src="{{ asset($general_setting->secondary_logo) }}" alt="logo" style="height:38px;">
        </a>
      </div>
      @include('admin.sidebar')
    </div>
  </div>

  <!-- ===== Header ===== -->
  <header class="pt-header">
    <div class="container">
      <div class="pt-headbar">
        <div class="pt-left">
          <button id="ptHeaderToggle" type="button" aria-label="Toggle sidebar" aria-pressed="false" title="Toggle sidebar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 6l-6 6 6 6"/>
            </svg>
          </button>
          <div class="pt-title">@yield('body-header')</div>
        </div>

        <div class="pt-actions">
          <div class="pt-cluster">
            <a target="_blank" class="pt-ctl" href="{{ route('home') }}" title="Open site">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"></path>
              </svg>
            </a>
            <span class="pt-sep-v"></span>
            <a class="pt-ctl" href="{{ route('admin.contact-message') }}" title="Messages">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V6a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
              </svg>
            </a>
            <span class="pt-sep-v"></span>
            <a class="pt-ctl" href="{{ route('admin.general-setting') }}" title="Settings">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.33 1.82l.03.06a2 2 0 1 1-3.4 0l.03-.06A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82-.33l-.06.03a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15 1.65 1.65 0 0 0 4 14"/>
              </svg>
            </a>
          </div>

          @php $auth_admin = Auth::guard('admin')->user(); @endphp
          <div class="pt-account">
            <a href="{{ route('admin.edit-profile') }}" class="pt-avatar">
              @if ($auth_admin?->image)
                <img src="{{ asset($auth_admin?->image) }}" alt="#">
              @else
                <img src="{{ asset($general_setting->default_avatar) }}" alt="#">
              @endif
            </a>

            <div class="crancy-dropdown crancy-dropdown--acount">
              <div class="crancy-dropdown__hover--inner">
                <ul class="crancy-dmenu">
                  <li>
                    <a href="{{ route('admin.edit-profile') }}">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12.1202 12.78C12.0502 12.77 11.9602 12.77 11.8802 12.78C10.1202 12.72 8.72021 11.28 8.72021 9.50998C8.72021 7.69998 10.1802 6.22998 12.0002 6.22998C13.8102 6.22998 15.2802 7.69998 15.2802 9.50998C15.2702 11.28 13.8802 12.72 12.1202 12.78Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.7398 19.3801C16.9598 21.0101 14.5998 22.0001 11.9998 22.0001C9.39977 22.0001 7.03977 21.0101 5.25977 19.3801C5.35977 18.4401 5.95977 17.5201 7.02977 16.8001C9.76977 14.9801 14.2498 14.9801 16.9698 16.8001C18.0398 17.5201 18.6398 18.4401 18.7398 19.3801Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      {{ __('translate.My Profile') }}
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15 10L13.7071 11.2929C13.3166 11.6834 13.3166 12.3166 13.7071 12.7071L15 14M14 12L22 12M6 20C3.79086 20 2 18.2091 2 16V8C2 5.79086 3.79086 4 6 4M6 20C8.20914 20 10 18.2091 10 16V8C10 5.79086 8.20914 4 6 4M6 20H14C16.2091 20 18 18.2091 18 16M6 4H14C16.2091 4 18 5.79086 18 8" stroke-width="1.5" stroke-linecap="round"/>
                      </svg>
                      {{ __('translate.Logout') }}
                    </a>
                    <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">@csrf</form>
                  </li>
                </ul>
              </div>
            </div>
          </div><!-- /.pt-account -->
        </div><!-- /.pt-actions -->
      </div><!-- /.pt-headbar -->
    </div>
  </header>

  <!-- ===== Conținut ===== -->
  <main id="ptPage" class="pt-normalize">
    @yield('body-content')
  </main>

</div>

<!-- JS -->
<script src="{{ asset('global/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('global/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('global/datatable/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-migrate.js') }}"></script>
<script src="{{ asset('backend/js/popper.min.js') }}"></script>
<script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('backend/js/nice-select.min.js') }}"></script>
<script src="{{ asset('backend/js/main.js') }}"></script>
<script src="{{ asset('global/toastr/toastr.min.js') }}"></script>

<script>
(function($){
  "use strict";

  const body = document.body;
  const btn  = document.getElementById('ptHeaderToggle');

  // Curăță stări/clase vechi (o singură dată la load)
  (function purgeLegacy(){
    ['sidebar-close','sidebar-collapsed','menu-close','menu-collapsed','crancy-close','crancy-menu-close','admin-menu-close']
      .forEach(c => body.classList.remove(c));
    const sb = document.getElementById('CrancyMenu');
    if (sb){
      sb.style.removeProperty('width');
      sb.style.removeProperty('transform');
      sb.style.removeProperty('display');
    }
  })();

  // Restaurează ultima stare salvată
  try{
    const savedClosed = localStorage.getItem('sb-closed') === '1';
    body.classList.toggle('sb-closed', savedClosed);
    if (btn) btn.setAttribute('aria-pressed', savedClosed ? 'true' : 'false');
  }catch(e){}

  function toggleSidebar(){
    body.classList.toggle('sb-closed');
    const closed = body.classList.contains('sb-closed');
    try{ localStorage.setItem('sb-closed', closed ? '1' : '0'); }catch(e){}
    if (btn) btn.setAttribute('aria-pressed', closed ? 'true' : 'false');
  }

  if (btn){
    btn.addEventListener('click', toggleSidebar);
    btn.addEventListener('keydown', (ev)=>{ if(ev.key==='Enter' || ev.key===' '){ ev.preventDefault(); toggleSidebar(); }});
  }

  /* Notificări + DataTables */
  $(document).ready(function () {
    const session_notify_message = @json(Session::get('message'));
    if (session_notify_message != null) {
      const session_notify_type = @json(Session::get('alert-type', 'info'));
      switch (session_notify_type) {
        case 'info': toastr.info(session_notify_message); break;
        case 'success': toastr.success(session_notify_message); break;
        case 'warning': toastr.warning(session_notify_message); break;
        case 'error': toastr.error(session_notify_message); break;
      }
    }
    const validation_errors = @json($errors->all());
    if (validation_errors.length > 0) validation_errors.forEach(e => toastr.error(e));

    const session_success = `{{ Session::get('success') }}`;
    const session_error   = `{{ Session::get('error') }}`;
    if (session_success) toastr.success(session_success);
    if (session_error)   toastr.error(session_error);

    $('#dataTable').DataTable();
  });
})(jQuery);
</script>

@stack('js_section')
</body>
</html>
