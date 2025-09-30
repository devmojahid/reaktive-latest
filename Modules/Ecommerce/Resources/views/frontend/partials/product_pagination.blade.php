@if ($products->hasPages())
    <div class="tg-pagenation-wrap text-center pt-35 mb-30">
        <nav class="navigation pagination center" aria-label="Posts">
            <ul class="nav-links">
                <li>
                    @if ($products->onFirstPage())
                        <span class="p-btn next page-numbers disabled">
                            Previous Page
                        </span>
                    @else
                        <a class="p-btn next page-numbers" href="{{ $products->previousPageUrl() }}">
                            Previous Page
                        </a>
                    @endif
                </li>

                @php
                    $start = max($products->currentPage() - 2, 1);
                    $end = min($start + 4, $products->lastPage());
                    $start = max(min($start, $products->lastPage() - 4), 1);
                @endphp

                @if ($start > 1)
                    <li>
                        <a class="page-numbers" href="{{ $products->url(1) }}">1</a>
                        @if ($start > 2)
                            <span class="page-numbers dots">...</span>
                        @endif
                    </li>
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    <li>
                        @if ($i == $products->currentPage())
                            <span aria-current="page" class="page-numbers active">{{ $i }}</span>
                        @else
                            <a class="page-numbers" href="{{ $products->url($i) }}">{{ $i }}</a>
                        @endif
                    </li>
                @endfor

                @if ($end < $products->lastPage())
                    <li>
                        @if ($end < $products->lastPage() - 1)
                            <span class="page-numbers dots">...</span>
                        @endif
                        <a class="page-numbers"
                            href="{{ $products->url($products->lastPage()) }}">{{ $products->lastPage() }}</a>
                    </li>
                @endif

                <li>
                    @if ($products->hasMorePages())
                        <a class="p-btn next page-numbers" href="{{ $products->nextPageUrl() }}">
                            Next Page
                        </a>
                    @else
                        <span class="p-btn next page-numbers disabled">
                            Next Page
                        </span>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
@endif
