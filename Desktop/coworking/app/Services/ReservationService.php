<?php

namespace App\Services;

use App\Models\{Reservation, EspaceCoworking, CodePromo};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /** Minutes the user has to choose a payment method after landing on /payment */
    const CHOOSE_METHOD_MINUTES = 15;

    /** Hours before a cash pending reservation expires (must show up & pay within this window) */
    const CASH_HOLD_HOURS = 24;

    public function __construct(private StripeService $stripeService) {}

    /**
     * Create a new reservation with all business logic
     */
    public function create(array $data, int $userId): Reservation
    {
        return DB::transaction(function () use ($data, $userId) {
            $pricing = $this->calculatePricing($data);

            $reservation = Reservation::create([
                'id_user'          => $userId,
                'id_espace'        => $data['id_espace'],
                'date_debut'       => $data['date_debut'],
                'date_fin'         => $data['date_fin'],
                'heure_debut'      => $data['heure_debut'] ?? '08:00',
                'heure_fin'        => $data['heure_fin'] ?? '18:00',
                'type_reservation' => $data['type_reservation'] ?? 'daily',
                'notes'            => $data['notes'] ?? null,
                'prix_total'       => $pricing['prix_total'],
                'remise_montant'   => $pricing['remise'],
                'prix_final'       => $pricing['prix_final'],
                'id_code'          => $pricing['id_code'] ?? null,
                'statut'           => 'pending',
                // Abandoned-hold deadline: 30 min to even reach the payment page
                'expires_at'       => Carbon::now()->addMinutes(self::CHOOSE_METHOD_MINUTES),
            ]);

            if ($pricing['id_code']) {
                CodePromo::where('id_code', $pricing['id_code'])->increment('usage_count');
            }


            return $reservation->load(['espace', 'codePromo']);
        });
    }

    /**
     * Calculate total pricing for a reservation
     */
    public function calculatePricing(array $data): array
    {
        $espace = EspaceCoworking::findOrFail($data['id_espace']);
        $dateDebut = Carbon::parse($data['date_debut']);
        $dateFin = Carbon::parse($data['date_fin']);

        $typeReservation = $data['type_reservation'] ?? 'daily';

        if ($typeReservation === 'hourly') {
            // Hourly: user picks ONE day + start/end time
            $heureDebut = Carbon::parse($data['heure_debut']);
            $heureFin = Carbon::parse($data['heure_fin']);
            $hours = max(1, $heureDebut->diffInHours($heureFin));
            $prixBase = $hours * $espace->prix_heure;
            $days = 1;
        } else {
            $days = $dateDebut->diffInDays($dateFin) + 1;
            $prixBase = $days * $espace->prix_jour;
        }

        $prixTotal = $prixBase;
        $remise = 0;
        $idCode = null;

        if (!empty($data['code_promo'])) {
            $code = CodePromo::where('code', $data['code_promo'])->first();
            if ($code && $code->isValid($prixTotal)) {
                $remise = $code->calculateDiscount($prixTotal);
                $idCode = $code->id_code;
            }
        }

        return [
            'prix_total' => $prixTotal,
            'remise'     => $remise,
            'prix_final' => max(0, $prixTotal - $remise),
            'id_code'    => $idCode,
            'days'       => $days,
            'prix_base'  => $prixBase,
        ];
    }

    /**
     * Check availability
     */
    public function checkAvailability(int $espaceId, string $dateDebut, string $dateFin, ?int $excludeId = null): array
    {
        $espace = EspaceCoworking::findOrFail($espaceId);
        $available = $espace->isAvailable($dateDebut, $dateFin, null, null, $excludeId);

        if ($available) {
            return ['available' => true];
        }

        $suggestion = $this->findNextAvailableSlot($espaceId, $dateDebut, $dateFin);
        return ['available' => false, 'suggestion' => $suggestion];
    }

    private function findNextAvailableSlot(int $espaceId, string $dateDebut, string $dateFin): ?array
    {
        $duration = Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin));
        $checkDate = Carbon::parse($dateFin)->addDay();

        for ($i = 0; $i < 30; $i++) {
            $checkEnd = $checkDate->copy()->addDays($duration);
            $espace = EspaceCoworking::find($espaceId);
            if ($espace && $espace->isAvailable($checkDate->toDateString(), $checkEnd->toDateString())) {
                return ['date_debut' => $checkDate->toDateString(), 'date_fin' => $checkEnd->toDateString()];
            }
            $checkDate->addDay();
        }

        return null;
    }

    public function confirm(Reservation $reservation): void
    {
        $reservation->update(['statut' => 'confirmed']);


    }

}