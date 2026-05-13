@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; display: flex; background: var(--surface); position: relative; overflow: hidden;">
    <div style="position: absolute; top: -200px; right: -200px; width: 600px; height: 600px; background: radial-gradient(circle, rgba(255,130,71,0.08) 0%, transparent 70%); pointer-events: none;"></div>
    <div style="position: absolute; bottom: -200px; left: -200px; width: 500px; height: 500px; background: radial-gradient(circle, rgba(76,188,190,0.06) 0%, transparent 70%); pointer-events: none;"></div>

    <!-- Left panel -->
    <div style="flex: 1; background: linear-gradient(135deg, #ff8247, #4cbcbe); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
        <div style="text-align: center; color: white; padding: 40px; max-width: 80%;">
            <div style="font-size: 72px; margin-bottom: 24px;">🚀</div>
            <h1 style="font-family: 'Poppins', sans-serif; font-size: 36px; font-weight: 800; margin-bottom: 16px;">Rejoignez SpaceHive</h1>
            <p style="font-size: 18px; opacity: 0.9; margin-bottom: 32px; line-height: 1.6;">
                Rejoignez notre communauté d'espaces<br>de travail collaboratifs et modernes.
            </p>
        </div>
    </div>

    <!-- Right panel -->
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 24px; position: relative;">
        <!-- Back Button -->
        <a href="{{ route('home') }}" style="position: absolute; top: 24px; left: 24px; display: flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Retour
        </a>

        <div style="width: 100%; max-width: 560px;" class="fade-in">
            <!-- Logo -->
            <div style="text-align: center; margin-bottom: 32px; margin-top: 20px;">
                <div style="display: inline-flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                    <div style="background: linear-gradient(135deg, #ff8247, #4cbcbe); border-radius: 16px; width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <span style="font-family: 'Poppins', sans-serif; font-size: 28px; font-weight: 800;">Space<span style="background: linear-gradient(135deg, #ff8247, #4cbcbe); -webkit-background-clip: text; background-clip: text; color: transparent;">Hive</span></span>
                </div>
                <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Créez votre compte SpaceHive</p>
            </div>

            <!-- Card -->
            <div class="card" style="background: var(--surface2); border: 1px solid var(--border); box-shadow: 0 24px 48px rgba(0,0,0,0.1); border-radius: 24px;">
                <h2 style="font-family: 'Poppins', sans-serif; font-size: 22px; font-weight: 700; margin-bottom: 24px; text-align: center;">Créer un compte</h2>

                @if($errors->any())
                    <div class="alert alert-error" style="background: rgba(220,53,69,0.1); border-left: 3px solid #dc3545; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px;">
                        @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}" id="registerForm" novalidate>
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Prénom *</label>
                            <input type="text" name="prenom" value="{{ old('prenom') }}" id="reg_prenom" class="form-input" placeholder="Jean"
                                   style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface);">
                            <small id="err_prenom" style="color:#dc3545;display:none;font-size:12px;margin-top:4px;"></small>
                        </div>
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Nom *</label>
                            <input type="text" name="nom" value="{{ old('nom') }}" id="reg_nom" class="form-input" placeholder="Dupont"
                                   style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface);">
                            <small id="err_nom" style="color:#dc3545;display:none;font-size:12px;margin-top:4px;"></small>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Adresse email *</label>
                            <input type="text" name="email" value="{{ old('email') }}" id="reg_email" class="form-input" placeholder="jean@exemple.com"
                                   style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface);">
                            <small id="err_email" style="color:#dc3545;display:none;font-size:12px;margin-top:4px;"></small>
                        </div>
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Téléphone <span style="color:var(--text-muted);font-weight:400;">(optionnel)</span></label>
                            <input type="text" name="telephone" value="{{ old('telephone') }}" id="reg_tel" class="form-input" placeholder="+216 XX XXX XXX"
                                   style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface);">
                            <small id="err_tel" style="color:#dc3545;display:none;font-size:12px;margin-top:4px;"></small>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Mot de passe *</label>
                            <input type="password" name="password" id="reg_password" class="form-input" placeholder="Minimum 8 caractères"
                                   style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface);">
                            <small id="err_password" style="color:#dc3545;display:none;font-size:12px;margin-top:4px;"></small>
                        </div>
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Confirmer le mot de passe *</label>
                            <input type="password" name="password_confirmation" id="reg_confirm" class="form-input" placeholder="Répétez votre mot de passe"
                                   style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface);">
                            <small id="err_confirm" style="color:#dc3545;display:none;font-size:12px;margin-top:4px;"></small>
                        </div>
                    </div>

                    <button type="submit" style="width: 100%; justify-content: center; padding: 14px; border-radius: 40px; font-weight: 600; display: flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #ff8247, #4cbcbe); border: none; color: white; cursor: pointer; font-size: 15px; transition: opacity 0.2s;">
                        Créer mon compte
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </form>
            </div>

            <p style="text-align: center; margin-top: 28px; color: var(--text-muted); font-size: 14px;">
                Déjà un compte ?
                <a href="{{ route('login') }}" style="color: #ff8247; font-weight: 600; text-decoration: none;">Se connecter</a>
            </p>
        </div>
    </div>
</div>

<script>
// ─── Helpers ───────────────────────────────────────────────
function showErr(id, msg) {
    const el = document.getElementById('err_' + id);
    el.textContent = msg;
    el.style.display = 'block';
    document.getElementById('reg_' + id).style.borderColor = '#dc3545';
}
function clearErr(id) {
    document.getElementById('err_' + id).style.display = 'none';
    document.getElementById('reg_' + id).style.borderColor = '';
}
const emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
// Only letters (including accented French letters), hyphens and spaces
const nameRx  = /^[A-Za-zÀ-öø-ÿ\s\-']{2,100}$/;
const telRx   = /^[\+\d\s\-\(\)]{6,20}$/;

// ─── Submit ────────────────────────────────────────────────
document.getElementById('registerForm').addEventListener('submit', function(e) {
    let valid = true;

    // Prénom — letters only
    const prenom = document.getElementById('reg_prenom').value.trim();
    clearErr('prenom');
    if (!prenom) { showErr('prenom', 'Le prénom est requis.'); valid = false; }
    else if (!nameRx.test(prenom)) { showErr('prenom', 'Le prénom ne doit contenir que des lettres (pas de chiffres).'); valid = false; }

    // Nom — letters only
    const nom = document.getElementById('reg_nom').value.trim();
    clearErr('nom');
    if (!nom) { showErr('nom', 'Le nom est requis.'); valid = false; }
    else if (!nameRx.test(nom)) { showErr('nom', 'Le nom ne doit contenir que des lettres (pas de chiffres).'); valid = false; }

    // Email
    const email = document.getElementById('reg_email').value.trim();
    clearErr('email');
    if (!email) { showErr('email', 'L\'adresse email est requise.'); valid = false; }
    else if (!emailRx.test(email)) { showErr('email', 'Veuillez entrer une adresse email valide.'); valid = false; }

    // Téléphone (optionnel)
    const tel = document.getElementById('reg_tel').value.trim();
    clearErr('tel');
    if (tel && !telRx.test(tel)) { showErr('tel', 'Numéro de téléphone invalide.'); valid = false; }

    // Mot de passe
    const pw = document.getElementById('reg_password').value;
    clearErr('password');
    if (!pw) { showErr('password', 'Le mot de passe est requis.'); valid = false; }
    else if (pw.length < 8) { showErr('password', 'Le mot de passe doit contenir au moins 8 caractères.'); valid = false; }

    // Confirmation
    const conf = document.getElementById('reg_confirm').value;
    clearErr('confirm');
    if (!conf) { showErr('confirm', 'Veuillez confirmer votre mot de passe.'); valid = false; }
    else if (conf !== pw) { showErr('confirm', 'Les mots de passe ne correspondent pas.'); valid = false; }

    if (!valid) e.preventDefault();
});

// ─── Clear on input ────────────────────────────────────────
['prenom','nom','email','tel','password','confirm'].forEach(id => {
    const el = document.getElementById('reg_' + id);
    if (el) el.addEventListener('input', () => clearErr(id));
});
</script>
@endsection
