<?php

namespace App\Http\Controllers;

use App\Models\{Reservation, Paiement};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $stats = [
            'total' => $user->reservations()->count(),
            'active' => $user->reservations()->where('statut', 'confirmed')->where('date_fin', '>=', today())->count(),
            'pending' => $user->reservations()->where('statut', 'pending')->count(),
            'spent' => Paiement::whereHas('reservation', fn($q) => $q->where('id_user', $user->id_user))->where('statut', 'paid')->sum('montant'),
        ];

        $upcomingReservations = $user->reservations()
            ->with('espace')
            ->whereIn('statut', ['confirmed', 'pending'])
            ->where('date_debut', '>=', today())
            ->orderBy('date_debut')
            ->limit(3)
            ->get();

        $recentReservations = $user->reservations()
            ->with(['espace', 'latestPaiement'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard.index', compact('stats', 'upcomingReservations', 'recentReservations'));
    }
}
