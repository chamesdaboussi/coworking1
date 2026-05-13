<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';
    protected $primaryKey = 'id_reservation';

    protected $fillable = [
        'date_debut', 'date_fin', 'heure_debut', 'heure_fin',
        'type_reservation', 'statut', 'code_confirmation', 'notes',
        'prix_total', 'remise_montant', 'prix_final',
        'id_user', 'id_espace', 'id_code',
        'expires_at', 'payment_method', 'payment_switch_count', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'date_debut'     => 'date',
            'date_fin'       => 'date',
            'prix_total'     => 'decimal:2',
            'remise_montant' => 'decimal:2',
            'prix_final'     => 'decimal:2',
            'expires_at'            => 'datetime',
            'cancelled_at'          => 'datetime',
            'payment_switch_count'  => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($reservation) {
            if (!$reservation->code_confirmation) {
                $reservation->code_confirmation = self::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'CW-' . strtoupper(Str::random(8));
        } while (self::where('code_confirmation', $code)->exists());
        return $code;
    }

    // Relationships
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_user', 'id_user');
    }

    public function espace()
    {
        return $this->belongsTo(EspaceCoworking::class, 'id_espace', 'id_espace');
    }

    public function codePromo()
    {
        return $this->belongsTo(CodePromo::class, 'id_code', 'id_code');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'id_reservation', 'id_reservation');
    }

    public function latestPaiement()
    {
        return $this->hasOne(Paiement::class, 'id_reservation', 'id_reservation')
                    ->latestOfMany('id_paiement');
    }

    // Status helpers
    public function isPending(): bool    { return $this->statut === 'pending'; }
    public function isConfirmed(): bool  { return $this->statut === 'confirmed'; }
    public function isExpired(): bool    { return $this->statut === 'expired'; }
    public function isCancelled(): bool  { return $this->statut === 'cancelled'; }
    public function isActive(): bool     { return in_array($this->statut, ['pending', 'confirmed']); }

    /** Seconds remaining before online expiry (0 if already past) */
    public function getSecondsUntilExpiryAttribute(): int
    {
        if (!$this->expires_at || $this->payment_method !== 'stripe') return 0;
        return max(0, (int) now()->diffInSeconds($this->expires_at, false));
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->statut) {
            'pending'   => ['label' => 'En attente',  'color' => 'yellow'],
            'confirmed' => ['label' => 'Confirmée',   'color' => 'green'],
            'expired'   => ['label' => 'Expirée',     'color' => 'red'],
            'cancelled' => ['label' => 'Annulée',     'color' => 'gray'],
            default     => ['label' => $this->statut, 'color' => 'gray'],
        };
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->date_debut->diffInDays($this->date_fin) + 1;
    }

    public function getDurationHoursAttribute(): float
    {
        $debut = Carbon::parse($this->heure_debut);
        $fin   = Carbon::parse($this->heure_fin);
        return $debut->diffInMinutes($fin) / 60;
    }

    public function getFormattedHeureDebutAttribute(): string
    {
        return Carbon::parse($this->heure_debut)->format('H:i');
    }

    public function getFormattedHeureFinAttribute(): string
    {
        return Carbon::parse($this->heure_fin)->format('H:i');
    }

    public function getPeriodLabelAttribute(): string
    {
        if ($this->type_reservation === 'hourly') {
            return $this->date_debut->format('d/m/Y') . ' • ' . $this->formatted_heure_debut . ' → ' . $this->formatted_heure_fin;
        }

        return $this->date_debut->format('d/m/Y') . ' → ' . $this->date_fin->format('d/m/Y');
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->type_reservation === 'hourly') {
            $minutes = Carbon::parse($this->heure_debut)->diffInMinutes(Carbon::parse($this->heure_fin));
            $hours = intdiv($minutes, 60);
            $mins = $minutes % 60;

            if ($hours > 0 && $mins > 0) {
                return $hours . 'h' . str_pad((string) $mins, 2, '0', STR_PAD_LEFT);
            }

            if ($hours > 0) {
                return $hours . ' heure' . ($hours > 1 ? 's' : '');
            }

            return $mins . ' minute' . ($mins > 1 ? 's' : '');
        }

        return $this->duration_days . ' jour' . ($this->duration_days > 1 ? 's' : '');
    }

}
