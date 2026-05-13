{{-- admin/promo/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Codes Promo')
@section('page-title', 'Codes Promotionnels')

@section('content')
<div class="fade-in" style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start;">

    {{-- ── Table ── --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:18px 20px;border-bottom:1px solid var(--border);">
            <h3 style="font-family:'Poppins',sans-serif;font-size:16px;font-weight:700;margin:0;">Codes actifs</h3>
        </div>
        <table class="table">
            <thead>
                <tr><th>Code</th><th>Type</th><th>Valeur</th><th>Utilisations</th><th>Expiration</th><th>Statut</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($codes as $code)
                <tr>
                    <td><span style="font-family:monospace;font-size:13px;color:var(--accent-light);background:rgba(79,126,247,0.1);padding:3px 8px;border-radius:6px;font-weight:700;">{{ $code->code }}</span></td>
                    <td><span class="badge badge-{{ $code->type === 'percent' ? 'purple' : 'blue' }}">{{ $code->type === 'percent' ? 'Pourcentage' : 'Fixe' }}</span></td>
                    <td style="font-weight:700;color:#10b981;">{{ $code->type === 'percent' ? $code->valeur.'%' : $code->valeur.' DT' }}</td>
                    <td style="color:var(--text-muted);">{{ $code->usage_count }}{{ $code->usage_max ? '/'.$code->usage_max : '' }}</td>
                    <td style="font-size:13px;color:var(--text-muted);">{{ $code->date_expiration ? $code->date_expiration->format('d/m/Y') : '∞' }}</td>
                    <td><span class="badge badge-{{ $code->actif ? 'green' : 'gray' }}">{{ $code->actif ? 'Actif' : 'Inactif' }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.promo.delete', $code->id_code) }}" onsubmit="return confirm('Supprimer ce code promo ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">Aucun code promo.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($codes->hasPages())<div style="padding:16px;">{{ $codes->links() }}</div>@endif
    </div>

    {{-- ── Create Form ── --}}
    <div class="card" style="position:sticky;top:80px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <div style="width:36px;height:36px;border-radius:9px;background:linear-gradient(135deg,#ff8247,#4cbcbe);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </div>
            <h3 style="font-family:'Poppins',sans-serif;font-size:16px;font-weight:700;margin:0;">Nouveau code promo</h3>
        </div>

        @if(session('success'))
        <div style="background:rgba(22,163,74,0.1);border-left:3px solid #16a34a;padding:10px 14px;border-radius:8px;margin-bottom:14px;color:#14532d;font-size:13px;">
            ✓ {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.promo.store') }}" id="promoForm" novalidate>
            @csrf

            {{-- Code --}}
            <div style="margin-bottom:14px;">
                <label class="form-label">Code *</label>
                <input type="text" name="code" id="promo_code" class="form-input"
                    placeholder="Ex: SUMMER20" style="text-transform:uppercase;"
                    value="{{ old('code') }}" autocomplete="off">
                <small id="err_code" style="color:#ef4444;display:none;font-size:12px;margin-top:4px;"></small>
            </div>

            {{-- Type + Valeur --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
                <div>
                    <label class="form-label">Type *</label>
                    <select name="type" id="promo_type" class="form-input">
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Pourcentage (%)</option>
                        <option value="fixed"   {{ old('type') === 'fixed'   ? 'selected' : '' }}>Montant fixe (DT)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Valeur *</label>
                    <input type="number" name="valeur" id="promo_valeur" class="form-input"
                        step="0.01" min="0.01" placeholder="Ex: 10"
                        value="{{ old('valeur') }}">
                    <small id="err_valeur" style="color:#ef4444;display:none;font-size:12px;margin-top:4px;"></small>
                </div>
            </div>

            {{-- Montant minimum — default 0 --}}
            <div style="margin-bottom:14px;">
                <label class="form-label">Montant minimum <span style="font-weight:400;text-transform:none;">(DT)</span></label>
                <input type="number" name="valeur_min_commande" id="promo_min" class="form-input"
                    step="0.01" min="0" value="{{ old('valeur_min_commande', '0') }}">
                <small id="err_min" style="color:#ef4444;display:none;font-size:12px;margin-top:4px;"></small>
                <div style="font-size:11px;color:var(--text-muted);margin-top:3px;">Laisser à 0 pour aucun minimum.</div>
            </div>

            {{-- Expiration + Utilisations max --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px;">
                <div>
                    <label class="form-label">Expiration <span style="font-weight:400;text-transform:none;">(optionnel)</span></label>
                    <input type="date" name="date_expiration" id="promo_exp" class="form-input"
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                        value="{{ old('date_expiration') }}">
                    <small id="err_exp" style="color:#ef4444;display:none;font-size:12px;margin-top:4px;"></small>
                </div>
                <div>
                    <label class="form-label">Utilisations max <span style="font-weight:400;text-transform:none;">(optionnel)</span></label>
                    <input type="number" name="usage_max" id="promo_usage" class="form-input"
                        min="1" placeholder="Illimité"
                        value="{{ old('usage_max') }}">
                    <small id="err_usage" style="color:#ef4444;display:none;font-size:12px;margin-top:4px;"></small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;border-radius:40px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Créer le code
            </button>
        </form>
    </div>
</div>

<script>
// ── Helpers ──────────────────────────────────────────────────────────────────
function showErr(fieldId, msg) {
    document.getElementById('err_' + fieldId).textContent = msg;
    document.getElementById('err_' + fieldId).style.display = 'block';
    const input = document.getElementById('promo_' + fieldId);
    if (input) input.style.borderColor = '#ef4444';
}
function clearErr(fieldId) {
    document.getElementById('err_' + fieldId).style.display = 'none';
    const input = document.getElementById('promo_' + fieldId);
    if (input) input.style.borderColor = '';
}

// ── Submit validation ─────────────────────────────────────────────────────────
document.getElementById('promoForm').addEventListener('submit', function(e) {
    let valid = true;

    // Code — required, alphanumeric + dash/underscore, 2-30 chars
    const code = document.getElementById('promo_code').value.trim().toUpperCase();
    clearErr('code');
    if (!code) {
        showErr('code', 'Le code promo est requis.'); valid = false;
    } else if (code.length < 2 || code.length > 30) {
        showErr('code', 'Le code doit contenir entre 2 et 30 caractères.'); valid = false;
    } else if (!/^[A-Z0-9_\-]+$/.test(code)) {
        showErr('code', 'Uniquement lettres, chiffres, tirets et underscores.'); valid = false;
    }

    // Valeur — required, > 0; if percent must be ≤ 100
    const valeur = document.getElementById('promo_valeur').value.trim();
    const type   = document.getElementById('promo_type').value;
    clearErr('valeur');
    if (valeur === '') {
        showErr('valeur', 'La valeur est requise.'); valid = false;
    } else if (isNaN(valeur) || parseFloat(valeur) <= 0) {
        showErr('valeur', 'La valeur doit être un nombre positif (> 0).'); valid = false;
    } else if (type === 'percent' && parseFloat(valeur) > 100) {
        showErr('valeur', 'Un pourcentage ne peut pas dépasser 100 %.'); valid = false;
    }

    // Montant minimum — optional, but if filled must be ≥ 0 and numeric
    const min = document.getElementById('promo_min').value.trim();
    clearErr('min');
    if (min === '') {
        // empty is fine; backend will treat as 0
    } else if (isNaN(min) || parseFloat(min) < 0) {
        showErr('min', 'Le montant minimum doit être ≥ 0.'); valid = false;
    }

    // Expiration — optional; if filled must be strictly in the future (≥ tomorrow)
    const exp = document.getElementById('promo_exp').value;
    clearErr('exp');
    if (exp) {
        const tomorrow = new Date();
        tomorrow.setHours(0, 0, 0, 0);
        tomorrow.setDate(tomorrow.getDate() + 1);
        if (new Date(exp + 'T00:00:00') < tomorrow) {
            showErr('exp', 'La date doit être demain au minimum.'); valid = false;
        }
    }

    // Utilisations max — optional; if filled must be integer ≥ 1
    const usage = document.getElementById('promo_usage').value.trim();
    clearErr('usage');
    if (usage !== '') {
        const u = parseInt(usage);
        if (isNaN(u) || u < 1 || !Number.isInteger(parseFloat(usage))) {
            showErr('usage', 'Doit être un entier ≥ 1 (ou laisser vide pour illimité).'); valid = false;
        }
    }

    if (!valid) e.preventDefault();
});

// ── Clear errors on input ─────────────────────────────────────────────────────
['code','valeur','min','exp','usage'].forEach(id => {
    const el = document.getElementById('promo_' + id);
    if (el) el.addEventListener('input', () => clearErr(id));
});
document.getElementById('promo_type').addEventListener('change', () => clearErr('valeur'));
</script>
@endsection
