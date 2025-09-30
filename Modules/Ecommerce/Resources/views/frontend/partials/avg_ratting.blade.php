@php
    $avg = $rating ?? 0; // fallback to 0 if null
    $avg = round($avg * 2) / 2; // round to nearest 0.5
@endphp

<ul class="tg-ratting-star d-flex list-unstyled">
    @for ($i = 1; $i <= 5; $i++)
        @if ($avg >= $i)
            <i class="fa-sharp fa-solid fa-star active "></i> <!-- Full star -->
        @elseif($avg == $i - 0.5)
            <i class="fa-sharp fa-solid fa-star-half-stroke active"></i> <!-- Half star -->
        @else
            <i class="fa-sharp fa-regular fa-star"></i> <!-- Empty star -->
        @endif
    @endfor
</ul>
