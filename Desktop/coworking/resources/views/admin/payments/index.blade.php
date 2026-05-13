@extends('layouts.admin')
@section('title', 'Paiements')
@section('page-title', 'Paiements')

@section('content')
<div class="fade-in">

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:24px;">
        <div class="stat-card" style="text-align:center;">
            <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Total encaissé</div>
            <div style="font-size:22px;font-weight:800;color:var(--accent);">{{ number_format($stats['total_paid'], 2) }} DT</div>
        </div>
        <div class="stat-card" style="text-align:center;">
            <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Payés</div>
            <div style="font-size:22px;font-weight:800;color:#16a34a;">{{ $stats['count_paid'] }}</div>
        </div>
        <div class="stat-card" style="text-align:center;">
            <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">En attente</div>
            <div style="font-size:22px;font-weight:800;color:#d97706;">{{ $stats['count_pending'] }}</div>
        </div>
        <div class="stat-card" style="text-align:center;">
            <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Cartes de crédit</div>
            <div style="font-size:22px;font-weight:800;color:var(--text);">{{ $stats['count_stripe'] }}</div>
        </div>
        <div class="stat-card" style="text-align:center;">
            <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Cash</div>
            <div style="font-size:22px;font-weight:800;color:var(--text);">{{ $stats['count_cash'] }}</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Espace</th>
                    <th>Réservation</th>
                    <th>Méthode</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($paiements as $p)
                @php
                    $r = $p->reservation;
                    $u = $r?->utilisateur;
                @endphp
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;">{{ $p->id_paiement }}</td>
                    <td>
                        @if($u)
                            <div style="font-weight:600;font-size:13px;">{{ $u->full_name }}</div>
                            <div style="color:var(--text-muted);font-size:12px;">{{ $u->email }}</div>
                        @else
                            <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $r?->espace?->nom ?? '—' }}</td>
                    <td>
                        @if($r)
                            <a href="{{ route('reservations.show', $r->id_reservation) }}"
                               style="font-family:monospace;font-size:12px;color:var(--accent);text-decoration:none;font-weight:700;">
                                {{ $r->code_confirmation }}
                            </a>
                        @else
                            <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($p->methode === 'stripe')
                            <span class="badge badge-blue">Carte de crédit</span>
                        @elseif($p->methode === 'cash')
                            <span class="badge badge-purple">Espèces</span>
                        @else
                            <span class="badge badge-gray">{{ ucfirst($p->methode) }}</span>
                        @endif
                    </td>
                    <td style="font-weight:700;">{{ number_format($p->montant, 2) }} DT</td>
                    <td>
                        @if($p->statut === 'paid')
                            <span class="badge badge-green">Payé</span>
                        @elseif($p->statut === 'pending')
                            <span class="badge badge-yellow">En attente</span>
                        @else
                            <span class="badge badge-red">Échoué</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--text-muted);white-space:nowrap;">
                        {{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : $p->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        @if($r && $p->methode === 'cash' && $p->statut === 'pending')
                            <form method="POST" action="{{ route('admin.reservations.validateCash', $r->id_reservation) }}">
                                @csrf
                                <button class="btn btn-success btn-sm" onclick="return confirm('Valider ce paiement cash ?')">
                                    Valider
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;color:var(--text-muted);padding:40px;">
                        Aucun paiement trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($paiements->hasPages())
    <div style="margin-top:18px;display:flex;justify-content:center;">
        {{ $paiements->links() }}
    </div>
    @endif

</div>
@endsection
