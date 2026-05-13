@extends('layouts.admin')
@section('title', 'Réservation ' . $reservation->code_confirmation)
@section('page-title', 'Détail de la réservation')

@section('content')
@php
    $latestPaiement = $reservation->paiements->sortByDesc('id_paiement')->first();
    $methodLabels = [
        'stripe' => 'Carte de crédit (en ligne)',
        'cash' => 'Espèces (sur place)',
    ];
    $paymentStatusLabels = [
        'paid' => 'Payé',
        'pending' => 'En attente',
        'failed' => 'Échoué',
    ];
@endphp

<div class="fade-in">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; gap: 14px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.reservations') }}" class="btn btn-outline btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Retour
            </a>
            <span style="font-family: monospace; font-size: 13px; color: var(--accent); background: rgba(79,126,247,0.08); padding: 4px 10px; border-radius: 6px; font-weight: 700;">{{ $reservation->code_confirmation }}</span>
            <span class="badge badge-{{ $reservation->status_badge['color'] }}" style="font-size: 12px; padding: 4px 10px;">{{ $reservation->status_badge['label'] }}</span>
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            @if($latestPaiement && $latestPaiement->methode === 'cash' && $latestPaiement->statut === 'pending')
                <form method="POST" action="{{ route('admin.reservations.validateCash', $reservation->id_reservation) }}" style="display:inline;">
                    @csrf
                    <button class="btn btn-success btn-sm" onclick="return confirm('Valider le paiement cash ?')">Valider cash</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card" style="margin-bottom: 20px;">
        <h3 style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 700; margin-bottom: 18px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Suivi</h3>
        <div style="display: flex; align-items: center;">
            @php
                $isTerminal = in_array($reservation->statut, ['expired', 'cancelled']);
                $steps = $isTerminal ? [
                    ['key' => 'pending',   'label' => 'En attente'],
                    ['key' => $reservation->statut, 'label' => $reservation->status_badge['label']],
                ] : [
                    ['key' => 'pending',   'label' => 'En attente'],
                    ['key' => 'confirmed', 'label' => 'Confirmée'],
                ];
                $order = ['pending' => 0, 'confirmed' => 1, 'expired' => 1, 'cancelled' => 1];
                $currentOrder = $order[$reservation->statut] ?? 0;
                $svgs = [
                    'pending'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                    'confirmed' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
                    'expired'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
                    'cancelled' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
                ];
            @endphp

            @foreach($steps as $i => $step)
                @php $done = $currentOrder >= $order[$step['key']]; @endphp
                <div style="flex: 1; display: flex; align-items: center;">
                    <div style="text-align: center; flex: 1;">
                        @php
                            $terminalColor = in_array($step['key'], ['expired','cancelled']) ? '#dc2626' : 'var(--accent)';
                            $stepBg    = $done ? $terminalColor : 'var(--surface3)';
                            $stepBor   = $done ? $terminalColor : 'var(--border)';
                            $stepColor = $done ? 'white' : 'var(--text-muted)';
                        @endphp
                        <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; background: {{ $stepBg }}; border: 2px solid {{ $stepBor }}; color: {{ $stepColor }}; transition: all 0.2s;">
                            {!! $svgs[$step['key']] !!}
                        </div>
                        <div style="font-size: 12px; font-weight: {{ $done ? '700' : '400' }}; color: {{ $done ? 'var(--text)' : 'var(--text-muted)' }};">{{ $step['label'] }}</div>
                    </div>
                    @if($i < count($steps) - 1)
                        <div style="height: 2px; flex: 1; background: {{ $currentOrder > $order[$step['key']] ? 'var(--accent)' : 'var(--border)' }};"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @if($reservation->isExpired())
        <div style="display:flex;align-items:center;gap:10px;background:#fef2f2;border:1.5px solid #ef4444;border-radius:12px;padding:12px 16px;margin-bottom:18px;font-size:14px;color:#7f1d1d;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Cette réservation a expiré — le paiement n'a pas été reçu dans le délai imparti.
        </div>
    @endif
    @if($reservation->isCancelled())
        <div style="display:flex;align-items:center;gap:10px;background:#f9fafb;border:1.5px solid #d1d5db;border-radius:12px;padding:12px 16px;margin-bottom:18px;font-size:14px;color:#374151;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Réservation annulée{{ $reservation->cancelled_at ? ' le ' . $reservation->cancelled_at->format('d/m/Y à H:i') : '' }}.
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px;">
        <div class="card">
            <h3 style="font-family: 'Poppins', sans-serif; font-size: 15px; font-weight: 700; margin-bottom: 14px;">Détails</h3>
            <table style="width: 100%; font-size: 14px;">
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--text-muted); width: 45%;">Client</td>
                    <td style="padding: 9px 0; font-weight: 600;">
                        {{ $reservation->utilisateur->full_name }}<br>
                        <span style="font-size: 12px; color: var(--text-muted); font-weight: 400;">{{ $reservation->utilisateur->email }}</span>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--text-muted);">Espace</td>
                    <td style="padding: 9px 0; font-weight: 600;">{{ $reservation->espace->nom }}</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--text-muted);">Adresse</td>
                    <td style="padding: 9px 0; font-size: 13px;">{{ $reservation->espace->adresse }}</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--text-muted);">Type</td>
                    <td style="padding: 9px 0;"><span class="badge badge-blue">{{ $reservation->type_reservation === 'daily' ? 'Journalier' : 'Horaire' }}</span></td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--text-muted);">{{ $reservation->type_reservation === 'hourly' ? 'Date et horaire' : 'Période' }}</td>
                    <td style="padding: 9px 0;">{{ $reservation->period_label }}</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--text-muted);">Durée</td>
                    <td style="padding: 9px 0; font-weight: 600;">{{ $reservation->duration_label }}</td>
                </tr>
                @if($reservation->notes)
                <tr>
                    <td style="padding: 9px 0; color: var(--text-muted); vertical-align: top;">Notes</td>
                    <td style="padding: 9px 0; font-size: 13px; color: var(--text-muted);">{{ $reservation->notes }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="card">
            <h3 style="font-family: 'Poppins', sans-serif; font-size: 15px; font-weight: 700; margin-bottom: 14px;">Paiement</h3>
            <table style="width: 100%; font-size: 14px;">
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--text-muted);">Méthode choisie</td>
                    <td style="padding: 9px 0; text-align: right; font-weight: 700;">
                        @if($latestPaiement)
                            <span class="badge badge-blue">{{ $methodLabels[$latestPaiement->methode] ?? strtoupper($latestPaiement->methode) }}</span>
                        @else
                            <span class="badge badge-gray">Non choisie</span>
                        @endif
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--text-muted);">État du paiement</td>
                    <td style="padding: 9px 0; text-align: right;">
                        @if($latestPaiement)
                            <span class="badge badge-{{ $latestPaiement->statut === 'paid' ? 'green' : ($latestPaiement->statut === 'pending' ? 'yellow' : 'red') }}">
                                {{ $paymentStatusLabels[$latestPaiement->statut] ?? $latestPaiement->statut }}
                            </span>
                        @else
                            <span class="badge badge-gray">Non payé</span>
                        @endif
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--text-muted);">Prix de base</td>
                    <td style="padding: 9px 0; text-align: right; font-weight: 600;">{{ number_format($reservation->prix_total, 2) }} DT</td>
                </tr>
                @if($reservation->remise_montant > 0)
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--success);">Code promo</td>
                    <td style="padding: 9px 0; text-align: right; color: var(--success); font-weight: 600;">- {{ number_format($reservation->remise_montant, 2) }} DT</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 12px 0 0; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 15px;">Total</td>
                    <td style="padding: 12px 0 0; text-align: right; font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 20px; color: var(--accent);">{{ number_format($reservation->prix_final, 2) }} DT</td>
                </tr>
            </table>

            @if($reservation->paiements->count())
                <div style="margin-top: 16px; display: grid; gap: 10px;">
                    @foreach($reservation->paiements->sortByDesc('id_paiement') as $paiement)
                    <div style="padding: 12px; background: var(--surface3); border-radius: 9px; border: 1px solid var(--border);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; gap: 8px;">
                            <span style="font-size: 12px; color: var(--text-muted);">{{ $methodLabels[$paiement->methode] ?? strtoupper($paiement->methode) }}</span>
                            <span class="badge badge-{{ $paiement->statut === 'paid' ? 'green' : ($paiement->statut === 'pending' ? 'yellow' : 'red') }}">
                                {{ $paymentStatusLabels[$paiement->statut] ?? $paiement->statut }}
                            </span>
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            {{ $paiement->paid_at ? $paiement->paid_at->format('d/m/Y à H:i') : 'Choisi le ' . $paiement->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
