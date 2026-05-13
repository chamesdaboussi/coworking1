@extends('layouts.admin')
@section('title', 'Réservations Admin')
@section('page-title', 'Liste des réservations')
@section('content')
<div class="fade-in">
    <div class="card" style="margin-bottom:16px;">
        <form method="GET" action="{{ route('admin.reservations') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
            <div style="flex:2;min-width:220px;"><label class="form-label">Recherche</label><input class="form-input" name="search" value="{{ request('search') }}" placeholder="code, client, espace..."></div>
            <div><label class="form-label">Paiement</label><select class="form-input" name="payment"><option value="">Tous</option><option value="cash" @selected(request('payment')==='cash')>Cash</option><option value="stripe" @selected(request('payment')==='stripe')>En ligne</option></select></div>
            <div><label class="form-label">Statut</label><select class="form-input" name="statut"><option value="">Tous</option><option value="pending" @selected(request('statut')==='pending')>En attente</option><option value="confirmed" @selected(request('statut')==='confirmed')>Confirmée</option></select></div>
            <button class="btn btn-primary">Filtrer</button><a class="btn btn-outline" href="{{ route('admin.reservations') }}">Reset</a>
        </form>
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
        <table class="table">
            <thead><tr><th>Code</th><th>Client</th><th>Espace</th><th>Dates</th><th>Montant</th><th>Statut</th><th>Paiement</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($reservations as $r)
                <tr>
                    <td><span style="font-family:monospace;color:var(--accent);">{{ $r->code_confirmation }}</span></td>
                    <td><strong>{{ $r->utilisateur->full_name }}</strong><br><span style="color:var(--text-muted);font-size:12px;">{{ $r->utilisateur->email }}</span></td>
                    <td>{{ $r->espace->nom }}</td>
                    <td>{{ $r->period_label }}</td>
                    <td><strong>{{ number_format($r->prix_final, 2) }} DT</strong></td>
                    <td><span class="badge badge-{{ $r->status_badge['color'] }}">{{ $r->status_badge['label'] }}</span></td>
                    <td>
                        @if($r->latestPaiement)
                            @php
                                $methodeLabel = $r->latestPaiement->methode === 'stripe' ? 'Carte de crédit' : ($r->latestPaiement->methode === 'cash' ? 'Espèces' : ucfirst($r->latestPaiement->methode));
                                $statutLabel = $r->latestPaiement->statut === 'paid' ? 'Payé' : ($r->latestPaiement->statut === 'pending' ? 'En attente' : 'Échoué');
                            @endphp
                            <span class="badge badge-{{ $r->latestPaiement->statut === 'paid' ? 'green' : ($r->latestPaiement->statut === 'pending' ? 'yellow' : 'red') }}">{{ $methodeLabel }} — {{ $statutLabel }}</span>
                        @else <span class="badge badge-gray">Non payé</span>@endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('reservations.show', $r->id_reservation) }}" class="btn btn-outline btn-sm">Voir</a>
                        @if($r->latestPaiement && $r->latestPaiement->methode === 'cash' && $r->latestPaiement->statut === 'pending')
                        <form method="POST" action="{{ route('admin.reservations.validateCash', $r->id_reservation) }}" style="display:inline;">@csrf<button class="btn btn-success btn-sm" onclick="return confirm('Valider le paiement cash ?')">Valider cash</button></form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">Aucune réservation.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($reservations->hasPages())<div style="padding:14px;border-top:1px solid var(--border);">{{ $reservations->links() }}</div>@endif
    </div>
</div>
@endsection
