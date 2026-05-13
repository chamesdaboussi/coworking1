<?php

namespace App\Services;

use App\Models\{Paiement, Reservation};
use Illuminate\Support\Facades\DB;

class StripeService
{
    public function createPublicPaymentSession(Reservation $reservation): array
    {
        Paiement::where('id_reservation', $reservation->id_reservation)
            ->where('statut', 'pending')
            ->delete();

        $paiement = Paiement::create([
            'id_reservation' => $reservation->id_reservation,
            'montant' => $reservation->prix_final,
            'statut' => 'pending',
            'methode' => 'stripe',
        ]);

        return [
            'publishable_key' => config('services.stripe.key'),
            'payment_id' => $paiement->id_paiement,
            'amount' => (float) $reservation->prix_final,
        ];
    }

    public function confirmPublicPayment(Reservation $reservation, string $paymentMethodId = null): bool
    {
        return DB::transaction(function () use ($reservation, $paymentMethodId) {
            $paiement = Paiement::where('id_reservation', $reservation->id_reservation)
                ->where('methode', 'stripe')
                ->latest('id_paiement')
                ->first();

            if (!$paiement) {
                $paiement = new Paiement([
                    'id_reservation' => $reservation->id_reservation,
                    'montant' => $reservation->prix_final,
                    'methode' => 'stripe',
                ]);
            }

            $paiement->statut = 'paid';
            $paiement->paid_at = now();
            $paiement->stripe_payment_intent_id = $paymentMethodId ?: null;
            $paiement->save();

            $reservation->update(['statut' => 'confirmed']);
            return true;
        });
    }
}
