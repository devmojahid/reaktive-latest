<div class="{{ $ratingClass ?? 'tg-listing-card-review mb-10' }}">
    @foreach (range(1, 5) as $star)
        <i class="fa-sharp fa-solid fa-star @if ($avgRating >= $star) active @endif"></i>
    @endforeach
    <span class="tg-listing-rating-percent">
        (
        {{ __($ratingCount) }}
        {{ __($ratingCount > 1 ? __('translate.Reviews') : __('translate.Review')) }}
        )
    </span>
</div>

@push('style_section')
    <style>
        .tg-listing-card-review i {
            color: #9c9c9c;
        }

        .tg-listing-card-review .active {
            color: var(--tg-common-yellow);
        }
    </style>
@endpush
