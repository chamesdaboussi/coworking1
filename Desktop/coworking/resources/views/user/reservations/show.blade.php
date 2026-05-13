@extends('layouts.user')
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
    $existingAvis = \App\Models\Avis::where('id_user', auth()->id())
        ->where('id_espace', $reservation->id_espace)
        ->first();
    $canReview = $reservation->isConfirmed();
@endphp

<div class="fade-in">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; gap: 14px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('reservations.index') }}" class="btn btn-outline btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Retour
            </a>
            <span style="font-family: monospace; font-size: 13px; color: var(--accent); background: rgba(79,126,247,0.08); padding: 4px 10px; border-radius: 6px; font-weight: 700;">{{ $reservation->code_confirmation }}</span>
            <span class="badge badge-{{ $reservation->status_badge['color'] }}" style="font-size: 12px; padding: 4px 10px;">{{ $reservation->status_badge['label'] }}</span>
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            @if($reservation->isPending())
                <a href="{{ route('reservations.payment', $reservation->id_reservation) }}" class="btn btn-primary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Payer maintenant
                </a>
                <form method="POST" action="{{ route('reservations.cancel', $reservation->id_reservation) }}" style="display:inline;"
                      onsubmit="return confirm('Annuler cette réservation ? L\'espace sera libéré immédiatement.')">
                    @csrf
                    <button class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#dc2626;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Annuler
                    </button>
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
                @php
                    $done = $currentOrder >= $order[$step['key']];
                @endphp
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

    {{-- Expiry / cancellation info banners --}}
    @if($reservation->isPending() && $reservation->expires_at)
        @if($reservation->payment_method === 'stripe')
            <div style="display:flex;align-items:center;gap:10px;background:#fff7ed;border:1.5px solid #f97316;border-radius:12px;padding:12px 16px;margin-bottom:18px;font-size:14px;color:#9a3412;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Paiement en ligne attendu avant le <strong style="margin:0 4px;">{{ $reservation->expires_at->format('H:i') }}</strong>. Passé ce délai, la réservation sera annulée.
                <a href="{{ route('reservations.payment', $reservation->id_reservation) }}" class="btn btn-primary btn-sm" style="margin-left:auto;">Payer maintenant</a>
            </div>
        @elseif($reservation->payment_method === 'cash')
            <div style="display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1.5px solid #22c55e;border-radius:12px;padding:12px 16px;margin-bottom:18px;font-size:14px;color:#14532d;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Paiement en espèces attendu dans les <strong style="margin:0 4px;">24 heures</strong>. Présentez-vous au bureau avec le montant.
            </div>
        @endif
    @endif
    @if($reservation->isExpired())
        <div style="display:flex;align-items:center;gap:10px;background:#fef2f2;border:1.5px solid #ef4444;border-radius:12px;padding:12px 16px;margin-bottom:18px;font-size:14px;color:#7f1d1d;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Cette réservation a expiré car le paiement n'a pas été reçu dans le délai imparti. L'espace a été libéré.
        </div>
    @endif
    @if($reservation->isCancelled())
        <div style="display:flex;align-items:center;gap:10px;background:#f9fafb;border:1.5px solid #d1d5db;border-radius:12px;padding:12px 16px;margin-bottom:18px;font-size:14px;color:#374151;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Cette réservation a été annulée{{ $reservation->cancelled_at ? ' le ' . $reservation->cancelled_at->format('d/m/Y à H:i') : '' }}. L'espace est de nouveau disponible.
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px;">
        <div class="card">
            <h3 style="font-family: 'Poppins', sans-serif; font-size: 15px; font-weight: 700; margin-bottom: 14px;">Détails</h3>
            <table style="width: 100%; font-size: 14px;">
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 9px 0; color: var(--text-muted); width: 45%;">Espace</td>
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

    {{-- ── Rating & Review Section ── --}}
    @if($canReview)
    <div class="card" style="margin-bottom:20px;overflow:hidden;">
        <div style="padding:0 0 20px;border-bottom:1px solid var(--border);margin-bottom:20px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#ff8247,#4cbcbe);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="0.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div>
                <h3 style="font-family:'Poppins',sans-serif;font-size:16px;font-weight:700;margin:0;">
                    @if($existingAvis) Votre avis @else Donner votre avis @endif
                </h3>
                <p style="color:var(--text-muted);font-size:13px;margin:2px 0 0;">{{ $reservation->espace->nom }}</p>
            </div>
            @if($existingAvis)
            <div style="margin-left:auto;">
                <span style="font-size:11px;color:var(--text-muted);">Modifiable</span>
            </div>
            @endif
        </div>

        @if(session('success'))
        <div style="background:rgba(22,163,74,0.1);border-left:3px solid #16a34a;padding:10px 14px;border-radius:8px;margin-bottom:16px;color:#14532d;font-size:13px;display:flex;align-items:center;gap:8px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('avis.store') }}" id="avisForm" novalidate>
            @csrf
            <input type="hidden" name="id_reservation" value="{{ $reservation->id_reservation }}">

            {{-- Star Rating --}}
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:10px;color:var(--text);">Note *</label>
                <div id="starContainer" style="display:flex;gap:8px;align-items:center;">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="star-btn" data-val="{{ $i }}"
                        style="background:none;border:none;cursor:pointer;padding:4px;transition:transform 0.15s;font-size:32px;line-height:1;color:{{ $existingAvis && $existingAvis->note >= $i ? '#f59e0b' : '#d1d5db' }};"
                        title="{{ $i }} étoile{{ $i > 1 ? 's' : '' }}">
                        ★
                    </button>
                    @endfor
                    <span id="starLabel" style="font-size:14px;color:var(--text-muted);margin-left:8px;">
                        @if($existingAvis)
                            @php $labels = ['','Médiocre','Passable','Correct','Bien','Excellent']; @endphp
                            {{ $labels[$existingAvis->note] ?? '' }}
                        @endif
                    </span>
                </div>
                <input type="hidden" name="note" id="noteInput" value="{{ $existingAvis?->note ?? '' }}">
                <small id="err_note" style="color:#ef4444;display:none;font-size:12px;margin-top:6px;"></small>
            </div>

            {{-- Comment --}}
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:8px;color:var(--text);">
                    Commentaire <span style="font-weight:400;color:var(--text-muted);">(optionnel)</span>
                </label>
                <textarea name="commentaire" id="avisCommentaire" class="form-input" rows="4"
                    placeholder="Partagez votre expérience avec cet espace de coworking..."
                    style="resize:vertical;min-height:100px;">{{ $existingAvis?->commentaire ?? '' }}</textarea>
                <div style="display:flex;justify-content:space-between;margin-top:6px;">
                    <small id="err_commentaire" style="color:#ef4444;display:none;font-size:12px;"></small>
                    <small id="charCount" style="color:var(--text-muted);font-size:11px;margin-left:auto;">0 / 1000</small>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:12px;">
                <button type="submit" class="btn btn-primary" style="border-radius:40px;gap:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $existingAvis ? 'Mettre à jour mon avis' : 'Publier mon avis' }}
                </button>
                @if($existingAvis)
                <button type="button" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#dc2626;"
                    onclick="if(confirm('Supprimer votre avis ?')) document.getElementById('deleteAvisForm').submit();">
                    Supprimer
                </button>
                @endif
            </div>
        </form>

        {{-- Standalone delete form (outside avisForm to avoid nesting) --}}
        @if($existingAvis)
        <form id="deleteAvisForm" method="POST" action="{{ route('avis.destroy', $existingAvis->id_avis) }}" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
        @endif
    </div>

    {{-- All reviews for this space --}}
    @php
        $allAvis = \App\Models\Avis::where('id_espace', $reservation->id_espace)
            ->with('utilisateur')
            ->latest()
            ->get();
        $avgNote = $allAvis->avg('note');
    @endphp

    @if($allAvis->count() > 0)
    <div class="card" style="margin-bottom:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="font-family:'Poppins',sans-serif;font-size:16px;font-weight:700;margin:0;">
                Avis sur {{ $reservation->espace->nom }}
            </h3>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:24px;font-weight:800;color:#f59e0b;">{{ number_format($avgNote,1) }}</span>
                <div>
                    <div style="font-size:16px;color:#f59e0b;letter-spacing:2px;">
                        @for($i = 1; $i <= 5; $i++){{ round($avgNote) >= $i ? '★' : '☆' }}@endfor
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $allAvis->count() }} avis</div>
                </div>
            </div>
        </div>
        <div style="display:grid;gap:14px;">
            @foreach($allAvis as $avis)
            <div style="padding:14px;background:var(--surface3);border-radius:10px;border:1px solid var(--border);{{ $avis->id_user === auth()->id() ? 'border-color:rgba(255,130,71,0.4);background:rgba(255,130,71,0.04);' : '' }}">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;gap:10px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#ff8247,#4cbcbe);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:12px;flex-shrink:0;">
                            {{ strtoupper(substr($avis->utilisateur->prenom ?? $avis->utilisateur->nom, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:13px;">
                                {{ $avis->id_user === auth()->id() ? 'Vous' : $avis->utilisateur->full_name }}
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $avis->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div style="font-size:18px;color:#f59e0b;letter-spacing:1px;">
                        @for($i = 1; $i <= 5; $i++){{ $avis->note >= $i ? '★' : '☆' }}@endfor
                    </div>
                </div>
                @if($avis->commentaire)
                <p style="font-size:13px;color:var(--text);margin:0;line-height:1.6;">{{ $avis->commentaire }}</p>
                @else
                <p style="font-size:12px;color:var(--text-muted);margin:0;font-style:italic;">Aucun commentaire.</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif

</div>

<style>
.star-btn:hover { transform: scale(1.2); }
.star-btn.hovered, .star-btn.active { color: #f59e0b !important; }
</style>

<script>
const stars = document.querySelectorAll('.star-btn');
const noteInput = document.getElementById('noteInput');
const starLabel = document.getElementById('starLabel');
const labels = ['','Médiocre','Passable','Correct','Bien','Excellent'];

let currentVal = parseInt(noteInput.value) || 0;

function paintStars(val) {
    stars.forEach(s => {
        s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#d1d5db';
    });
    starLabel.textContent = val ? labels[val] : '';
}

paintStars(currentVal);

stars.forEach(star => {
    star.addEventListener('mouseenter', () => paintStars(parseInt(star.dataset.val)));
    star.addEventListener('mouseleave', () => paintStars(currentVal));
    star.addEventListener('click', () => {
        currentVal = parseInt(star.dataset.val);
        noteInput.value = currentVal;
        paintStars(currentVal);
        document.getElementById('err_note').style.display = 'none';
    });
});

// Char counter
const ta = document.getElementById('avisCommentaire');
const cc = document.getElementById('charCount');
if (ta && cc) {
    cc.textContent = ta.value.length + ' / 1000';
    ta.addEventListener('input', () => {
        cc.textContent = ta.value.length + ' / 1000';
        if (ta.value.length > 1000) cc.style.color = '#dc2626';
        else cc.style.color = 'var(--text-muted)';
    });
}

// Form validation
document.getElementById('avisForm').addEventListener('submit', function(e) {
    let ok = true;
    const noteErr = document.getElementById('err_note');
    if (!noteInput.value || parseInt(noteInput.value) < 1) {
        noteErr.textContent = 'Veuillez sélectionner une note (1 à 5 étoiles).';
        noteErr.style.display = 'block';
        ok = false;
    } else {
        noteErr.style.display = 'none';
    }
    const commErr = document.getElementById('err_commentaire');
    if (ta && ta.value.length > 1000) {
        commErr.textContent = 'Le commentaire ne peut pas dépasser 1000 caractères.';
        commErr.style.display = 'block';
        ok = false;
    } else {
        commErr.style.display = 'none';
    }
    if (!ok) e.preventDefault();
});
</script>
@endsection
