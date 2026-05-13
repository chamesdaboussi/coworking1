<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\EspaceCoworking;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvisController extends Controller
{
    /** User: store or update their review */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_reservation' => 'required|integer|exists:reservations,id_reservation',
            'note'           => 'required|integer|between:1,5',
            'commentaire'    => 'nullable|string|max:1000',
        ]);

        $reservation = Reservation::findOrFail($data['id_reservation']);

        if ($reservation->id_user !== Auth::id()) {
            abort(403, 'Vous ne pouvez pas noter cette réservation.');
        }

        if ($reservation->statut !== 'confirmed') {
            return back()->withErrors(['avis' => 'Vous ne pouvez noter que les réservations confirmées.']);
        }

        Avis::updateOrCreate(
            ['id_user' => Auth::id(), 'id_espace' => $reservation->id_espace],
            [
                'id_reservation' => $reservation->id_reservation,
                'note'           => $data['note'],
                'commentaire'    => $data['commentaire'] ?? null,
            ]
        );

        return back()->with('success', 'Votre avis a été enregistré. Merci !');
    }

    /** User: delete their own review (standalone form, method-spoofed DELETE) */
    public function destroy(Avis $avis)
    {
        if ($avis->id_user !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        $avis->delete();
        return back()->with('success', 'Avis supprimé.');
    }

    /** Admin: list all reviews with filters */
    public function adminIndex(Request $request)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $query = Avis::with(['utilisateur', 'espace', 'reservation'])
            ->latest();

        if ($request->filled('espace')) {
            $query->where('id_espace', $request->espace);
        }
        if ($request->filled('note')) {
            $query->where('note', $request->note);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('commentaire', 'like', "%$s%")
                  ->orWhereHas('utilisateur', fn($u) => $u->where('nom','like',"%$s%")->orWhere('prenom','like',"%$s%")->orWhere('email','like',"%$s%"))
                  ->orWhereHas('espace', fn($e) => $e->where('nom','like',"%$s%"));
            });
        }

        $avis    = $query->paginate(15)->withQueryString();
        $espaces = EspaceCoworking::orderBy('nom')->get();

        $stats = [
            'total'   => Avis::count(),
            'avg'     => round(Avis::avg('note') ?? 0, 1),
            'five'    => Avis::where('note', 5)->count(),
            'one_two' => Avis::whereIn('note', [1, 2])->count(),
        ];

        return view('admin.avis.index', compact('avis', 'espaces', 'stats'));
    }

    /** Admin: delete any review */
    public function adminDestroy(Avis $avis)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $avis->delete();
        return back()->with('success', 'Avis supprimé.');
    }
}

