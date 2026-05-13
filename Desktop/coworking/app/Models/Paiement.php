<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $primaryKey = 'id_paiement';

    protected $fillable = [
        'id_reservation',
        'montant',
        'statut',
        'methode',
        'stripe_payment_intent_id',
        'paid_at',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'id_reservation');
    }
public function isPaid(): bool
    {
        return $this->statut === 'paid';
    }
}
