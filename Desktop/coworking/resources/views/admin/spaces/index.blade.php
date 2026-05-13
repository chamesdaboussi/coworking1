@extends('layouts.admin')
@section('title', 'Gestion des espaces')
@section('page-title', 'Gestion des espaces')

@section('content')
<div class="fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <p style="color: var(--text-muted); font-size: 14px;">{{ $espaces->total() }} espace(s) enregistré(s)</p>
        <a href="{{ route('admin.spaces.create') }}" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Ajouter un espace
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
        @forelse($espaces as $espace)
        <div class="card" style="padding: 0; overflow: hidden;">
            @if($espace->image)
                <img src="{{ route('storage.public', ['path' => $espace->image]) }}" alt="{{ $espace->nom }}" style="width: 100%; height: 160px; object-fit: cover;">
            @else
                <div style="width: 100%; height: 160px; background: linear-gradient(135deg, rgba(79,126,247,0.3), rgba(124,95,207,0.2)); display: flex; align-items: center; justify-content: center;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                </div>
            @endif
            <div style="padding: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <h3 style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700;">{{ $espace->nom }}</h3>
                    <span class="badge badge-{{ $espace->disponible ? 'green' : 'red' }}">{{ $espace->disponible ? 'Disponible' : 'Indisponible' }}</span>
                </div>
                <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">{{ $espace->adresse }}</div>
                <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 10px;">{{ $espace->type_libelle }} · {{ $espace->reservations_count }} réservation(s)</div>
                <div style="display: flex; gap: 8px; font-size: 13px; margin-bottom: 14px;">
                    <span style="color: #10b981; font-weight: 600;">{{ number_format($espace->prix_jour, 2) }} DT/jour</span>
                    <span style="color: var(--text-muted);">·</span>
                    <span style="color: var(--text-muted);">{{ number_format($espace->prix_heure, 2) }} DT/h</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('admin.spaces.edit', $espace->id_espace) }}" class="btn btn-outline btn-sm" style="flex: 1; justify-content: center;">Modifier</a>
                    <form method="POST" action="{{ route('admin.spaces.delete', $espace->id_espace) }}" onsubmit="return confirm('Supprimer cet espace ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 48px; color: var(--text-muted);">
            <p>Aucun espace. <a href="{{ route('admin.spaces.create') }}" style="color: var(--accent-light);">Créer le premier.</a></p>
        </div>
        @endforelse
    </div>
    @if($espaces->hasPages())
    <div style="margin-top: 20px;">{{ $espaces->links() }}</div>
    @endif
</div>
@endsection
