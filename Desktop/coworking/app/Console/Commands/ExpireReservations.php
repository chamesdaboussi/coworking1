<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireReservations extends Command
{
    protected $signature   = 'reservations:expire';
    protected $description = 'Expire pending online reservations after 30 min and pending cash reservations after 24 h';

    public function handle(): int
    {
        $now = Carbon::now();

        // ── Online (Stripe) reservations: hard 30-minute deadline ──────────
        $expiredOnline = Reservation::where('statut', 'pending')
            ->where('payment_method', 'stripe')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->get();

        foreach ($expiredOnline as $reservation) {
            $reservation->update([
                'statut'     => 'expired',
                'expires_at' => $reservation->expires_at,
            ]);
            $reservation->paiements()->where('statut', 'pending')->update(['statut' => 'failed']);
            $this->info("[Online] Expired reservation #{$reservation->id_reservation} ({$reservation->code_confirmation})");
        }

        // ── Cash reservations: expire if no admin validation within 24 h ──
        // Logic: the user has until end-of-day on their reservation start date
        // to show up and pay. After 24 h from booking (expires_at), it expires.
        $expiredCash = Reservation::where('statut', 'pending')
            ->where('payment_method', 'cash')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->get();

        foreach ($expiredCash as $reservation) {
            $reservation->update(['statut' => 'expired']);
            $reservation->paiements()->where('statut', 'pending')->update(['statut' => 'failed']);
            $this->info("[Cash] Expired reservation #{$reservation->id_reservation} ({$reservation->code_confirmation})");
        }

        // ── Pending without any payment method chosen: expire after 30 min ─
        // Covers reservations created but where user never reached payment page
        $expiredAbandoned = Reservation::where('statut', 'pending')
            ->whereNull('payment_method')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->get();

        foreach ($expiredAbandoned as $reservation) {
            $reservation->update(['statut' => 'expired']);
            $reservation->paiements()->where('statut', 'pending')->update(['statut' => 'failed']);
            $this->info("[Abandoned] Expired reservation #{$reservation->id_reservation} ({$reservation->code_confirmation})");
        }

        $total = $expiredOnline->count() + $expiredCash->count() + $expiredAbandoned->count();
        $this->info("Done. {$total} reservation(s) expired.");

        return Command::SUCCESS;
    }
}
