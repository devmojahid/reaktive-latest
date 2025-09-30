@if ($items->hasPages())
    <div class="tg-pagenation-wrap">
        <nav>
            <ul class="nav-links pagination">
                <li>
                    @if ($items->onFirstPage())
                        <span class="p-btn next page-numbers disabled">
                            Previous Page
                        </span>
                    @else
                        <a class="p-btn next page-numbers" href="{{ $items->previousPageUrl() }}">
                            Previous Page
                        </a>
                    @endif
                </li>

                @php
                    $start = max($items->currentPage() - 2, 1);
                    $end = min($start + 4, $items->lastPage());
                    $start = max(min($start, $items->lastPage() - 4), 1);
                @endphp

                @if ($start > 1)
                    <li>
                        <a class="page-numbers" href="{{ $items->url(1) }}">1</a>
                        @if ($start > 2)
                            <span class="page-numbers dots">...</span>
                        @endif
                    </li>
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    <li>
                        @if ($i == $items->currentPage())
                            <span aria-current="page" class="page-numbers active">{{ $i }}</span>
                        @else
                            <a class="page-numbers" href="{{ $items->url($i) }}">{{ $i }}</a>
                        @endif
                    </li>
                @endfor

                @if ($end < $items->lastPage())
                    <li>
                        @if ($end < $items->lastPage() - 1)
                            <span class="page-numbers dots">...</span>
                        @endif
                        <a class="page-numbers"
                            href="{{ $items->url($items->lastPage()) }}">{{ $items->lastPage() }}</a>
                    </li>
                @endif

                <li>
                    @if ($items->hasMorePages())
                        <a class="p-btn next page-numbers" href="{{ $items->nextPageUrl() }}">
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
