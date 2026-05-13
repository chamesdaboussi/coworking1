<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SpaceHive — Trouvez votre espace idéal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
:root {
    --primary: #ff8247;
    --primary-dark: #e06930;
    --primary-light: #fff4ef;
    --gray-900: #111827;
    --gray-700: #374151;
    --gray-500: #6b7280;
    --gray-300: #d1d5db;
    --gray-100: #f3f4f6;
    --white: #ffffff;
    --success: #10b981;
    --warning: #f59e0b;
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--gray-100);
    color: var(--gray-900);
    font-size: 14px;
    line-height: 1.5;
}

/* Navigation */
.nav {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--gray-300);
    padding: 0 5%;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.brand {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-weight: 700;
    font-size: 18px;
    color: var(--gray-900);
}

.brand-icon {
    width: 32px;
    height: 32px;
    background: var(--primary);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 12px;
}

.nav-link {
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    color: var(--gray-700);
    padding: 8px 16px;
    border-radius: var(--radius-sm);
    transition: all 0.2s;
}

.nav-link:hover {
    background: var(--gray-100);
    color: var(--primary);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-outline {
    background: transparent;
    border: 1.5px solid var(--gray-300);
    color: var(--gray-700);
}

.btn-outline:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-light);
}

.btn-primary {
    background: linear-gradient(135deg, #ff8247, #4cbcbe);
    color: var(--white);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #e06930, #3aacae);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Hero Section */
.hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: calc(100vh - 64px);
    background: var(--white);
}

.hero-left {
    padding: 48px 5% 48px 6%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--primary-light);
    color: var(--primary);
    padding: 6px 14px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 600;
    width: fit-content;
    margin-bottom: 24px;
}

.hero-title {
    font-size: clamp(32px, 4vw, 48px);
    font-weight: 700;
    line-height: 1.2;
    color: var(--gray-900);
    margin-bottom: 20px;
    letter-spacing: -0.02em;
}

.hero-title .accent {
    color: var(--primary);
}

.hero-sub {
    font-size: 15px;
    color: var(--gray-500);
    max-width: 440px;
    margin-bottom: 32px;
    line-height: 1.6;
}

/* Search Box */
.search-box {
    background: var(--white);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    padding: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 36px;
    max-width: 560px;
    box-shadow: var(--shadow-lg);
}

.search-field {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: var(--radius-sm);
}

.search-field-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.search-field-val {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-900);
}

.search-input {
    width: 100%;
    border: none;
    outline: none;
    background: transparent;
    font: inherit;
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-900);
    min-width: 120px;
}

.search-input::placeholder {
    color: var(--gray-500);
}

.search-field:focus-within {
    background: var(--primary-light);
}

.no-results-message {
    display: none;
    grid-column: 1/-1;
    text-align: center;
    padding: 42px 20px;
    border: 1px dashed var(--gray-300);
    border-radius: var(--radius-md);
    color: var(--gray-500);
    background: var(--gray-100);
}

.search-sep {
    width: 1px;
    height: 32px;
    background: var(--gray-300);
}

/* Stats */
.stats {
    display: flex;
    gap: 40px;
    justify-content: center;
    text-align: center;
    max-width: 560px;
}

.stat-val {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.stat-lbl {
    font-size: 12px;
    color: var(--gray-500);
}

/* Map Section */
.hero-right {
    position: relative;
    background: var(--gray-100);
    overflow: hidden;
}

#hero-map {
    width: 100%;
    height: 100%;
    min-height: 500px;
}

/* Spaces Section */
.section {
    padding: 60px 5%;
}

.section-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 32px;
}

.section-title {
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.01em;
    margin-bottom: 6px;
}

.section-sub {
    font-size: 14px;
    color: var(--gray-500);
}

.view-all {
    font-size: 14px;
    font-weight: 600;
    color: var(--primary);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Cards Grid */
.spaces-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
}

.space-card {
    background: var(--white);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
    display: block;
}

.space-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary);
}

.card-img {
    height: 180px;
    overflow: hidden;
    position: relative;
    background: var(--primary-light);
}

.card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.space-card:hover .card-img img {
    transform: scale(1.05);
}

.card-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: var(--white);
    padding: 4px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 600;
    color: var(--primary);
    box-shadow: var(--shadow-sm);
}

.avail-dot {
    position: absolute;
    bottom: 12px;
    right: 12px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid var(--white);
}

.avail-dot.ok {
    background: var(--success);
}

.avail-dot.no {
    background: #ef4444;
}

.card-body {
    padding: 16px;
}

.card-name {
    font-weight: 700;
    font-size: 16px;
    margin-bottom: 6px;
}

.card-addr {
    font-size: 12px;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 12px;
}

.card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.card-price {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary);
}

.card-price span {
    font-size: 12px;
    font-weight: 400;
    color: var(--gray-500);
}

.card-cap {
    font-size: 12px;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 4px;
}

.card-amenities {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin: 0 0 12px;
}

.card-amenity {
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-radius: 99px;
    padding: 4px 9px;
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-700);
}

.card-btn {
    display: block;
    text-align: center;
    background: var(--primary);
    color: var(--white);
    border-radius: var(--radius-sm);
    padding: 10px;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    transition: background 0.2s;
}

.card-btn:hover {
    background: var(--primary-dark);
}

/* Why Choose Us */
.why-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
}

.why-card {
    background: var(--white);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    padding: 24px;
    transition: box-shadow 0.2s;
}

.why-card:hover {
    box-shadow: var(--shadow-md);
}

.why-icon {
    width: 48px;
    height: 48px;
    background: var(--primary-light);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}

.why-title {
    font-weight: 700;
    font-size: 16px;
    margin-bottom: 8px;
}

.why-sub {
    font-size: 13px;
    color: var(--gray-500);
    line-height: 1.5;
}

/* Stats Band */
.stats-band {
    background: linear-gradient(135deg, #ff8247, #4cbcbe);
    padding: 48px 5%;
}

.stats-band-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
    max-width: 800px;
    margin: 0 auto;
}

.stats-band-item {
    text-align: center;
}

.stats-band-val {
    font-size: 32px;
    font-weight: 700;
    color: var(--white);
    margin-bottom: 6px;
}

.stats-band-lbl {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.9);
}

/* Map Popup */
.l-popup {
    font-family: 'Inter', sans-serif;
    padding: 4px;
    min-width: 220px;
}

.l-popup-img {
    width: 100%;
    height: 92px;
    border-radius: 10px;
    object-fit: cover;
    margin-bottom: 10px;
    background: var(--gray-100);
}

.l-popup-name {
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 4px;
}

.l-popup-addr {
    font-size: 12px;
    color: var(--gray-500);
    margin-bottom: 8px;
}

.l-popup-price {
    color: var(--primary);
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 8px;
}

.l-popup-amenities {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin: 8px 0 10px;
}

.l-popup-amenity {
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-radius: 999px;
    padding: 3px 7px;
    font-size: 10px;
    color: var(--gray-700);
    line-height: 1.2;
}

.l-popup-link {
    display: block;
    margin-top: 10px;
    background: var(--primary);
    color: var(--white);
    text-align: center;
    border-radius: var(--radius-sm);
    padding: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
}

/* Responsive */
@media (max-width: 900px) {
    .hero {
        grid-template-columns: 1fr;
    }
    
    .hero-right {
        height: 400px;
    }
    
    .stats-band-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
}

@media (max-width: 640px) {
    .nav-links .nav-link {
        display: none;
    }
    
    .stats {
        gap: 24px;
    }
    
    .search-sep {
        display: none;
    }
    
    .hero-title {
        font-size: 28px;
    }
}
</style>
</head>
<body>

<nav class="nav">
    <a href="{{ route('home') }}" class="brand">
        <div class="brand-icon" style="background: linear-gradient(135deg, #ff8247, #4cbcbe);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </div>
        Space<span style="background: linear-gradient(135deg, #ff8247, #4cbcbe); -webkit-background-clip: text; background-clip: text; color: transparent;">Hive</span>
    </a>
    <div class="nav-links">
        <a href="#espaces" class="nav-link">Espaces</a>
        <a href="#pourquoi" class="nav-link">Pourquoi nous</a>
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-outline">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline">Connexion</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Créer un compte</a>
        @endauth
    </div>
</nav>

<section class="hero">
    <div class="hero-left">
        <div class="hero-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 2a15 15 0 0 0 0 20 15 15 0 0 0 0-20z"/>
            </svg>
            Tunis, Tunisie
        </div>
        <h1 class="hero-title">
            Trouvez votre<br><span class="accent">espace de coworking</span><br>idéal
        </h1>
        <p class="hero-sub">Découvrez et réservez des espaces de coworking près de chez vous. Simple, rapide et sécurisé.</p>

        <div class="search-box">
            <label class="search-field" for="spaceSearchInput">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <div style="width: 100%;">
                    <div class="search-field-label">Lieu</div>
                    <input id="spaceSearchInput" class="search-input" type="text" placeholder="Tunis, Tunisie" autocomplete="off">
                </div>
            </label>
            <div class="search-sep"></div>
            <label class="search-field" for="spaceDateInput">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <div style="width: 100%;">
                    <div class="search-field-label">Date</div>
                    <input id="spaceDateInput" class="search-input" type="date">
                </div>
            </label>
            <a href="#espaces" id="spaceSearchBtn" class="btn btn-primary" style="white-space: nowrap;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Rechercher
            </a>
        </div>

        <div class="stats">
            <div>
                <div class="stat-val">{{ $stats['espaces'] }}+</div>
                <div class="stat-lbl">Espaces</div>
            </div>
            <div>
                <div class="stat-val">{{ $stats['utilisateurs'] }}+</div>
                <div class="stat-lbl">Utilisateurs</div>
            </div>
        </div>
    </div>

    <div class="hero-right">
        <div id="hero-map"></div>
    </div>
</section>

<section class="section" id="espaces" style="background: var(--white);">
    <div class="section-header">
        <div>
            <h2 class="section-title">Espaces populaires</h2>
            <p class="section-sub"><span id="spacesResultCount">{{ $espaces->count() }}</span> espace(s) trouvé(s)</p>
        </div>
        @auth
        <a href="{{ route('reservations.create') }}" class="view-all">
            Voir tous les espaces
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </a>
        @endauth
    </div>

    <div class="spaces-grid">
        @forelse($espaces as $espace)
        <div class="space-card" data-space-id="{{ $espace->id_espace }}" data-search="{{ \Illuminate\Support\Str::lower($espace->nom . ' ' . $espace->adresse . ' ' . $espace->type_libelle . ' ' . implode(' ', $espace->amenities ?? [])) }}">
            <div class="card-img">
                @if($espace->image)
                    <img src="{{ route('storage.public', ['path' => $espace->image]) }}" alt="{{ $espace->nom }}">
                @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(59,130,246,0.2)" stroke-width="1">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                @endif
                <div class="card-badge">{{ $espace->type_libelle }}</div>
                <div class="avail-dot {{ $espace->disponible ? 'ok' : 'no' }}"></div>
            </div>
            <div class="card-body">
                <div class="card-name">{{ $espace->nom }}</div>
                <div class="card-addr">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    {{ $espace->adresse }}
                </div>
                @if(!empty($espace->amenities))
                <div class="card-amenities" aria-label="Équipements">
                    @foreach(array_slice($espace->amenities, 0, 4) as $amenity)
                        <span class="card-amenity">{{ $amenity }}</span>
                    @endforeach
                    @if(count($espace->amenities) > 4)
                        <span class="card-amenity">+{{ count($espace->amenities) - 4 }}</span>
                    @endif
                </div>
                @endif
                <div class="card-footer">
                    <div class="card-price">{{ number_format($espace->prix_jour, 0) }} DT <span>/jour</span></div>
                    <div class="card-cap">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                        </svg>
                        {{ $espace->capacite }} pers.
                    </div>
                </div>
                @if($espace->avis_count > 0)
                <div style="display:flex;align-items:center;gap:5px;margin-bottom:10px;">
                    <span style="color:#f59e0b;font-size:13px;letter-spacing:1px;">
                        @for($i=1;$i<=5;$i++){{ round($espace->avg_note) >= $i ? '★' : '☆' }}@endfor
                    </span>
                    <span style="font-size:12px;font-weight:700;color:var(--gray-700);">{{ number_format($espace->avg_note,1) }}</span>
                    <span style="font-size:11px;color:var(--gray-500);">({{ $espace->avis_count }} avis)</span>
                </div>
                @endif
                <div class="card-cta">
                    @auth
                        @if($espace->disponible)
                            <a href="{{ route('reservations.create', ['espace_id' => $espace->id_espace]) }}" data-base-url="{{ route('reservations.create', ['espace_id' => $espace->id_espace]) }}" class="card-btn reserve-link">Réserver maintenant</a>
                        @else
                            <span class="card-btn" style="background: var(--gray-500); cursor: default;">Non disponible</span>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="card-btn">Se connecter pour réserver</a>
                    @endauth
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: var(--gray-500);">
            <p>Aucun espace disponible pour le moment.</p>
        </div>
        @endforelse
        <div id="noSpacesResults" class="no-results-message">Aucun espace ne correspond à votre recherche.</div>
    </div>
</section>

<section class="section" id="pourquoi">
    <div class="section-header">
        <div>
            <h2 class="section-title">Pourquoi nous choisir ?</h2>
            <p class="section-sub">Tout ce dont vous avez besoin pour travailler sereinement</p>
        </div>
    </div>
    <div class="why-grid">
        <div class="why-card">
            <div class="why-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="why-title">Réservation facile</div>
            <div class="why-sub">Réservez en quelques clics. Interface simple et intuitive, disponible 24h/24.</div>
        </div>
        <div class="why-card">
            <div class="why-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>
            <div class="why-title">Meilleurs emplacements</div>
            <div class="why-sub">Des espaces dans les meilleurs quartiers de Tunis, bien connectés et accessibles.</div>
        </div>
        <div class="why-card">
            <div class="why-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div class="why-title">Paiement sécurisé</div>
            <div class="why-sub">Vos paiements sont 100% sécurisés en ligne ou en espèces avec validation admin.</div>
        </div>
        <div class="why-card">
            <div class="why-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="why-title">Support 24/7</div>
            <div class="why-sub">Notre équipe est disponible pour vous aider à tout moment de votre réservation.</div>
        </div>
    </div>
</section>

<div class="stats-band">
    <div class="stats-band-grid">
        <div class="stats-band-item">
            <div class="stats-band-val">{{ $stats['espaces'] }}+</div>
            <div class="stats-band-lbl">Espaces</div>
        </div>
        <div class="stats-band-item">
            <div class="stats-band-val">{{ $stats['utilisateurs'] }}+</div>
            <div class="stats-band-lbl">Utilisateurs</div>
        </div>
        <div class="stats-band-item">
            <div class="stats-band-val">{{ $stats['villes'] }}+</div>
            <div class="stats-band-lbl">Villes</div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const espacesData = @json($espacesJs);

const tunisOffsets = [
    [36.8185, 10.1656], [36.8600, 10.1947], [36.8050, 10.1817],
    [36.7950, 10.1550], [36.8320, 10.1230], [36.8750, 10.2100],
    [36.8430, 10.1760], [36.8010, 10.1900], [36.8700, 10.1500],
];

const map = L.map('hero-map', {
    center: [36.8065, 10.1815],
    zoom: 12,
    scrollWheelZoom: false,
    attributionControl: true,
    zoomControl: true,
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
}).addTo(map);

const mkIcon = (size, color) => L.divIcon({
    className: '',
    html: `<div style="width:${size}px;height:${size}px;border-radius:50% 50% 50% 0;background:${color};border:3px solid white;transform:rotate(-45deg);box-shadow:0 4px 14px rgba(255,130,71,0.4);"></div>`,
    iconSize: [size, size],
    iconAnchor: [size/2, size],
    popupAnchor: [0, -(size+6)],
});

const normalIcon = mkIcon(34, 'var(--primary)');
const hoverIcon = mkIcon(40, 'var(--primary-dark)');

const markerItems = [];
const buildUrlWithDate = (baseUrl) => {
    const selectedDate = document.getElementById('spaceDateInput')?.value || '';
    if (!selectedDate) return baseUrl;
    const url = new URL(baseUrl, window.location.origin);
    url.searchParams.set('date_debut', selectedDate);
    url.searchParams.set('date_fin', selectedDate);
    return url.pathname + url.search + url.hash;
};

const buildPopupHtml = (esp) => {
    const amenities = Array.isArray(esp.amenities) ? esp.amenities : [];
    const amenitiesHtml = amenities.length
        ? `<div class="l-popup-amenities">${amenities.slice(0, 4).map(a => `<span class="l-popup-amenity">${a}</span>`).join('')}${amenities.length > 4 ? `<span class="l-popup-amenity">+${amenities.length - 4}</span>` : ''}</div>`
        : '';
    return `<div class="l-popup">
        <img src="${esp.image}" alt="${esp.nom}" class="l-popup-img" loading="lazy">
        <div class="l-popup-name">${esp.nom}</div>
        <div class="l-popup-addr">📍 ${esp.adresse}</div>
        <div class="l-popup-price">${Number(esp.prix_jour).toFixed(0)} DT <span style="font-size:11px;font-weight:400;">/jour</span></div>
        ${amenitiesHtml}
        <a href="${buildUrlWithDate(esp.url)}" class="l-popup-link">${esp.disponible ? 'Réserver' : 'Voir détails'}</a>
    </div>`;
};

espacesData.forEach((esp, i) => {
    const lat = esp.lat || tunisOffsets[i % tunisOffsets.length][0];
    const lng = esp.lng || tunisOffsets[i % tunisOffsets.length][1];
    const marker = L.marker([lat, lng], { icon: normalIcon }).addTo(map);
    marker.bindPopup(buildPopupHtml(esp), { minWidth: 230, closeButton: true });
    marker.on('mouseover', function() { this.setIcon(hoverIcon); this.openPopup(); });
    marker.on('mouseout', function() { this.setIcon(normalIcon); });
    markerItems.push({ marker, esp, text: `${esp.nom} ${esp.adresse} ${(esp.amenities || []).join(' ')}`.toLowerCase() });
});

const fitVisibleMarkers = () => {
    const visibleMarkers = markerItems.filter(item => map.hasLayer(item.marker)).map(item => item.marker);
    if (visibleMarkers.length > 0) {
        map.fitBounds(L.featureGroup(visibleMarkers).getBounds().pad(0.25));
    }
};

const refreshReservationLinks = () => {
    document.querySelectorAll('.reserve-link[data-base-url]').forEach(link => {
        link.href = buildUrlWithDate(link.dataset.baseUrl);
    });
    markerItems.forEach(item => item.marker.setPopupContent(buildPopupHtml(item.esp)));
};

const filterSpaces = () => {
    const query = (document.getElementById('spaceSearchInput')?.value || '').trim().toLowerCase();
    let visibleCount = 0;

    document.querySelectorAll('.space-card[data-space-id]').forEach(card => {
        const match = !query || (card.dataset.search || '').includes(query);
        card.style.display = match ? '' : 'none';
        if (match) visibleCount++;
    });

    markerItems.forEach(item => {
        const match = !query || item.text.includes(query);
        if (match && !map.hasLayer(item.marker)) item.marker.addTo(map);
        if (!match && map.hasLayer(item.marker)) map.removeLayer(item.marker);
    });

    document.getElementById('spacesResultCount').textContent = visibleCount;
    document.getElementById('noSpacesResults').style.display = visibleCount === 0 ? 'block' : 'none';
    if (visibleCount > 0) fitVisibleMarkers();
};

document.getElementById('spaceSearchInput')?.addEventListener('input', filterSpaces);
document.getElementById('spaceDateInput')?.addEventListener('change', refreshReservationLinks);
document.getElementById('spaceSearchBtn')?.addEventListener('click', (e) => {
    e.preventDefault();
    filterSpaces();
    document.getElementById('espaces')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

refreshReservationLinks();
fitVisibleMarkers();
</script>
</body>
</html>