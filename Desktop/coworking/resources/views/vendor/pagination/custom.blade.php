@if ($paginator->hasPages())
<nav style="display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap;">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span style="padding: 8px 14px; border-radius: 8px; font-size: 13px; color: var(--text-muted); background: var(--surface3); border: 1px solid var(--border); cursor: not-allowed; opacity: 0.5;">← Précédent</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; font-size: 13px; color: var(--text); background: var(--surface3); border: 1px solid var(--border); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--accent)'; this.style.color='var(--accent-light)';" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text)';">← Précédent</a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding: 8px 10px; color: var(--text-muted); font-size: 13px;">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 700; color: white; background: var(--accent); border: 1px solid var(--accent);">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding: 8px 14px; border-radius: 8px; font-size: 13px; color: var(--text); background: var(--surface3); border: 1px solid var(--border); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--accent)'; this.style.color='var(--accent-light)';" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text)';">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; font-size: 13px; color: var(--text); background: var(--surface3); border: 1px solid var(--border); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--accent)'; this.style.color='var(--accent-light)';" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text)';">Suivant →</a>
    @else
        <span style="padding: 8px 14px; border-radius: 8px; font-size: 13px; color: var(--text-muted); background: var(--surface3); border: 1px solid var(--border); cursor: not-allowed; opacity: 0.5;">Suivant →</span>
    @endif
</nav>
@endif
