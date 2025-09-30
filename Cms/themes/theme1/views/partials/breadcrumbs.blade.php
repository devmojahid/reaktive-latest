<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item {{ !$breadcrumb['url'] ? 'active' : '' }}">
                        @if($breadcrumb['url'])
                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                        @else
                            {{ $breadcrumb['label'] }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>
</div>

@push('styles')
<style>
    .breadcrumb-section {
        background-color: var(--light-color);
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }

    .breadcrumb {
        margin-bottom: 0;
        background: transparent;
        padding: 0;
    }

    .breadcrumb-item {
        font-size: 0.875rem;
    }

    .breadcrumb-item a {
        color: var(--primary-color);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .breadcrumb-item a:hover {
        color: var(--secondary-color);
    }

    .breadcrumb-item.active {
        color: var(--dark-color);
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: var(--dark-color);
    }
</style>
@endpush 