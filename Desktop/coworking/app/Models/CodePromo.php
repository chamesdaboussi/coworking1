<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CodePromo extends Model
{
    protected $table = 'code_promos';
    protected $primaryKey = 'id_code';

    protected $fillable = [
        'code', 'type', 'valeur', 'valeur_min_commande',
        'date_expiration', 'usage_max', 'usage_count', 'actif',
    ];

    protected function casts(): array
    {
        return [
            'date_expiration' => 'date',
            'actif' => 'boolean',
            'valeur' => 'decimal:2',
            'valeur_min_commande' => 'decimal:2',
        ];
    }

    public function isValid(float $montant = 0): bool
    {
        if (!$this->actif) return false;
        if ($this->date_expiration && $this->date_expiration->isPast()) return false;
        if ($this->usage_max !== null && $this->usage_count >= $this->usage_max) return false;
        if ($montant < $this->valeur_min_commande) return false;
        return true;
    }

    public function calculateDiscount(float $montant): float
    {
        if ($this->type === 'percent') {
            return round($montant * ($this->valeur / 100), 2);
        }
        return min($this->valeur, $montant);
    }
}
