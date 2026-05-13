<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SpaceHive') — Espace de Coworking</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.css">
    <style>
        :root {
            --accent: #ff8247;
            --accent-light: #6b94f8;
            --accent-dark: #3563d4;
            --surface: #f5f7ff;
            --surface2: #ffffff;
            --surface3: #eef1fb;
            --text: #1a1f3c;
            --text-muted: #6b7399;
            --border: #dde2f5;
            --success: #0f9e6e;
            --warning: #d97706;
            --danger: #dc2626;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--surface);
            color: var(--text);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.55;
        }
        h1,h2,h3,h4,h5,.font-display { font-family: 'Poppins', sans-serif; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--surface); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        /* Sidebar */
        .sidebar {
            background: var(--surface2);
            border-right: 1px solid var(--border);
            width: 252px;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow-y: auto;
            z-index: 50;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 12px rgba(79,126,247,0.06);
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 10px;
            color: var(--text-muted); font-size: 13px; font-weight: 500;
            transition: all 0.15s; text-decoration: none;
            margin: 1px 0;
        }
        .sidebar-link:hover {
            background: var(--surface3);
            color: var(--accent);
        }
        .sidebar-link.active {
            background: rgba(79,126,247,0.1);
            color: var(--accent);
            font-weight: 600;
        }
        .sidebar-link svg { flex-shrink: 0; }

        .main-content { margin-left: 252px; min-height: 100vh; }

        /* Cards */
        .card {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 1px 4px rgba(79,126,247,0.05);
        }
        .card-sm { padding: 14px; border-radius: 10px; }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: 9px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            border: none; transition: all 0.15s; text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: var(--accent-dark); box-shadow: 0 3px 14px rgba(79,126,247,0.3); }
        .btn-outline { background: transparent; color: var(--text); border: 1.5px solid var(--border); }
        .btn-outline:hover { background: var(--surface3); border-color: var(--accent); color: var(--accent); }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #0d8a5f; }
        .btn-sm { padding: 5px 12px; font-size: 13px; border-radius: 7px; }
        .btn-lg { padding: 12px 26px; font-size: 15px; border-radius: 11px; }

        /* Form inputs */
        .form-input {
            width: 100%;
            background: var(--surface3);
            border: 1.5px solid var(--border);
            border-radius: 9px;
            color: var(--text);
            padding: 9px 13px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(79,126,247,0.12); background: white; }
        .form-input::placeholder { color: #a8afc8; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.06em; }

        /* Badges */
        .badge {
            display: inline-flex; align-items: center;
            padding: 2px 9px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
        }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .badge-gray { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
        .badge-purple { background: #ede9fe; color: #7c3aed; }

        /* Stats card */
        .stat-card {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 1px 4px rgba(79,126,247,0.05);
        }

        /* Table */
        .table { width: 100%; border-collapse: collapse; }
        .table th {
            text-align: left; padding: 11px 14px;
            font-size: 11px; font-weight: 700;
            color: var(--text-muted); text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1.5px solid var(--border);
            background: var(--surface3);
        }
        .table td {
            padding: 13px 14px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            line-height: 1.55;
        }
        .table tr:last-child td { border-bottom: none; }
        .table tr:hover td { background: rgba(79,126,247,0.03); }

        /* Alert */
        .alert { border-radius: 9px; padding: 11px 14px; margin-bottom: 14px; font-size: 13px; display: flex; align-items: flex-start; gap: 8px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .alert-info { background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; }
        .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #d97706; }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #ff8247, #4cbcbe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animations */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.25s ease forwards; }

        /* Loading spinner */
        .spinner { width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: white; animation: spin 0.6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        /* Divider label */
        .nav-label {
            font-size: 10px; color: var(--text-muted); font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            margin: 14px 0 6px; padding: 0 4px;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full">

<!-- Sidebar — User -->
<aside class="sidebar" id="sidebar">
    <div style="padding: 20px 16px;">
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 10px; margin-bottom: 24px; text-decoration: none;">
            <div style="background: linear-gradient(135deg, #ff8247, #4cbcbe); border-radius: 11px; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <span style="font-family: 'Poppins', sans-serif; font-size: 20px; font-weight: 800; color: var(--text);">Space<span class="gradient-text">Hive</span></span>
        </a>

        <!-- User chip -->
        <div style="background: var(--surface3); border-radius: 10px; padding: 10px 12px; margin-bottom: 20px; border: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: linear-gradient(135deg, #ff8247, #4cbcbe); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; font-size: 13px; flex-shrink: 0;">
                    {{ strtoupper(substr(Auth::user()->prenom ?? Auth::user()->nom, 0, 1)) }}
                </div>
                <div style="min-width: 0;">
                    <div style="font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->full_name }}</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Membre</div>
                </div>
            </div>
        </div>

        <!-- Navigation — User only -->
        <nav>
            <div class="nav-label">Principal</div>

            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                Tableau de bord
            </a>

            <a href="{{ route('reservations.index') }}" class="sidebar-link {{ request()->routeIs('reservations.index') || request()->routeIs('reservations.show') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Mes Réservations
                @if(Auth::user()->reservations()->where('statut', 'pending')->count() > 0)
                    <span class="badge badge-yellow" style="margin-left: auto; font-size: 11px;">{{ Auth::user()->reservations()->where('statut', 'pending')->count() }}</span>
                @endif
            </a>

            <a href="{{ route('reservations.create') }}" class="sidebar-link {{ request()->routeIs('reservations.create') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Nouvelle Réservation
            </a>

            <a href="{{ route('reservations.calendar') }}" class="sidebar-link {{ request()->routeIs('reservations.calendar') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><circle cx="8" cy="15" r="1" fill="currentColor"/><circle cx="12" cy="15" r="1" fill="currentColor"/><circle cx="16" cy="15" r="1" fill="currentColor"/></svg>
                Calendrier
            </a>

            <a href="{{ route('payments.history') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Paiements
            </a>
        </nav>

        <!-- Logout -->
        <div style="margin-top: 20px; padding-top: 14px; border-top: 1px solid var(--border);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link" style="background: none; border: none; cursor: pointer; width: 100%; color: var(--text-muted);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Main -->
<div class="main-content">
    <!-- Top bar -->
    <header style="background: var(--surface2); border-bottom: 1px solid var(--border); padding: 0 24px; height: 58px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 40;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button onclick="document.getElementById('sidebar').classList.toggle('open')" style="display: none; background: none; border: none; color: var(--text); cursor: pointer;" class="mobile-menu-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <h1 style="font-family: 'Poppins', sans-serif; font-size: 18px; font-weight: 700; color: var(--text);">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('reservations.create') }}" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Réserver
            </a>
        </div>
    </header>

    <!-- Flash messages -->
    <div style="padding: 0 24px; padding-top: 14px;" id="flash-container">
        @if(session('success'))
            <div class="alert alert-success fade-in">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error fade-in">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info fade-in">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
                {{ session('info') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-error fade-in">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
            </div>
        @endif
    </div>

    <!-- Content -->
    <main style="padding: 14px 24px 40px;">
        @yield('content')
    </main>
</div>

<script>
setTimeout(() => {
    document.querySelectorAll('#flash-container .alert').forEach(a => {
        a.style.opacity = '0'; a.style.transition = 'opacity 0.4s';
        setTimeout(() => a.remove(), 400);
    });
}, 4000);
</script>
@stack('scripts')
</body>
</html>
