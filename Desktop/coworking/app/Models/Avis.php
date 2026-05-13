<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    protected $table = 'avis';
    protected $primaryKey = 'id_avis';

    protected $fillable = [
        'id_user',
        'id_espace',
        'id_reservation',
        'note',
        'commentaire',
    ];

    protected function casts(): array
    {
        return [
            'note' => 'integer',
        ];
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_user', 'id_user');
    }

    public function espace()
    {
        return $this->belongsTo(EspaceCoworking::class, 'id_espace', 'id_espace');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'id_reservation', 'id_reservation');
    }

    public function getStarsHtmlAttribute(): string
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= $i <= $this->note ? '★' : '☆';
        }
        return $html;
    }
}
