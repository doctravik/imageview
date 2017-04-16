@if ($paginator->hasPages())
    <nav class="pagination is-centered">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <a class="pagination-previous" rel="prev">Previous</a>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-previous" rel="prev">Previous</a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-next">Next page</a>
        @else
            <a rel="next" class="pagination-next">Next page</a>
        @endif

        <ul class="pagination-list">
        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li><span class="pagination-ellipsis">&hellip;</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><a class="pagination-link is-current">{{ $page }}</a></li>
                    @else
                        <li><a class="pagination-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
        </ul>
    </nav>
@endif
