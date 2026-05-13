@if ($paginator->hasPages())
<nav style="display: flex; align-items: center; justify-content: center; gap: 8px;">
    @if ($paginator->onFirstPage())
        <span style="padding: 8px 14px; border-radius: 8px; font-size: 13px; color: var(--text-muted); background: var(--surface3); border: 1px solid var(--border); opacity: 0.5;">← Précédent</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; font-size: 13px; color: var(--text); background: var(--surface3); border: 1px solid var(--border); text-decoration: none;">← Précédent</a>
    @endif

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; font-size: 13px; color: var(--text); background: var(--surface3); border: 1px solid var(--border); text-decoration: none;">Suivant →</a>
    @else
        <span style="padding: 8px 14px; border-radius: 8px; font-size: 13px; color: var(--text-muted); background: var(--surface3); border: 1px solid var(--border); opacity: 0.5;">Suivant →</span>
    @endif
</nav>
@endif
