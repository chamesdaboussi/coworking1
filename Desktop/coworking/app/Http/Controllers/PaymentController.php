<?php

namespace App\Http\Controllers;

use App\Models\{Paiement, Reservation};
use App\Services\{ReservationService, StripeService};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    const MAX_SWITCHES = 2;

    public function __construct(private StripeService $stripeService) {}

    public function history()
    {
        $user = Auth::user();
        $query = Paiement::with(['reservation.espace']);

        if (!$user->isAdmin()) {
            $query->whereHas('reservation', fn($q) => $q->where('id_user', $user->id_user));
        }

        $paiements = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('user.payments.history', compact('paiements'));
    }

    public function chooseCash(Reservation $reservation)
    {
        $this->authorizePayment($reservation);

        if (!$reservation->isPending()) {
            return redirect()->route('reservations.show', $reservation)
                ->with('info', 'Cette réservation est déjà traitée.');
        }

        // Already cash — no-op
        if ($reservation->payment_method === 'cash') {
            return redirect()->route('reservations.payment', $reservation->id_reservation)
                ->with('info', 'Vous avez déjà choisi le paiement en espèces.');
        }

        // Enforce switch limit
        if ($reservation->payment_method === 'stripe' && $reservation->payment_switch_count >= self::MAX_SWITCHES) {
            return redirect()->route('reservations.payment', $reservation->id_reservation)
                ->with('warning', 'Vous avez changé de méthode trop de fois. Veuillez compléter votre paiement en ligne ou annuler la réservation.');
        }

        $reservation->update([
            'payment_method'       => 'cash',
            'payment_switch_count' => $reservation->payment_switch_count + ($reservation->payment_method === 'stripe' ? 1 : 0),
            'expires_at'           => Carbon::now()->addHours(ReservationService::CASH_HOLD_HOURS),
        ]);

        Paiement::where('id_reservation', $reservation->id_reservation)
            ->where('statut', 'pending')
            ->delete();

        Paiement::create([
            'id_reservation' => $reservation->id_reservation,
            'montant'        => $reservation->prix_final,
            'statut'         => 'pending',
            'methode'        => 'cash',
        ]);

        return redirect()->route('reservations.payment', $reservation->id_reservation)
            ->with('success', 'Paiement en espèces choisi. Présentez-vous avec le montant dans les 24 h. Passé ce délai, la réservation sera automatiquement annulée.');
    }

    public function prepareStripe(Reservation $reservation)
    {
        $this->authorizePayment($reservation);

        if (!$reservation->isPending()) {
            return redirect()->route('reservations.show', $reservation)
                ->with('info', 'Cette réservation est déjà confirmée.');
        }

        // Already stripe — just go to payment page
        if ($reservation->payment_method === 'stripe') {
            return redirect()->route('reservations.payment', $reservation->id_reservation);
        }

        // Enforce switch limit
        if ($reservation->payment_method === 'cash' && $reservation->payment_switch_count >= self::MAX_SWITCHES) {
            return redirect()->route('reservations.payment', $reservation->id_reservation)
                ->with('warning', 'Vous avez changé de méthode trop de fois. Veuillez vous présenter pour payer en espèces ou annuler la réservation.');
        }

        $reservation->update([
            'payment_method'       => 'stripe',
            'payment_switch_count' => $reservation->payment_switch_count + ($reservation->payment_method === 'cash' ? 1 : 0),
            'expires_at'           => Carbon::now()->addMinutes(ReservationService::CHOOSE_METHOD_MINUTES),
        ]);

        return redirect()->route('reservations.payment', $reservation->id_reservation);
    }

    public function confirmPublicStripe(Request $request, Reservation $reservation)
    {
        $this->authorizePayment($reservation);

        $request->validate([
            'payment_method_id' => 'required|string|max:255',
        ]);

        // Reject if the window already passed
        if ($reservation->isExpired()) {
            return response()->json([
                'ok'      => false,
                'message' => 'Le délai de paiement a expiré. Veuillez créer une nouvelle réservation.',
            ], 422);
        }

        if (!$reservation->isPending()) {
            return response()->json(['ok' => true, 'redirect' => route('reservations.show', $reservation->id_reservation)]);
        }

        $this->stripeService->confirmPublicPayment($reservation, $request->payment_method_id);

        return response()->json([
            'ok'       => true,
            'redirect' => route('reservations.show', $reservation->id_reservation),
        ]);
    }

    private function authorizePayment(Reservation $reservation): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $reservation->id_user !== $user->id_user) abort(403);
    }
}