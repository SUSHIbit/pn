{{-- resources/views/pagination/simple.blade.php --}}
@if ($paginator->hasPages())
    <nav class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="opacity: 0.5; cursor: not-allowed;">
                <i class="fas fa-chevron-left"></i> Previous
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span>{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span style="opacity: 0.5; cursor: not-allowed;">
                Next <i class="fas fa-chevron-right"></i>
            </span>
        @endif
    </nav>
@endif