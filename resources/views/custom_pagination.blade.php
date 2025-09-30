<ul>
    @if ($paginator->onFirstPage())
    @else
        <li>
            <a href="{{ $paginator->previousPageUrl() }}" class="p-btn mr-25" type="button">
                {{ __('translate.Previous') }}
            </a>
        </li>
    @endif

    @foreach ($elements as $element)
        @if (!is_array($element))
            <li><a class="p-btn" href="javascript: void(0);">...</a></li>
        @else
            @if (count($element) < 2)
            @else
                @foreach ($element as $key => $el)
                    @if ($key == $paginator->currentPage())
                        <li><a class="p-btn active" href="javascript::void()">{{ $key }}</a></li>
                    @else
                        <li><a class="p-btn" href="{{ $el }}">{{ $key }}</a></li>
                    @endif
                @endforeach
            @endif
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <li>
            <a href="{{ $paginator->nextPageUrl() }}" class="p-btn" type="button">
                {{ __('translate.Next') }}
            </a>
        </li>
    @else
    @endif
</ul>
