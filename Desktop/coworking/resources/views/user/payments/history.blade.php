@extends('layouts.user')
@section('title', 'Paiements')
@section('page-title', 'Historique des paiements')
@section('content')
<div class="fade-in">
    <div class="card" style="padding:0; overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Réservation</th>
                    <th>Espace</th>
                    <th>Méthode</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paiements as $p)
                <tr>
                    <td><span style="font-family:monospace; color:var(--accent);">{{ $p->reservation->code_confirmation }}</span></td>
                    <td>{{ $p->reservation->espace->nom ?? '—' }}</td>
                    <td>
                        @if($p->methode === 'stripe')
                            <span class="badge badge-blue">Carte de crédit</span>
                        @elseif($p->methode === 'cash')
                            <span class="badge badge-purple">Espèces</span>
                        @else
                            <span class="badge badge-gray">{{ ucfirst($p->methode) }}</span>
                        @endif
                    </td>
                    <td><strong>{{ number_format($p->montant, 2) }} DT</strong></td>
                    <td><span class="badge badge-{{ $p->statut === 'paid' ? 'green' : ($p->statut === 'pending' ? 'yellow' : 'red') }}">{{ ['paid'=>'Payé','pending'=>'En attente','failed'=>'Échoué'][$p->statut] ?? $p->statut }}</span></td>
                    <td style="color:var(--text-muted);">{{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : $p->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">Aucun paiement.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($paiements->hasPages())<div style="padding:14px; border-top:1px solid var(--border);">{{ $paiements->links() }}</div>@endif
    </div>
</div>
@endsection
