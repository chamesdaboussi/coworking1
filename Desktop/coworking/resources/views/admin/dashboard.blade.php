@extends('layouts.admin')
@section('title', 'Administration')
@section('page-title', 'Tableau de bord Admin')

@section('content')
<div class="fade-in">

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 28px;">
        <div class="stat-card" style="border-left: 3px solid var(--accent);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 9px; background: rgba(79,126,247,0.1); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted);">Réservations</div>
            </div>
            <div style="font-family: 'Poppins', sans-serif; font-size: 34px; font-weight: 800; color: var(--text);">{{ $stats['total_reservations'] }}</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Toutes périodes</div>
        </div>

        <div class="stat-card" style="border-left: 3px solid var(--success);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 9px; background: rgba(15,158,110,0.1); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted);">Confirmées</div>
            </div>
            <div style="font-family: 'Poppins', sans-serif; font-size: 34px; font-weight: 800; color: var(--success);">{{ $stats['confirmed'] }}</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Actuellement actives</div>
        </div>

        <div class="stat-card" style="border-left: 3px solid var(--warning);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 9px; background: rgba(217,119,6,0.1); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--warning)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted);">En attente</div>
            </div>
            <div style="font-family: 'Poppins', sans-serif; font-size: 34px; font-weight: 800; color: var(--warning);">{{ $stats['pending'] }}</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">À confirmer / payer</div>
        </div>

        <div class="stat-card" style="border-left: 3px solid #7c5fcf;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 9px; background: rgba(124,95,207,0.1); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c5fcf" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted);">Revenus ce mois</div>
            </div>
            <div style="font-family: 'Poppins', sans-serif; font-size: 26px; font-weight: 800; color: var(--text);">{{ number_format($stats['revenue_month'], 3) }} DT</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">{{ $stats['espaces_total'] }} espaces actifs</div>
        </div>

        <div class="stat-card" style="border-left: 3px solid #0284c7;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 9px; background: rgba(2,132,199,0.1); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted);">Membres</div>
            </div>
            <div style="font-family: 'Poppins', sans-serif; font-size: 34px; font-weight: 800; color: var(--text);">{{ $stats['users_total'] }}</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Utilisateurs inscrits</div>
        </div>
    </div>

    <!-- Recent reservations -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 18px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-family: 'Poppins', sans-serif; font-size: 15px; font-weight: 700;">Réservations récentes</h3>
            <a href="{{ route('admin.reservations') }}" class="btn btn-outline btn-sm">
                Tout voir
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Client</th>
                        <th>Espace</th>
                        <th>Dates</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReservations as $r)
                    <tr>
                        <td>
                            <span style="font-family: monospace; font-size: 12px; color: var(--accent); background: rgba(79,126,247,0.08); padding: 3px 8px; border-radius: 5px; font-weight: 700;">
                                {{ $r->code_confirmation }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 13px; font-weight: 600;">{{ $r->utilisateur->full_name }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $r->utilisateur->email }}</div>
                        </td>
                        <td style="font-size: 13px; font-weight: 500;">{{ $r->espace->nom }}</td>
                        <td style="font-size: 12px; white-space: nowrap;">
                            {{ $r->period_label }}
                        </td>
                        <td style="font-weight: 700; font-size: 14px;">{{ number_format($r->prix_final, 3) }} DT</td>
                        <td>
                            @php
                                $badge = $r->status_badge;
                                $colors = ['green'=>'badge-green','yellow'=>'badge-yellow','red'=>'badge-red','blue'=>'badge-blue','gray'=>'badge-gray'];
                            @endphp
                            <span class="badge {{ $colors[$badge['color']] ?? 'badge-gray' }}">{{ $badge['label'] }}</span>
                        </td>
                        <td>
                            <a href="{{ route('reservations.show', $r->id_reservation) }}" class="btn btn-outline btn-sm">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px; color: var(--text-muted); font-size: 14px;">Aucune réservation récente.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
