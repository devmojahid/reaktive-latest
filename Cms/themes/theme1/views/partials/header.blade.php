@push('styles')
<style>
    .site-header {
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .navbar-brand img {
        max-height: 40px;
        width: auto;
    }

    .nav-item .nav-link {
        padding: 0.5rem 1rem;
        color: var(--dark-color);
        transition: color 0.3s ease;
    }

    .nav-item .nav-link:hover,
    .nav-item .nav-link.active {
        color: var(--primary-color);
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        color: var(--dark-color);
        transition: all 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: var(--light-color);
        color: var(--primary-color);
    }
</style>
@endpush
