<?php

namespace App\Http\Controllers;

use App\Models\{Reservation, EspaceCoworking, CodePromo, Paiement};
use App\Services\{ReservationService, StripeService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservationService,
        private StripeService $stripeService
    ) {}

    /**
     * List reservations for current user (or all for admin)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Bulk-expire any overdue pending reservations for this user before listing
        $expiredIds = Reservation::where('statut', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->when(!$user->isAdmin(), fn($q) => $q->where('id_user', $user->id_user))
            ->pluck('id_reservation');

        if ($expiredIds->isNotEmpty()) {
            Reservation::whereIn('id_reservation', $expiredIds)->update(['statut' => 'expired']);
            Paiement::whereIn('id_reservation', $expiredIds)->where('statut', 'pending')->update(['statut' => 'failed']);
        }

        $query = Reservation::with(['espace', 'utilisateur', 'latestPaiement']);

        if (!$user->isAdmin()) {
            $query->where('id_user', $user->id_user);
        }

        // Filters
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('code_confirmation', 'like', "%$s%")
                  ->orWhereHas('espace', fn($sq) => $sq->where('nom', 'like', "%$s%"))
                  ->orWhereHas('utilisateur', fn($sq) => $sq->where('nom', 'like', "%$s%"));
            });
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('date_debut', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_fin', '<=', $request->date_fin);
        }
        if ($request->filled('espace')) {
            $query->where('id_espace', $request->espace);
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $espaces = EspaceCoworking::where('disponible', true)->get();

        $view = $user->isAdmin() ? 'admin.reservations.index' : 'user.reservations.index';
        return view($view, compact('reservations', 'espaces'));
    }

    /**
     * Show booking form
     */
    public function create(Request $request)
    {
        $espaces = EspaceCoworking::where('disponible', true)->get();
        $selectedEspace = $request->espace_id ? EspaceCoworking::find($request->espace_id) : null;

        // Pre-map for Alpine.js (avoids Blade/PHP arrow-function conflict inside @json)
        $espacesJs = $espaces->map(function ($e) {
            return [
                'id'          => $e->id_espace,
                'nom'         => $e->nom,
                'adresse'     => $e->adresse,
                'prix_jour'   => (float) $e->prix_jour,
                'prix_heure'  => (float) $e->prix_heure,
                'capacite'    => (int) $e->capacite,
                'type'        => $e->type,
                'type_libelle'=> $e->type_libelle ?? ucfirst(str_replace('_', ' ', $e->type)),
                'description' => $e->description,
                'amenities'   => $e->amenities ?? [],
                'image'       => $e->image ? route('storage.public', ['path' => $e->image]) : null,
            ];
        })->values();

        return view('user.reservations.create', compact('espaces', 'selectedEspace', 'espacesJs'));
    }

    /**
     * Store new reservation
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_espace'         => 'required|exists:espace_coworking,id_espace',
            'date_debut'        => 'required|date|after_or_equal:today',
            'date_fin'          => 'required|date|after_or_equal:date_debut',
            'type_reservation'  => 'required|in:hourly,daily',
            'heure_debut'       => 'required_if:type_reservation,hourly|nullable|date_format:H:i',
            'heure_fin'         => 'required_if:type_reservation,hourly|nullable|date_format:H:i|after:heure_debut',
            'notes'             => 'nullable|string|max:500',
            'code_promo'        => 'nullable|string|max:50',
        ]);

        // For hourly reservations: date_fin == date_debut (same day booking)
        if ($request->type_reservation === 'hourly') {
            $request->merge(['date_fin' => $request->date_debut]);
        }

        // Check availability
        $availability = $this->reservationService->checkAvailability(
            $request->id_espace,
            $request->date_debut,
            $request->date_fin
        );

        if (!$availability['available']) {
            $suggestion = $availability['suggestion'];
            $message = "Cet espace n'est pas disponible pour les dates sélectionnées.";
            if ($suggestion) {
                $message .= " Prochaine disponibilité : {$suggestion['date_debut']} au {$suggestion['date_fin']}.";
            }
            return back()->withErrors(['availability' => $message])->withInput();
        }

        try {
            $reservation = $this->reservationService->create($request->all(), Auth::id() ?? Auth::user()->id_user);
            return redirect()->route('reservations.payment', $reservation->id_reservation)
                ->with('success', "Réservation créée ! Procédez au paiement.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show reservation details
     */
    public function show(Reservation $reservation)
    {
        $this->authorizeReservation($reservation);
        $this->autoExpire($reservation);
        $reservation->load(['espace', 'utilisateur', 'paiements', 'codePromo']);
        $view = Auth::user()->isAdmin() ? 'admin.reservations.show' : 'user.reservations.show';
        return view($view, compact('reservation'));
    }

    /**
     * Show payment page
     */
    public function payment(Reservation $reservation)
    {
        $this->authorizeReservation($reservation);
        $this->autoExpire($reservation);

        if ($reservation->isExpired()) {
            return redirect()->route('reservations.show', $reservation->id_reservation)
                ->with('warning', 'Le délai de paiement a expiré. Votre réservation a été annulée.');
        }

        if ($reservation->isConfirmed()) {
            return redirect()->route('reservations.show', $reservation->id_reservation)
                ->with('info', 'Cette réservation est déjà payée et confirmée.');
        }

        $reservation->load(['espace', 'codePromo', 'latestPaiement']);

        $paymentData = null;
        $showStripe = session('method') === 'stripe'
            || request('method') === 'stripe'
            || $reservation->payment_method === 'stripe';

        if ($showStripe) {
            try {
                $paymentData = $this->stripeService->createPublicPaymentSession($reservation);
                // Stamp method in case user came back without query param
                if (!$reservation->payment_method) {
                    $reservation->update(['payment_method' => 'stripe']);
                }
            } catch (\Exception $e) {
                return back()->withErrors(['payment' => 'Erreur paiement en ligne: ' . $e->getMessage()]);
            }
        }

        return view('user.reservations.payment', compact('reservation', 'paymentData'));
    }

    /**
     * Handle payment success callback
     */
    public function paymentSuccess(Request $request, Reservation $reservation)
    {
        $this->authorizeReservation($reservation);

        return redirect()->route('reservations.show', $reservation->id_reservation)
            ->with('success', '🎉 Paiement confirmé ! Votre réservation est active.');
    }


    /**
     * Calendar view
     */
    public function calendar()
    {
        $user = Auth::user();
        $query = Reservation::with('espace');

        if (!$user->isAdmin()) {
            $query->where('id_user', $user->id_user);
        }

        $reservations = $query->whereIn('statut', ['pending', 'confirmed'])
            ->get()
            ->map(fn($r) => [
                'id'    => $r->id_reservation,
                'title' => $r->espace->nom . ' (' . $r->status_badge['label'] . ')',
                'start' => $r->date_debut->format('Y-m-d'),
                'end'   => $r->date_fin->addDay()->format('Y-m-d'),
                'color' => match($r->statut) {
                    'confirmed' => '#10b981',
                    'pending'   => '#f59e0b',
                    default     => '#6b7280',
                },
                'url'   => route('reservations.show', $r->id_reservation),
            ]);

        return view('user.reservations.calendar', compact('reservations'));
    }

    /**
     * Check availability via AJAX
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'id_espace'  => 'required|exists:espace_coworking,id_espace',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after_or_equal:date_debut',
        ]);

        $result = $this->reservationService->checkAvailability(
            $request->id_espace,
            $request->date_debut,
            $request->date_fin,
            $request->exclude_id
        );

        return response()->json($result);
    }

    /**
     * Calculate price via AJAX
     */
    public function calculatePrice(Request $request)
    {
        $request->validate([
            'id_espace'        => 'required|exists:espace_coworking,id_espace',
            'date_debut'       => 'required|date',
            'date_fin'         => 'required|date|after_or_equal:date_debut',
            'type_reservation' => 'required|in:hourly,daily',
        ]);

        try {
            $pricing = $this->reservationService->calculatePricing($request->all());
            return response()->json(['success' => true, 'pricing' => $pricing]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Validate promo code via AJAX
     */
    public function validatePromo(Request $request)
    {
        $code = CodePromo::where('code', $request->code)->first();

        if (!$code) {
            return response()->json(['valid' => false, 'message' => 'Code invalide.']);
        }

        $montant = (float) $request->montant;
        if (!$code->isValid($montant)) {
            return response()->json(['valid' => false, 'message' => 'Code expiré ou conditions non remplies.']);
        }

        $discount = $code->calculateDiscount($montant);
        return response()->json([
            'valid'   => true,
            'discount'=> $discount,
            'label'   => $code->type === 'percent' ? "{$code->valeur}% de réduction" : "{$code->valeur} DT de réduction",
        ]);
    }


    /**
     * Cancel a pending reservation (user-initiated)
     */
    public function cancel(Reservation $reservation)
    {
        $this->authorizeReservation($reservation);

        if (!$reservation->isPending()) {
            return back()->with('info', 'Seules les réservations en attente peuvent être annulées.');
        }

        $reservation->update([
            'statut'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Cancel any pending payment records too
        $reservation->paiements()->where('statut', 'pending')->update(['statut' => 'failed']);

        return redirect()->route('reservations.index')
            ->with('success', "Réservation annulée avec succès. L'espace est de nouveau disponible.");
    }

    private function autoExpire(Reservation $reservation): void
    {
        if ($reservation->isPending()
            && $reservation->expires_at
            && $reservation->expires_at->isPast()
        ) {
            $reservation->update(['statut' => 'expired']);
            Paiement::where('id_reservation', $reservation->id_reservation)
                ->where('statut', 'pending')
                ->update(['statut' => 'failed']);
            $reservation->refresh();
        }
    }

    private function authorizeReservation(Reservation $reservation): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $reservation->id_user !== $user->id_user) {
            abort(403);
        }
    }
}