@extends('layouts.user')
@section('title', 'Mes Réservations')
@section('page-title', 'Mes réservations')

@section('content')
<div class="fade-in">

    <!-- Filters -->
    <div class="card" style="margin-bottom: 20px;">
        <form method="GET" action="{{ route('reservations.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Code, espace...">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-input">
                    <option value="">Tous</option>
                    <option value="pending"   {{ request('statut') === 'pending'   ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ request('statut') === 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                    <option value="expired"   {{ request('statut') === 'expired'   ? 'selected' : '' }}>Expirée</option>
                    <option value="cancelled" {{ request('statut') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label">Du</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="form-input">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label">Au</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="form-input">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('reservations.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </form>
    </div>

    <!-- Actions bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <div style="font-size: 14px; color: var(--text-muted);">
            <strong style="color: var(--text);">{{ $reservations->total() }}</strong> réservation(s) trouvée(s)
        </div>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary btn-sm">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouvelle réservation
        </a>
    </div>

    <!-- Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Espace</th>
                        <th>Période</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Paiement</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $r)
                    <tr>
                        <td>
                            <span style="font-family: monospace; font-size: 12px; color: var(--accent); background: rgba(79,126,247,0.08); padding: 3px 8px; border-radius: 6px; white-space: nowrap;">{{ $r->code_confirmation }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 500; font-size: 14px;">{{ $r->espace->nom }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $r->espace->type_libelle }}</div>
                        </td>
                        <td style="white-space: nowrap; font-size: 13px; color: var(--text-muted);">
                            {{ $r->period_label }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $r->type_reservation === 'daily' ? 'blue' : 'purple' }}">
                                {{ $r->type_reservation === 'daily' ? 'Journalier' : 'Horaire' }}
                            </span>
                        </td>
                        <td style="font-weight: 700; white-space: nowrap;">{{ number_format($r->prix_final, 2) }} DT</td>
                        <td><span class="badge badge-{{ $r->status_badge['color'] }}">{{ $r->status_badge['label'] }}</span></td>
                        <td>
                            @if($r->latestPaiement)
                                <span class="badge badge-{{ $r->latestPaiement->statut === 'paid' ? 'green' : ($r->latestPaiement->statut === 'pending' ? 'yellow' : 'red') }}">
                                    {{ ['paid' => 'Payé', 'pending' => 'En attente', 'failed' => 'Échoué'][$r->latestPaiement->statut] ?? $r->latestPaiement->statut }}
                                </span>
                            @else
                                <span class="badge badge-gray">Non payé</span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('reservations.show', $r->id_reservation) }}" class="btn btn-outline btn-sm">Voir</a>
                            @if($r->isPending())
                                <a href="{{ route('reservations.payment', $r->id_reservation) }}" class="btn btn-primary btn-sm" style="margin-left: 4px;">Payer</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 12px; display: block; opacity: 0.3;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <p style="font-size: 15px; margin-bottom: 12px;">Aucune réservation trouvée</p>
                            <a href="{{ route('reservations.create') }}" class="btn btn-primary btn-sm">Créer ma première réservation</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reservations->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--border);">
            {{ $reservations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
