@if ($paginator->hasPages())
<nav class="app-pagination" role="navigation" aria-label="Pagination Navigation">
    <div class="app-pagination-summary">
        @if ($paginator->firstItem())
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        @else
            Showing 0 results
        @endif
    </div>
    <div class="app-pagination-links">
        @if ($paginator->onFirstPage())
            <span class="app-page disabled" aria-disabled="true" aria-label="Previous page">‹</span>
        @else
            <a class="app-page" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">‹</a>
        @endif
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="app-page dots">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="app-page active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="app-page" href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <a class="app-page" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">›</a>
        @else
            <span class="app-page disabled" aria-disabled="true" aria-label="Next page">›</span>
        @endif
    </div>
</nav>
@endif
