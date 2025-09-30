@php
    use Illuminate\Support\Facades\DB;

    // --- Data ---
    $destinations = destinations();

    // Service types (pentru „pill-urile” de sub bară)
    $serviceTypes = serviceTypeTab();
    $typeCounts = [];
    if ($serviceTypes->count()) {
        $ids = $serviceTypes->pluck('id')->all();
        $typeCounts = DB::table('services')
            ->select('service_type_id', DB::raw('COUNT(*) as c'))
            ->whereIn('service_type_id', $ids)
            ->where('status', 1)
            ->groupBy('service_type_id')
            ->pluck('c', 'service_type_id')
            ->toArray();
    }

    // Limbi pentru dropdown-ul din search bar
    $languageCounts = DB::table('services')
        ->where('status', 1)
        ->pluck('languages')
        ->filter()
        ->flatMap(fn($json) => is_array($a = json_decode($json, true)) ? $a : [])
        ->map(fn($v) => trim((string) $v))
        ->filter()
        ->reduce(function ($acc, $l) {
            $acc[$l] = ($acc[$l] ?? 0) + 1;
            return $acc;
        }, []);
    ksort($languageCounts);

    // i18n helper (fallback EN)
    $t = fn($key, $en) => __($key) === $key ? $en : __($key);

    // === FEATURE FLAG pentru PILL BAR ===
    // setează la true dacă vrei să afișezi bara cu „pills”
    $showPills = false;
@endphp

@push('style_section')
    <link rel="stylesheet" href="{{ asset('global/datetimerange/daterangepicker.css') }}">
    <style>
        /* ===== SEARCH BAR ===== */
        .search-shell{
            background:#fff;border:1px solid #E6E6E6;border-radius:18px;
            padding:10px 14px;
            box-shadow:0 2px 0 rgba(0,0,0,.02);
        }
        .search-grid{
            display:grid;
            grid-template-columns:minmax(0,1fr) minmax(0,1fr) minmax(0,1fr) 190px;
            align-items:center;gap:8px;
        }
        @media (max-width:1199.98px){ .search-grid{ grid-template-columns:minmax(0,1fr) minmax(0,1fr); } }
        @media (max-width:575.98px){ .search-grid{ grid-template-columns:1fr; } }

        .field{position:relative;display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:12px;}
        .field + .field{border-left:1px solid #E6E6E6;}
        @media (max-width:1199.98px){ .field + .field{border-left:0;} }

        .field-icon{opacity:.85;display:inline-flex;align-items:center;justify-content:center;width:18px;}
        .field-icon i{font-size:18px;line-height:18px;display:inline-block;}
        .field-icon svg{width:18px;height:18px;display:inline-block;}

        .field-placeholder{font-weight:600;color:#202124;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .field input{
            border:none;background:transparent;width:100%;height:46px;
            padding:0;                 /* aliniază ca la “Where?” */
            font-weight:600;color:#202124;
        }
        .field input::placeholder{
            color:#202124;             /* aceeași culoare ca “Where?” */
            opacity:1;                 /* Safari/iOS */
            font-weight:600;
        }

        .dropdown-list{
            position:absolute;top:100%;left:0;right:0;z-index:50;display:none;
            background:#fff;border:1px solid #E6E6E6;border-radius:12px;margin-top:8px;padding:6px;
            max-height:320px;overflow:auto;box-shadow:0 6px 24px rgba(0,0,0,.08);
        }
        .dropdown-list.open{display:block;}
        .dropdown-list li{
            padding:10px 12px;border-radius:10px;cursor:pointer;display:flex;align-items:center;gap:8px;
        }
        .dropdown-list li:hover{background:#F6F7F9;}
        .btn-search{
            width:100%;height:48px;border-radius:14px;background:#ff4200;color:#fff;font-weight:700;border:none;
        }
        .btn-search:hover{filter:brightness(.95);}

        /* ===== PILL CAROUSEL ===== */
        .pill-carousel{position:relative;margin-top:28px;margin-bottom:12px;}
        .pill-viewport{
            overflow-x:auto;overflow-y:hidden;
            -webkit-overflow-scrolling:touch;
            touch-action:pan-x;
            scroll-behavior:smooth;
            overscroll-behavior-x:contain;
        }
        .pill-track{display:flex;gap:14px;align-items:center;will-change:transform;padding:2px 6px;}
        .pill{
            flex:0 0 auto;border:1px solid #E6E6E6;border-radius:999px;background:#fff;
            padding:10px 16px;display:flex;align-items:center;gap:10px;
        }
        .pill b{font-weight:700;color:#1f1f1f;white-space:nowrap;}
        .pill small{color:#6b7280;line-height:1;display:block;white-space:nowrap;}

        .pill-nav{
            position:absolute;top:50%;transform:translateY(-50%);
            width:38px;height:38px;border-radius:999px;border:1px solid #e5e7eb;background:#fff;
            display:flex;align-items:center;justify-content:center;cursor:pointer;
            box-shadow:0 2px 10px rgba(0,0,0,.06);
            z-index:5;
        }
        .pill-nav.left{left:8px;}
        .pill-nav.right{right:8px;}
        /* dacă vrei să ascunzi săgețile pe mobile, de-comentează:
        @media (max-width:575.98px){ .pill-nav{display:none;} } */

        /* edge fades – nu blochează click-urile */
        .pill-carousel:before,.pill-carousel:after{
            content:"";position:absolute;top:0;bottom:0;width:24px;pointer-events:none;
            background:linear-gradient(to right, #fff, rgba(255,255,255,0)); z-index:2;
        }
        .pill-carousel:before{left:0;}
        .pill-carousel:after{right:0;transform:scaleX(-1);}

        /* === Bigger icons in pills === */
        .pill { 
          gap: 14px;                 /* un pic mai mult spațiu între icon și text */
          padding: 12px 18px;        /* ușor mai înalt/lat ca-n mock */
        }
        .pill > img,
        .pill > i,
        .pill svg {
          width: 28px;
          height: 28px;
        }
        /* pentru font icons (ex: Font Awesome) asigură aliniere perfectă */
        .pill > i {
          font-size: 28px;
          line-height: 1;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          min-width: 28px;           /* previne strângerea la flex */
        }
        /* pentru imagini încărcate din DB */
        .pill > img {
          object-fit: contain;
          display: block;
        }
    </style>

    <style>
        /* ===== DATERANGEPICKER — single, clean, theme-friendly ===== */
        .daterangepicker{
          border:1px solid #E6E6E6;
          border-radius:16px;
          box-shadow:0 12px 34px rgba(0,0,0,.10);
          overflow:hidden;
          font-family: var(--tg-ff-outfit, inherit);
        }
        .daterangepicker.single .drp-calendar{
          width:auto!important;
          padding:14px 16px 6px 16px;
        }
        .daterangepicker .calendar-table{ border:none; }
        .daterangepicker th,
        .daterangepicker td{
          font-size:14px;color:#353844;
          width:34px;height:32px;text-align:center;border-radius:10px;
        }
        .daterangepicker td.available:hover,
        .daterangepicker th.available:hover{ background:#F6F7F9; }
        .daterangepicker td.off{ color:#C2C6D0; opacity:.8; }
        .daterangepicker td.active,
        .daterangepicker td.active:hover{ background:#ff4200;color:#fff!important; }
        .daterangepicker .monthselect,
        .daterangepicker .yearselect{
          border:1px solid #E6E6E6;border-radius:10px;padding:6px 8px;height:auto;font-size:14px;
        }
        .daterangepicker .drp-buttons{
          padding:10px 14px;border-top:1px solid #F0F0F0;
        }
        .daterangepicker .drp-buttons .btn{
          border-radius:10px;padding:8px 14px;font-weight:600;
        }
        .daterangepicker .applyBtn{ background:#ff4200;border-color:#ff4200;color:#fff; }
        .daterangepicker .cancelBtn{ display:none!important; }
        .daterangepicker:before,.daterangepicker:after{ display:none; }
    </style>
@endpush


@if ($serviceTypes->count() > 0)
    <!-- SEARCH BAR (one line) -->
    <div class="tg-booking-form-area tg-booking-form-space pb-60">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="search-shell" x-data="heroSearch()">
                        <div class="search-grid">
                            <!-- Where -->
                            <div class="field">
                                <span class="field-icon">
                                    <svg width="18" height="18" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M12.33 6.707C12.33 11.232 6.555 15.111 6.555 15.111S.777 11.232.777 6.707a5.777 5.777 0 1111.553 0z" stroke="currentColor" stroke-width="1.2" />
                                        <circle cx="6.555" cy="6.707" r="2" stroke="currentColor" stroke-width="1.2" />
                                    </svg>
                                </span>
                                <div class="flex-1">
                                    <div class="field-placeholder" x-text="destination || '{{ $t('translate.Where?','Where?') }}'"></div>
                                </div>
                                <ul class="dropdown-list" x-ref="destList">
                                    @foreach ($destinations as $d)
                                        <li @click="selectDestination('{{ $d->id }}','{{ addslashes($d->name) }}')">
                                            <i class="fa-regular fa-location-dot"></i><span>{{ $d->name }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- When -->
                            <div class="field">
                                <span class="field-icon"><i class="fa-regular fa-calendar"></i></span>
                                <div class="flex-1">
                                    <input id="check_in_check_out_main"
                                           x-model="check_in_check_out"
                                           type="text"
                                           placeholder="{{ $t('translate.When?','When?') }}"
                                           autocomplete="off">
                                </div>
                            </div>

                            <!-- Languages (în search bar) -->
                            <div class="field">
                                <span class="field-icon"><i class="fa-regular fa-globe"></i></span>
                                <div class="flex-1">
                                    <div class="field-placeholder" x-text="languageLabel()"></div>
                                </div>
                                <ul class="dropdown-list" x-ref="langList">
                                    <li @click="selectLanguage('')">
                                        <span>{{ $t('Guide Language','All languages') }}</span>
                                        <small class="ms-auto">{{ array_sum($languageCounts) }}</small>
                                    </li>
                                    @foreach ($languageCounts as $lang => $cnt)
                                        <li @click="selectLanguage('{{ addslashes($lang) }}')">
                                            <span>{{ $lang }}</span><small class="ms-auto">{{ $cnt }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Search -->
                            <div>
                                <button class="btn-search" @click="submitForm()">
                                    {{ $t('translate.Search','Search') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PILL BAR (one line + infinite scroll) -->
                    @if ($showPills)
                    <div class="pill-carousel">
                        <button class="pill-nav left" type="button" aria-label="Prev" data-pill-prev>
                            <i class="fa-solid fa-angle-left"></i>
                        </button>
                        <div class="pill-viewport" data-pill-viewport>
                            <div class="pill-track" data-pill-track>
                                @foreach ($serviceTypes as $type)
                                    @php
                                        $count = (int)($typeCounts[$type->id] ?? 0);
                                        $href = route('front.tourbooking.services', ['service_type_id' => $type->id]);
                                    @endphp
                                    <a href="{{ $href }}" class="pill">
                                        @if($type->image)
                                            <img src="{{ asset($type->image) }}" alt="" width="22" height="22" style="object-fit:contain;">
                                        @elseif($type->icon)
                                            <i class="{{ $type->icon }}"></i>
                                        @else
                                            <i class="fa-regular fa-tag"></i>
                                        @endif
                                        <div>
                                            <b>{{ $type->name }}</b>
                                            <small>{{ $t('translate.Excursions','Excursions') }}: {{ $count }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <button class="pill-nav right" type="button" aria-label="Next" data-pill-next>
                            <i class="fa-solid fa-angle-right"></i>
                        </button>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endif

@push('js_section')
    <script src="{{ asset('global/datetimerange/moment.min.js') }}"></script>
    <script src="{{ asset('global/datetimerange/daterangepicker.js') }}"></script>

    <!-- Alpine -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // ========= HERO SEARCH (Alpine) =========
        function heroSearch(){
            return {
                destination: '', destination_id: '',
                check_in: '', check_out: '', check_in_check_out: '',
                language: '',

                init(){
                    // open dropdowns on click pe câmp
                    this.$el.querySelectorAll('.field').forEach(f => {
                        f.addEventListener('click', (e)=>{
                            const dest = f.querySelector('[x-ref="destList"]');
                            const lang = f.querySelector('[x-ref="langList"]');
                            if (dest) { dest.classList.toggle('open'); }
                            if (lang) { lang.classList.toggle('open'); }
                            e.stopPropagation();
                        });
                    });

                    document.addEventListener('click', ()=>{
                        this.$refs?.destList?.classList?.remove('open');
                        this.$refs?.langList?.classList?.remove('open');
                    });

                    // DatePicker – SINGLE DATE (fără range / fără checkout vizibil)
                    $('#check_in_check_out_main').daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        autoApply: true,
                        autoUpdateInput: true,
                        opens: 'left',
                        drops: 'down',
                        minDate: moment(),
                        locale: { format: 'MMM DD, YYYY' } // ce afișăm în input
                    });

                    // la selectare: setăm și checkOut = checkIn
                    $('#check_in_check_out_main').on('apply.daterangepicker', (ev, picker)=>{
                        const displayVal = picker.startDate.format('MMM DD, YYYY');
                        const isoVal     = picker.startDate.format('MM/DD/YYYY');

                        $('#check_in_check_out_main').val(displayVal);

                        this.check_in           = isoVal;
                        this.check_out          = isoVal;                 // << aceeași zi
                        this.check_in_check_out = `${isoVal} - ${isoVal}`; // compat cu vechiul format
                    });

                    // fallback (rare) dacă se dă cancel
                    $('#check_in_check_out_main').on('cancel.daterangepicker', ()=>{
                        $('#check_in_check_out_main').val('');
                        this.check_in=''; this.check_out=''; this.check_in_check_out='';
                    });
                },

                selectDestination(id, name){
                    this.destination_id = id;
                    this.destination = name;
                    this.$refs?.destList?.classList?.remove('open');
                },
                selectLanguage(lang){
                    this.language = lang || '';
                    this.$refs?.langList?.classList?.remove('open');
                },
                languageLabel(){
                    return this.language ? this.language : '{{ $t('translate.All languages','All languages') }}';
                },

                submitForm(){
                    const params = new URLSearchParams({
                        destination: this.destination,
                        destination_id: this.destination_id,
                        checkIn: this.check_in,
                        checkOut: this.check_out,
                        check_in_check_out: this.check_in_check_out,
                        language: this.language
                    });
                    window.location.href = `{{ route('front.tourbooking.services') }}?` + params.toString();
                }
            }
        }

        // ========= PILL CAROUSEL (infinite loop + drag + wheel + arrows) =========
        (function(){
            const viewport = document.querySelector('[data-pill-viewport]');
            const track    = document.querySelector('[data-pill-track]');
            if(!viewport || !track) return;

            // 1) Dublăm conținutul track-ului pentru efect „infinite”
            track.innerHTML = track.innerHTML + track.innerHTML;

            const halfWidth = () => track.scrollWidth / 2;

            const normalize = ()=>{
                const half = halfWidth();
                if (viewport.scrollLeft >= half) viewport.scrollLeft -= half;
                else if (viewport.scrollLeft <= 0) viewport.scrollLeft += half;
            };

            // 2) Drag cu Pointer Events pe viewport
            let isDown = false, startX = 0, startLeft = 0;
            viewport.addEventListener('pointerdown', (e)=>{
                isDown = true;
                startX = e.clientX;
                startLeft = viewport.scrollLeft;
                viewport.setPointerCapture(e.pointerId);
                viewport.style.cursor = 'grabbing';
                viewport.style.userSelect = 'none';
            });
            viewport.addEventListener('pointermove', (e)=>{
                if(!isDown) return;
                const dx = e.clientX - startX;
                viewport.scrollLeft = startLeft - dx;
                normalize();
            });
            ['pointerup','pointercancel','pointerleave'].forEach(ev=>{
                viewport.addEventListener(ev, ()=>{
                    isDown = false; viewport.style.cursor=''; viewport.style.userSelect='';
                });
            });

            // 3) Wheel → orizontal
            viewport.addEventListener('wheel', (e)=>{
                e.preventDefault();
                viewport.scrollLeft += (e.deltaY || e.deltaX);
                normalize();
            }, {passive:false});

            // 4) Arrows
            const STEP = 340;
            document.querySelector('[data-pill-prev]')?.addEventListener('click', ()=>{
                viewport.scrollBy({left:-STEP, behavior:'smooth'});
                setTimeout(normalize, 400);
            });
            document.querySelector('[data-pill-next]')?.addEventListener('click', ()=>{
                viewport.scrollBy({left: STEP, behavior:'smooth'});
                setTimeout(normalize, 400);
            });

            // 5) poziționare inițială
            viewport.scrollLeft = (track.scrollWidth / 2) / 2;

            // 6) pe resize, menținem poziția în jumătatea curentă
            window.addEventListener('resize', ()=> setTimeout(normalize, 200));
        })();
    </script>
@endpush
