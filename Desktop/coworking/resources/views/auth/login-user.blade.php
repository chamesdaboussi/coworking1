@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; display: flex; background: var(--surface); position: relative; overflow: hidden;">
    <!-- Background decoration -->
    <div style="position: absolute; top: -200px; right: -200px; width: 600px; height: 600px; background: radial-gradient(circle, rgba(255, 130, 71, 0.08) 0%, transparent 70%); pointer-events: none;"></div>
    <div style="position: absolute; bottom: -200px; left: -200px; width: 500px; height: 500px; background: radial-gradient(circle, rgba(76, 188, 190, 0.06) 0%, transparent 70%); pointer-events: none;"></div>

    <!-- Left Cadre (Info/Welcome Section) -->
    <div style="flex: 1; background: linear-gradient(135deg, #ff8247, #4cbcbe); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
        <div style="text-align: center; color: white; padding: 40px; max-width: 80%;">
            <div style="font-size: 64px; margin-bottom: 24px;">🏢</div>
            <h1 style="font-family: 'Poppins', sans-serif; font-size: 36px; font-weight: 800; margin-bottom: 16px;">Bienvenue sur SpaceHive</h1>
            <p style="font-size: 18px; opacity: 0.9; margin-bottom: 32px; line-height: 1.6;">
                Trouvez votre espace de travail idéal.<br>
                Collaborez et réussissez ensemble.
            </p>
        </div>
    </div>

    <!-- Right Side: Form Section -->
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 24px; position: relative;">
        <!-- Back Button -->
        <a href="{{ route('home') }}" style="position: absolute; top: 24px; left: 24px; display: flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-size: 14px; transition: all 0.2s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Retour
        </a>

        <div style="width: 100%; max-width: 460px;" class="fade-in">
            <!-- Logo (small version) -->
            <div style="text-align: center; margin-bottom: 32px; margin-top: 20px;">
                <div style="display: inline-flex; align-items: center; gap: 12px;">
                    <div style="background: linear-gradient(135deg, #ff8247, #4cbcbe); border-radius: 14px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <span style="font-family: 'Poppins', sans-serif; font-size: 26px; font-weight: 800; color: var(--text);">Space<span style="background: linear-gradient(135deg, #ff8247, #4cbcbe); -webkit-background-clip: text; background-clip: text; color: transparent;">Hive</span></span>
                </div>
                <p style="color: var(--text-muted); font-size: 15px; margin-top: 12px;">Connectez-vous à votre compte</p>
            </div>

            <!-- Card (Form) -->
            <div class="card" style="background: var(--surface2); border: 1px solid var(--border); box-shadow: 0 24px 48px rgba(0,0,0,0.1); border-radius: 24px;">
                <h2 style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 700; margin-bottom: 28px; text-align: center;">Connexion Utilisateur</h2>

                @if($errors->any())
                    <div class="alert alert-error" style="background: rgba(220,53,69,0.1); border-left: 3px solid #dc3545; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" id="userLoginForm" novalidate>
                    @csrf
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Adresse email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-input email-input" placeholder="votre@email.com" required autofocus
                               style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface); transition: all 0.2s;">
                        <small class="error-message" style="color: #dc3545; display: none; margin-top: 6px; font-size: 12px;"></small>
                    </div>
                    <div style="margin-bottom: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label class="form-label" style="font-weight: 500;">Mot de passe</label>
                            <a href="#" style="font-size: 13px; color: var(--accent-light); text-decoration: none;">Mot de passe oublié ?</a>
                        </div>
                        <input type="password" name="password" class="form-input password-input" placeholder="••••••••" required
                               style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface);">
                        <small class="error-message" style="color: #dc3545; display: none; margin-top: 6px; font-size: 12px;"></small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center; padding: 14px; border-radius: 40px; font-weight: 600; display: flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #ff8247, #4cbcbe); border: none; color: white; cursor: pointer; transition: all 0.2s;">
                        Se connecter
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </form>
            </div>

            <p style="text-align: center; margin-top: 28px; color: var(--text-muted); font-size: 14px;">
                Pas encore de compte ?
                <a href="{{ route('register') }}" style="color: #ff8247; font-weight: 600; text-decoration: none;">Créer un compte</a>
            </p>

            <p style="text-align: center; margin-top: 16px; color: var(--text-muted); font-size: 13px;">
                Administrateur ? <a href="{{ route('admin.login') }}" style="color: #4cbcbe; font-weight: 600; text-decoration: none;">Connexion Admin</a>
            </p>
        </div>
    </div>
</div>

<script>
// Custom form validation
document.getElementById('userLoginForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Email validation
    const emailInput = document.querySelector('.email-input');
    const emailError = emailInput.parentElement.querySelector('.error-message');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailInput.value.trim()) {
        emailError.textContent = 'L\'email est requis.';
        emailError.style.display = 'block';
        emailInput.style.borderColor = '#dc3545';
        isValid = false;
    } else if (!emailRegex.test(emailInput.value)) {
        emailError.textContent = 'Veuillez entrer une adresse email valide.';
        emailError.style.display = 'block';
        emailInput.style.borderColor = '#dc3545';
        isValid = false;
    } else {
        emailError.style.display = 'none';
        emailInput.style.borderColor = '';
    }
    
    // Password validation
    const passwordInput = document.querySelector('.password-input');
    const passwordError = passwordInput.parentElement.querySelector('.error-message');
    
    if (!passwordInput.value.trim()) {
        passwordError.textContent = 'Le mot de passe est requis.';
        passwordError.style.display = 'block';
        passwordInput.style.borderColor = '#dc3545';
        isValid = false;
    } else if (passwordInput.value.length < 1) {
        passwordError.textContent = 'Le mot de passe est invalide.';
        passwordError.style.display = 'block';
        passwordInput.style.borderColor = '#dc3545';
        isValid = false;
    } else {
        passwordError.style.display = 'none';
        passwordInput.style.borderColor = '';
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});

// Clear error on focus
document.querySelectorAll('.form-input').forEach(input => {
    input.addEventListener('focus', function() {
        this.style.borderColor = '';
        const error = this.parentElement.querySelector('.error-message');
        if (error) error.style.display = 'none';
    });
});
</script>
@endsection
