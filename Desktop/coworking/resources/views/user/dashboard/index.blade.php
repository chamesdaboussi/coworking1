@extends('layouts.user')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="fade-in">
    <div style="background: linear-gradient(135deg, rgba(79,126,247,0.08), rgba(124,95,207,0.06)); border: 1px solid rgba(79,126,247,0.16); border-radius: 18px; padding: 24px; margin-bottom: 22px; display: flex; justify-content: space-between; gap: 18px; align-items: center;">
        <div>
            <h2 style="font-family: 'Poppins', sans-serif; font-size: 22px; font-weight: 750; margin-bottom: 5px;">Bonjour, {{ Auth::user()->prenom ?? Auth::user()->nom }}</h2>
            <p style="color: var(--text-muted); font-size: 13px;">Réservez un espace, choisissez paiement en ligne ou paiement cash, puis suivez le statut ici.</p>
        </div>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary">Nouvelle réservation</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 22px;">
        <div class="stat-card"><div class="form-label">Total réservations</div><div style="font-family:'Poppins'; font-size:28px; font-weight:800;">{{ $stats['total'] }}</div></div>
        <div class="stat-card"><div class="form-label">Actives</div><div style="font-family:'Poppins'; font-size:28px; font-weight:800; color:var(--success);">{{ $stats['active'] }}</div></div>
        <div class="stat-card"><div class="form-label">En attente</div><div style="font-family:'Poppins'; font-size:28px; font-weight:800; color:var(--warning);">{{ $stats['pending'] }}</div></div>
        <div class="stat-card"><div class="form-label">Total payé</div><div style="font-family:'Poppins'; font-size:24px; font-weight:800; color:var(--accent);">{{ number_format($stats['spent'], 2) }} DT</div></div>
    </div>

    <div class="card" style="padding:0; overflow:hidden;">
        <div style="padding:16px 18px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-family:'Poppins'; font-size:15px; font-weight:700;">Mes dernières réservations</h3>
            <a href="{{ route('reservations.index') }}" class="btn btn-outline btn-sm">Voir tout</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead><tr><th>Code</th><th>Espace</th><th>Dates</th><th>Montant</th><th>Statut</th><th>Paiement</th><th></th></tr></thead>
                <tbody>
                    @forelse($recentReservations as $r)
                    <tr>
                        <td><span style="font-family:monospace; color:var(--accent);">{{ $r->code_confirmation }}</span></td>
                        <td>{{ $r->espace->nom }}</td>
                        <td>{{ $r->period_label }}</td>
                        <td><strong>{{ number_format($r->prix_final, 2) }} DT</strong></td>
                        <td><span class="badge badge-{{ $r->status_badge['color'] }}">{{ $r->status_badge['label'] }}</span></td>
                        <td>
                            @if($r->latestPaiement)
                                @php $mLabel = $r->latestPaiement->methode === 'stripe' ? 'Carte de crédit' : ($r->latestPaiement->methode === 'cash' ? 'Espèces' : ucfirst($r->latestPaiement->methode)); @endphp
                                <span class="badge badge-{{ $r->latestPaiement->statut === 'paid' ? 'green' : ($r->latestPaiement->statut === 'pending' ? 'yellow' : 'red') }}">{{ $mLabel }} — {{ $r->latestPaiement->statut === 'paid' ? 'Payé' : ($r->latestPaiement->statut === 'pending' ? 'En attente' : 'Échoué') }}</span>
                            @else
                                <span class="badge badge-gray">Non payé</span>
                            @endif
                        </td>
                        <td><a href="{{ route('reservations.show', $r->id_reservation) }}" class="btn btn-outline btn-sm">Voir</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="padding:34px; text-align:center; color:var(--text-muted);">Aucune réservation.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
