<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EspaceCoworking extends Model
{
    use HasFactory;

    protected $table = 'espace_coworking';
    protected $primaryKey = 'id_espace';

    protected $fillable = [
        'nom',
        'adresse',
        'latitude',
        'longitude',
        'prix_heure',
        'prix_jour',
        'capacite',
        'description',
        'amenities',
        'image',
        'disponible',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'disponible' => 'boolean',
            'prix_heure' => 'decimal:2',
            'prix_jour' => 'decimal:2',
        ];
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'id_espace', 'id_espace');
    }

    public function avis()
    {
        return $this->hasMany(Avis::class, 'id_espace', 'id_espace');
    }

    public function getAvgNoteAttribute(): float
    {
        return round($this->avis()->avg('note') ?? 0, 1);
    }

    public function getAvisCountAttribute(): int
    {
        return $this->avis()->count();
    }

    public function isAvailable(string $dateDebut, string $dateFin, ?string $heureDebut = null, ?string $heureFin = null, ?int $excludeReservationId = null): bool
    {
        if (!$this->disponible) return false;


        // Check existing reservations
        $query = $this->reservations()
            ->whereIn('statut', ['pending', 'confirmed'])  // exclude expired/cancelled
            ->where('date_debut', '<=', $dateFin)
            ->where('date_fin', '>=', $dateDebut);

        if ($excludeReservationId) {
            $query->where('id_reservation', '!=', $excludeReservationId);
        }

        return !$query->exists();
    }

    public function getTypeLibelleAttribute(): string
    {
        return match($this->type) {
            'bureau_prive' => 'Bureau Privé',
            'espace_ouvert' => 'Espace Ouvert',
            'salle_reunion' => 'Salle de Réunion',
            'studio' => 'Studio',
            default => $this->type,
        };
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return route('storage.public', ['path' => $this->image]);
        }
        return asset('images/default-space.jpg');
    }
}
