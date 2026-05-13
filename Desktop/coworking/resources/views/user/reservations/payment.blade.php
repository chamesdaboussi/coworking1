@extends('layouts.user')
@section('title', 'Paiement')
@section('page-title', 'Paiement')

@push('styles')
<style>
#card-element {
    background: var(--surface3);
    border-radius: 12px;
    padding: 16px;
    border: 1px solid var(--border);
    margin: 14px 0;
}
.method-card {
    border: 1.5px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    background: white;
    transition: border-color 0.2s, box-shadow 0.2s;
    position: relative;
}
.method-card.active-method {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(79,126,247,0.1);
}
.method-card.active-cash {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22,163,74,0.08);
}
.method-badge {
    position: absolute;
    top: -10px;
    left: 16px;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 20px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.method-badge-stripe { background: var(--accent); color: white; }
.method-badge-cash   { background: #16a34a; color: white; }

/* Countdown banner */
#hold-banner {
    display: flex;
    align-items: center;
    gap: 12px;
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 22px;
    font-size: 14px;
    font-weight: 600;
    border: 1.5px solid #f97316;
    background: #fff7ed;
    color: #9a3412;
    transition: background 0.4s, border-color 0.4s, color 0.4s;
}
#hold-banner.danger { background: #fef2f2; border-color: #ef4444; color: #7f1d1d; }
#hold-banner.expired-ui { background: #fef2f2; border-color: #ef4444; color: #7f1d1d; opacity: 0.8; }
#hold-banner svg { flex-shrink: 0; }
#hold-timer {
    font-size: 22px;
    font-weight: 800;
    font-variant-numeric: tabular-nums;
    letter-spacing: 0.04em;
    color: #c2410c;
    min-width: 56px;
    text-align: center;
    background: rgba(0,0,0,0.06);
    border-radius: 8px;
    padding: 2px 8px;
}
#hold-banner.danger #hold-timer { color: #dc2626; }
#hold-label { flex: 1; line-height: 1.4; }

.switch-btn {
    font-size: 12px;
    color: var(--text-muted);
    text-decoration: underline;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px 0;
    margin-top: 10px;
    display: inline-block;
}
.switch-btn:hover { color: var(--accent); }
.switch-locked {
    font-size: 12px;
    color: #dc2626;
    margin-top: 8px;
    font-style: italic;
}
</style>
@endpush

@php
    $switchCount = $reservation->payment_switch_count;
    $maxSwitches = 2;
    $canSwitch = $switchCount < $maxSwitches;
    $currentMethod = $reservation->payment_method;
@endphp

@section('content')
<div class="fade-in" style="max-width:980px;margin:0 auto;">

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert" style="margin-bottom:16px;">{{ session('info') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-error" style="margin-bottom:16px;">{{ session('warning') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:16px;">{{ $errors->first() }}</div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 340px;gap:22px;align-items:start;">

        {{-- ── Left column ── --}}
        <div class="card">
            <h2 style="font-family:'Poppins';font-size:20px;font-weight:750;margin-bottom:16px;">Méthode de paiement</h2>

            {{-- Countdown (stripe pending) --}}
            @if($reservation->isPending() && $reservation->expires_at && $currentMethod === 'stripe')
                <div id="hold-banner">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span id="hold-timer">--:--</span>
                    <span id="hold-label">Complétez votre paiement en ligne avant expiration.</span>
                </div>
            @endif

            {{-- No method yet: initial choose --}}
            @if(!$currentMethod)
                <p style="color:var(--text-muted);font-size:14px;margin-bottom:18px;">Choisissez comment vous souhaitez payer :</p>
            @endif

            {{-- ── Stripe card ── --}}
            <div class="method-card {{ $currentMethod === 'stripe' ? 'active-method' : '' }}" style="margin-bottom:16px;">
                @if($currentMethod === 'stripe')
                    <span class="method-badge method-badge-stripe">Méthode choisie</span>
                @endif
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $currentMethod === 'stripe' ? 'var(--accent)' : 'var(--text-muted)' }}" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                    <h3 style="font-weight:700;margin:0;">Carte bancaire (en ligne)</h3>
                </div>
                <p style="color:var(--text-muted);font-size:13px;margin-bottom:12px;">Paiement sécurisé immédiat. Votre réservation est confirmée instantanément.</p>

                @if($currentMethod === 'stripe' && $paymentData)
                    {{-- Stripe form --}}
                    <div id="payment-message" class="alert" style="display:none;margin-bottom:10px;"></div>
                    <form id="payment-form">
                        <div id="card-element"></div>
                        <button id="submit" class="btn btn-primary" type="submit" style="width:100%;justify-content:center;margin-top:4px;">
                            Confirmer {{ number_format($reservation->prix_final, 2) }} DT
                        </button>
                    </form>
                    @if($canSwitch && $currentMethod === 'stripe')
                        <form method="POST" action="{{ route('reservations.payment.cash', $reservation->id_reservation) }}" style="margin-top:4px;"
                              onsubmit="return confirm('Changer pour paiement en espèces ? ({{ $maxSwitches - $switchCount }} changement(s) restant(s))')">
                            @csrf
                            <button type="submit" class="switch-btn">Changer pour paiement en espèces</button>
                        </form>
                    @elseif(!$canSwitch && $currentMethod === 'stripe')
                        <p class="switch-locked">⚠️ Limite de changements atteinte. Veuillez payer en ligne ou annuler.</p>
                    @endif
                @elseif($currentMethod !== 'stripe')
                    <form method="POST" action="{{ route('reservations.payment.stripe', $reservation->id_reservation) }}"
                          @if($currentMethod === 'cash' && !$canSwitch) onsubmit="return false;" @endif>
                        @csrf
                        @if($currentMethod === 'cash' && !$canSwitch)
                            <button class="btn btn-outline" type="button" disabled style="width:100%;justify-content:center;opacity:0.5;cursor:not-allowed;">
                                Payer en ligne (non disponible)
                            </button>
                            <p class="switch-locked">⚠️ Limite de changements atteinte. Veuillez payer en espèces ou annuler.</p>
                        @elseif($currentMethod === 'cash')
                            <button class="btn btn-outline" type="submit" style="width:100%;justify-content:center;"
                                    onclick="return confirm('Changer pour paiement en ligne ? ({{ $maxSwitches - $switchCount }} changement(s) restant(s))')">
                                Changer pour paiement en ligne
                            </button>
                        @else
                            <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center;">
                                Payer en ligne
                            </button>
                        @endif
                    </form>
                @endif
            </div>

            {{-- ── Cash card ── --}}
            <div class="method-card {{ $currentMethod === 'cash' ? 'active-cash' : '' }}">
                @if($currentMethod === 'cash')
                    <span class="method-badge method-badge-cash">Méthode choisie</span>
                @endif
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $currentMethod === 'cash' ? '#16a34a' : 'var(--text-muted)' }}" stroke-width="2">
                        <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                    <h3 style="font-weight:700;margin:0;">Espèces / Cash</h3>
                </div>

                @if($currentMethod === 'cash' && $reservation->expires_at && $reservation->isPending())
                    <div style="background:#f0fdf4;border:1px solid #22c55e;border-radius:10px;padding:10px 12px;margin-bottom:12px;font-size:13px;color:#14532d;">
                        ✅ Présentez-vous avec <strong>{{ number_format($reservation->prix_final, 2) }} DT</strong>
                        dans les <strong>24 heures</strong> pour valider votre réservation.
                    </div>
                    @if($canSwitch)
                        <form method="POST" action="{{ route('reservations.payment.stripe', $reservation->id_reservation) }}"
                              onsubmit="return confirm('Changer pour paiement en ligne ? ({{ $maxSwitches - $switchCount }} changement(s) restant(s))')">
                            @csrf
                            <button type="submit" class="switch-btn">Changer pour paiement en ligne</button>
                        </form>
                    @else
                        <p class="switch-locked">⚠️ Limite de changements atteinte. Veuillez vous présenter en espèces ou annuler.</p>
                    @endif
                @else
                    <p style="color:var(--text-muted);font-size:13px;margin-bottom:12px;">Payez en vous présentant au bureau. Vous aurez <strong>24 h</strong> pour venir avec le montant.</p>
                    @if($currentMethod !== 'cash')
                        <form method="POST" action="{{ route('reservations.payment.cash', $reservation->id_reservation) }}"
                              @if($currentMethod === 'stripe' && !$canSwitch) onsubmit="return false;" @endif>
                            @csrf
                            @if($currentMethod === 'stripe' && !$canSwitch)
                                <button class="btn btn-outline" type="button" disabled style="width:100%;justify-content:center;opacity:0.5;cursor:not-allowed;">
                                    Choisir cash (non disponible)
                                </button>
                            @elseif($currentMethod === 'stripe')
                                <button class="btn btn-outline" type="submit" style="width:100%;justify-content:center;"
                                        onclick="return confirm('Changer pour paiement en espèces ? ({{ $maxSwitches - $switchCount }} changement(s) restant(s))')">
                                    Changer pour espèces
                                </button>
                            @else
                                <button class="btn btn-outline" type="submit" style="width:100%;justify-content:center;">
                                    Choisir cash
                                </button>
                            @endif
                        </form>
                    @endif
                @endif
            </div>

        </div>

        {{-- ── Right column: summary + cancel ── --}}
        <div class="card">
            <h3 style="font-family:'Poppins';font-size:16px;font-weight:700;margin-bottom:14px;">Récapitulatif</h3>
            <p><strong>{{ $reservation->espace->nom }}</strong></p>
            <p style="color:var(--text-muted);font-size:13px;">{{ $reservation->period_label }}</p>
            <hr style="border:none;border-top:1px solid var(--border);margin:14px 0;">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                <span>Prix</span><strong>{{ number_format($reservation->prix_total, 2) }} DT</strong>
            </div>
            @if($reservation->remise_montant > 0)
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;color:var(--success);">
                <span>Remise</span><strong>-{{ number_format($reservation->remise_montant, 2) }} DT</strong>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;font-size:18px;">
                <span>Total</span><strong style="color:var(--accent);">{{ number_format($reservation->prix_final, 2) }} DT</strong>
            </div>

            @if($switchCount > 0)
            <div style="margin-top:14px;font-size:12px;color:var(--text-muted);background:var(--surface3);border-radius:8px;padding:8px 10px;">
                Changements de méthode : <strong>{{ $switchCount }}/{{ $maxSwitches }}</strong>
                @if(!$canSwitch)
                    <br><span style="color:#dc2626;">— Limite atteinte</span>
                @endif
            </div>
            @endif

            @if($reservation->isPending())
            <hr style="border:none;border-top:1px solid var(--border);margin:16px 0;">
            <a href="{{ route('reservations.show', $reservation->id_reservation) }}" class="btn btn-outline" style="width:100%;justify-content:center;margin-bottom:8px;">
                Voir les détails
            </a>
            <form method="POST" action="{{ route('reservations.cancel', $reservation->id_reservation) }}"
                  onsubmit="return confirm('Annuler cette réservation ? L\'espace sera libéré immédiatement.')">
                @csrf
                <button type="submit" class="btn btn-outline"
                        style="width:100%;justify-content:center;color:#dc2626;border-color:#dc2626;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         style="margin-right:6px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Annuler la réservation
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')

{{-- Countdown: stripe pending only --}}
@if($reservation->isPending() && $reservation->expires_at && $currentMethod === 'stripe')
<script>
(function () {
    const expiresAt   = {{ $reservation->expires_at->timestamp }} * 1000;
    const banner      = document.getElementById('hold-banner');
    const timerEl     = document.getElementById('hold-timer');
    const labelEl     = document.getElementById('hold-label');
    const redirectUrl = '{{ route('reservations.index') }}';

    function tick() {
        const remaining = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
        const m = String(Math.floor(remaining / 60)).padStart(2, '0');
        const s = String(remaining % 60).padStart(2, '0');
        timerEl.textContent = m + ':' + s;

        if (remaining <= 60) banner.classList.add('danger');

        if (remaining === 0) {
            timerEl.textContent = '00:00';
            banner.classList.add('expired-ui');
            labelEl.textContent = 'Le délai a expiré. Redirection en cours…';
            setTimeout(() => { window.location.href = redirectUrl; }, 2000);
            return;
        }

        setTimeout(tick, 1000);
    }

    tick();
})();
</script>
@endif

{{-- Stripe card form JS --}}
@if($paymentData && $currentMethod === 'stripe')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe   = Stripe('{{ config('services.stripe.key') }}');
const elements = stripe.elements();
const card     = elements.create('card', {
    hidePostalCode: true,
    style: {
        base:    { fontSize: '15px', color: '#1f2937', fontFamily: 'Poppins, sans-serif' },
        invalid: { color: '#dc2626' }
    }
});
card.mount('#card-element');

document.getElementById('payment-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submit');
    const msg = document.getElementById('payment-message');
    btn.disabled    = true;
    btn.textContent = 'Traitement…';
    msg.style.display = 'none';

    const result = await stripe.createPaymentMethod({ type: 'card', card });
    if (result.error) {
        msg.style.display = 'block';
        msg.className     = 'alert alert-error';
        msg.textContent   = result.error.message;
        btn.disabled      = false;
        btn.textContent   = 'Réessayer';
        return;
    }

    const response = await fetch('{{ route('reservations.payment.stripe.confirm', $reservation->id_reservation) }}', {
        method: 'POST',
        headers: {
            'Content-Type':  'application/json',
            'Accept':        'application/json',
            'X-CSRF-TOKEN':  '{{ csrf_token() }}'
        },
        body: JSON.stringify({ payment_method_id: result.paymentMethod.id })
    });

    const data = await response.json();
    if (data.ok) {
        window.location.href = data.redirect;
        return;
    }

    msg.style.display = 'block';
    msg.className     = 'alert alert-error';
    msg.textContent   = data.message || 'Paiement refusé. Veuillez réessayer.';
    btn.disabled      = false;
    btn.textContent   = 'Réessayer';
});
</script>
@endif

@endpush
