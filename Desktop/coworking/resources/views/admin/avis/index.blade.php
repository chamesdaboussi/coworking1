@extends('layouts.admin')
@section('title', 'Avis & Commentaires')
@section('page-title', 'Avis & Commentaires')

@section('content')
<div class="fade-in">

{{-- ── Stats row ── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
    <div class="stat-card" style="text-align:center;">
        <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Total avis</div>
        <div style="font-size:26px;font-weight:800;color:var(--accent);">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card" style="text-align:center;">
        <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Note moyenne</div>
        <div style="font-size:26px;font-weight:800;color:#f59e0b;">
            {{ $stats['avg'] > 0 ? $stats['avg'] : '—' }}
            @if($stats['avg'] > 0)<span style="font-size:16px;">★</span>@endif
        </div>
    </div>
    <div class="stat-card" style="text-align:center;">
        <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">5 étoiles ★★★★★</div>
        <div style="font-size:26px;font-weight:800;color:#16a34a;">{{ $stats['five'] }}</div>
    </div>
    <div class="stat-card" style="text-align:center;">
        <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">1–2 étoiles ★★</div>
        <div style="font-size:26px;font-weight:800;color:#dc2626;">{{ $stats['one_two'] }}</div>
    </div>
</div>

{{-- ── Filters ── --}}
<div class="card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('admin.avis') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:2;min-width:200px;">
            <label class="form-label">Rechercher</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nom, email, espace, commentaire...">
        </div>
        <div style="flex:1;min-width:160px;">
            <label class="form-label">Espace</label>
            <select name="espace" class="form-input">
                <option value="">Tous les espaces</option>
                @foreach($espaces as $e)
                <option value="{{ $e->id_espace }}" {{ request('espace') == $e->id_espace ? 'selected' : '' }}>{{ $e->nom }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:130px;">
            <label class="form-label">Note</label>
            <select name="note" class="form-input">
                <option value="">Toutes</option>
                @for($n = 5; $n >= 1; $n--)
                <option value="{{ $n }}" {{ request('note') == $n ? 'selected' : '' }}>{{ $n }} étoile{{ $n > 1 ? 's' : '' }}</option>
                @endfor
            </select>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="{{ route('admin.avis') }}" class="btn btn-outline">Réinitialiser</a>
        </div>
    </form>
</div>

{{-- ── Avis list ── --}}
<div style="display:grid;gap:14px;">

    @forelse($avis as $av)
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="display:flex;align-items:stretch;gap:0;">

            {{-- Note bar (left accent) --}}
            <div style="width:6px;flex-shrink:0;background:{{ $av->note >= 4 ? '#16a34a' : ($av->note == 3 ? '#f59e0b' : '#dc2626') }};border-radius:14px 0 0 14px;"></div>

            <div style="flex:1;padding:16px 18px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;">

                    {{-- Left: user + espace info --}}
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#ff8247,#4cbcbe);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:15px;flex-shrink:0;">
                            {{ strtoupper(substr($av->utilisateur->prenom ?? $av->utilisateur->nom, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;">{{ $av->utilisateur->full_name }}</div>
                            <div style="font-size:12px;color:var(--text-muted);">{{ $av->utilisateur->email }}</div>
                        </div>
                    </div>

                    {{-- Right: espace + note + date --}}
                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                        <div style="text-align:right;">
                            <div style="font-size:12px;font-weight:700;color:var(--text);">{{ $av->espace->nom }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $av->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                        {{-- Stars --}}
                        <div style="text-align:center;">
                            <div style="font-size:20px;letter-spacing:2px;color:#f59e0b;line-height:1;">
                                @for($i=1;$i<=5;$i++){{ $av->note >= $i ? '★' : '☆' }}@endfor
                            </div>
                            <div style="font-size:11px;font-weight:700;color:var(--text-muted);margin-top:2px;">
                                {{ ['','Médiocre','Passable','Correct','Bien','Excellent'][$av->note] }}
                            </div>
                        </div>
                        {{-- Delete --}}
                        <form method="POST" action="{{ route('admin.avis.delete', $av->id_avis) }}"
                            onsubmit="return confirm('Supprimer cet avis définitivement ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Supprimer cet avis">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Comment body --}}
                @if($av->commentaire)
                <div style="margin-top:12px;padding:12px 14px;background:var(--surface3);border-radius:9px;border-left:3px solid {{ $av->note >= 4 ? '#16a34a' : ($av->note == 3 ? '#f59e0b' : '#dc2626') }};">
                    <p style="font-size:13px;color:var(--text);margin:0;line-height:1.6;">{{ $av->commentaire }}</p>
                </div>
                @else
                <div style="margin-top:10px;">
                    <span style="font-size:12px;color:var(--text-muted);font-style:italic;">Aucun commentaire écrit.</span>
                </div>
                @endif

                {{-- Reservation link --}}
                @if($av->reservation)
                <div style="margin-top:10px;display:flex;align-items:center;gap:6px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span style="font-size:11px;color:var(--text-muted);">Réservation :</span>
                    <a href="{{ route('reservations.show', $av->reservation->id_reservation) }}"
                        style="font-size:11px;font-family:monospace;color:var(--accent);font-weight:700;text-decoration:none;">
                        {{ $av->reservation->code_confirmation }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    @empty
    <div style="text-align:center;padding:60px;color:var(--text-muted);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin:0 auto 16px;display:block;opacity:0.3;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <p style="font-size:15px;margin-bottom:6px;">Aucun avis trouvé</p>
        <p style="font-size:13px;">Les avis apparaîtront ici dès que des utilisateurs noteront leurs réservations.</p>
    </div>
    @endforelse

</div>

{{-- Pagination --}}
@if($avis->hasPages())
<div style="margin-top:20px;display:flex;justify-content:center;">
    {{ $avis->links() }}
</div>
@endif

</div>
@endsection
