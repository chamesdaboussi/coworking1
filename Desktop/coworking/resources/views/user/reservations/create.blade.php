@extends('layouts.user')
@section('title', 'Nouvelle Réservation')
@section('page-title', 'Nouvelle Réservation')

@push('styles')
<style>
/* Step indicators */
.step-bar { display: flex; align-items: center; margin-bottom: 32px; }
.step-item { display: flex; flex-direction: column; align-items: center; position: relative; flex: 1; }
.step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 18px; left: calc(50% + 20px);
    width: calc(100% - 40px); height: 2px;
    background: var(--border);
    transition: background 0.3s;
}
.step-item.done:not(:last-child)::after { background: var(--accent); }
.step-circle {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 14px;
    border: 2px solid var(--border); background: var(--surface2);
    color: var(--text-muted); transition: all 0.25s; flex-shrink: 0;
    position: relative; z-index: 2;
}
.step-item.active .step-circle { border-color: var(--accent); background: var(--accent); color: white; box-shadow: 0 0 0 4px rgba(79,126,247,0.15); }
.step-item.done .step-circle { border-color: var(--accent); background: var(--accent); color: white; }
.step-label { font-size: 11px; font-weight: 600; color: var(--text-muted); margin-top: 6px; text-transform: uppercase; letter-spacing: 0.06em; text-align: center; }
.step-item.active .step-label, .step-item.done .step-label { color: var(--accent); }

/* Space cards */
.space-card {
    background: var(--surface2);
    border: 2px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}
.space-card:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(79,126,247,0.12); }
.space-card.selected { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(79,126,247,0.15); }
.space-card .check-overlay {
    position: absolute; top: 10px; right: 10px;
    width: 26px; height: 26px; border-radius: 50%;
    background: var(--accent); display: flex; align-items: center; justify-content: center;
    opacity: 0; transform: scale(0.6); transition: all 0.2s;
}
.space-card.selected .check-overlay { opacity: 1; transform: scale(1); }

/* Type badge */
.type-badge {
    display: inline-flex; align-items: center;
    background: rgba(79,126,247,0.1); color: var(--accent);
    padding: 3px 9px; border-radius: 20px;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
}

/* Navigation buttons */
.step-nav { display: flex; justify-content: space-between; align-items: center; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border); }

/* Summary sidebar */
.summary-card { background: var(--surface2); border: 1px solid var(--border); border-radius: 14px; padding: 20px; position: sticky; top: 72px; }
.summary-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border); font-size: 14px; }
.summary-row:last-child { border-bottom: none; }

/* Date picker highlight */
input[type="date"]:focus, input[type="time"]:focus { outline: none; }

/* Amenity tag */
.amenity-tag { display: inline-flex; align-items: center; gap: 5px; background: var(--surface3); border: 1px solid var(--border); border-radius: 6px; padding: 4px 9px; font-size: 12px; color: var(--text-muted); }
</style>
@endpush

@section('content')
<div x-data="bookingWizard()" x-init="init()" class="fade-in">

    <!-- Step bar -->
    <div class="step-bar">
        <div class="step-item" :class="{ active: step === 1, done: step > 1 }">
            <div class="step-circle">
                <template x-if="step > 1"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></template>
                <template x-if="step <= 1"><span>1</span></template>
            </div>
            <div class="step-label">Espace</div>
        </div>
        <div class="step-item" :class="{ active: step === 2, done: step > 2 }">
            <div class="step-circle">
                <template x-if="step > 2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></template>
                <template x-if="step <= 2"><span>2</span></template>
            </div>
            <div class="step-label">Dates</div>
        </div>
        <div class="step-item" :class="{ active: step === 3, done: step > 3 }">
            <div class="step-circle">
                <template x-if="step > 3"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></template>
                <template x-if="step <= 3"><span>3</span></template>
            </div>
            <div class="step-label">Options</div>
        </div>
        <div class="step-item" :class="{ active: step === 4 }">
            <div class="step-circle"><span>4</span></div>
            <div class="step-label">Confirmation</div>
        </div>
    </div>

    <div :style="step === 1 ? 'display: grid; grid-template-columns: 1fr; gap: 24px; align-items: start;' : 'display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;'">

        <!-- Main content -->
        <div>

            <!-- ========== STEP 1: Choose Space ========== -->
            <div x-show="step === 1" x-transition:enter="fade-in">
                <div class="card" style="margin-bottom: 0;">
                    <div style="margin-bottom: 20px;">
                        <h2 style="font-family: 'Poppins', sans-serif; font-size: 20px; font-weight: 800; margin-bottom: 4px;">Choisissez votre espace</h2>
                        <p style="color: var(--text-muted); font-size: 14px;">Sélectionnez l'espace qui correspond à vos besoins.</p>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                        <template x-for="espace in espaces" :key="espace.id">
                            <div class="space-card" :class="{ selected: selectedId === espace.id }" @click="selectEspace(espace)">
                                <!-- Image -->
                                <div style="height: 160px; background: var(--surface3); overflow: hidden; position: relative;">
                                    <template x-if="espace.image">
                                        <img :src="espace.image" :alt="espace.nom" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                    </template>
                                    <template x-if="!espace.image">
                                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(79,126,247,0.08), rgba(124,95,207,0.08));">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(79,126,247,0.3)" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                        </div>
                                    </template>
                                    <!-- Type badge overlay -->
                                    <div style="position: absolute; bottom: 10px; left: 10px;">
                                        <span class="type-badge" x-text="espace.type_libelle" style="backdrop-filter: blur(8px); background: rgba(255,255,255,0.9); color: var(--accent);"></span>
                                    </div>
                                    <!-- Check overlay -->
                                    <div class="check-overlay">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div style="padding: 14px;">
                                    <div style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 15px; margin-bottom: 4px;" x-text="espace.nom"></div>
                                    <div style="display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--text-muted); margin-bottom: 10px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span x-text="espace.adresse.length > 40 ? espace.adresse.substring(0,40) + '...' : espace.adresse"></span>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div>
                                            <span style="font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 16px; color: var(--accent);" x-text="espace.prix_jour.toFixed(3) + ' DT'"></span>
                                            <span style="font-size: 11px; color: var(--text-muted);">/jour</span>
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-muted);">
                                            <span x-text="espace.prix_heure.toFixed(3) + ' DT/h'"></span>
                                        </div>
                                    </div>
                                    <!-- Capacite -->
                                    <div style="display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--text-muted); margin-top: 8px; padding-top: 8px; border-top: 1px solid var(--border);">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                        <span x-text="'Capacité : ' + espace.capacite + ' personne(s)'"></span>
                                    </div>
                                    <!-- Amenities -->
                                    <div x-show="espace.amenities && espace.amenities.length > 0" style="margin-top: 10px;">
                                        <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 7px;">Équipements</div>
                                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                            <template x-for="a in espace.amenities.slice(0, 4)" :key="a">
                                                <span class="amenity-tag" x-text="a"></span>
                                            </template>
                                            <span class="amenity-tag" x-show="espace.amenities.length > 4" x-text="'+' + (espace.amenities.length - 4)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="step-nav">
                        <div style="color: var(--text-muted); font-size: 13px;">Cliquez sur un espace pour passer directement aux dates.</div>
                    </div>
                </div>
            </div>

            <!-- ========== STEP 2: Dates ========== -->
            <div x-show="step === 2" x-transition:enter="fade-in">
                <div class="card">
                    <!-- Selected space recap -->
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: var(--surface3); border-radius: 10px; margin-bottom: 22px; border: 1px solid var(--border);">
                        <div style="width: 44px; height: 44px; border-radius: 8px; overflow: hidden; flex-shrink: 0; background: var(--border);">
                            <template x-if="selectedEspace && selectedEspace.image">
                                <img :src="selectedEspace.image" style="width: 100%; height: 100%; object-fit: cover;">
                            </template>
                            <template x-if="!selectedEspace || !selectedEspace.image">
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(79,126,247,0.1);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                </div>
                            </template>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; font-size: 14px;" x-text="selectedEspace ? selectedEspace.nom : ''"></div>
                            <div style="font-size: 12px; color: var(--text-muted);" x-text="selectedEspace ? selectedEspace.adresse : ''"></div>
                        </div>
                        <button @click="goToStep(1)" class="btn btn-outline btn-sm">Changer</button>
                    </div>

                    <h2 style="font-family: 'Poppins', sans-serif; font-size: 18px; font-weight: 800; margin-bottom: 4px;">Quand souhaitez-vous réserver ?</h2>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 22px;">Choisissez le type, les dates et les horaires.</p>

                    <!-- Booking type toggle -->
                    <div style="display: flex; gap: 12px; margin-bottom: 22px;">
                        <label style="flex: 1; cursor: pointer;">
                            <input type="radio" value="daily" x-model="typeReservation" style="display:none;" @change="if(typeReservation==='daily'){dateFin=dateFin||dateDebut;} calculatePrice()">
                            <div :style="typeReservation === 'daily' ? 'border-color: var(--accent); background: rgba(79,126,247,0.06);' : ''"
                                 style="padding: 14px 16px; border: 2px solid var(--border); border-radius: 12px; transition: all 0.15s; display: flex; align-items: center; gap: 12px;">
                                <div style="width: 38px; height: 38px; border-radius: 9px; background: rgba(79,126,247,0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 14px;">Par journée</div>
                                    <div style="font-size: 12px; color: var(--text-muted);" x-text="selectedEspace ? selectedEspace.prix_jour.toFixed(3) + ' DT / jour' : 'Prix journalier'"></div>
                                </div>
                            </div>
                        </label>
                        <label style="flex: 1; cursor: pointer;">
                            <input type="radio" value="hourly" x-model="typeReservation" style="display:none;" @change="if(dateDebut){dateFin=dateDebut;} calculatePrice()">
                            <div :style="typeReservation === 'hourly' ? 'border-color: var(--accent); background: rgba(79,126,247,0.06);' : ''"
                                 style="padding: 14px 16px; border: 2px solid var(--border); border-radius: 12px; transition: all 0.15s; display: flex; align-items: center; gap: 12px;">
                                <div style="width: 38px; height: 38px; border-radius: 9px; background: rgba(79,126,247,0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 14px;">Par heure</div>
                                    <div style="font-size: 12px; color: var(--text-muted);" x-text="selectedEspace ? selectedEspace.prix_heure.toFixed(3) + ' DT / heure' : 'Prix horaire'"></div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Dates -->
                    <div x-show="typeReservation === 'daily'" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                        <div>
                            <label class="form-label">Date de début *</label>
                            <input type="date" id="input_date_debut" class="form-input" x-model="dateDebut" :min="today" @change="onDateChange(); clearFieldErr('date_debut')">
                            <small id="err_date_debut" style="color:#ef4444;display:none;font-size:12px;margin-top:5px;font-weight:600;">⚠ Veuillez choisir une date de début.</small>
                        </div>
                        <div>
                            <label class="form-label">Date de fin *</label>
                            <input type="date" id="input_date_fin" class="form-input" x-model="dateFin" :min="dateDebut || today" @change="onDateChange(); clearFieldErr('date_fin')">
                            <small id="err_date_fin" style="color:#ef4444;display:none;font-size:12px;margin-top:5px;font-weight:600;">⚠ Veuillez choisir une date de fin.</small>
                        </div>
                    </div>

                    <!-- Hourly: single day + time -->
                    <div x-show="typeReservation === 'hourly'" style="margin-bottom: 18px;">
                        <div style="margin-bottom: 16px;">
                            <label class="form-label">Date *</label>
                            <input type="date" id="input_date_heure" class="form-input" x-model="dateDebut" :min="today" @change="onDateChange(); clearFieldErr('date_heure')">
                            <small id="err_date_heure" style="color:#ef4444;display:none;font-size:12px;margin-top:5px;font-weight:600;">⚠ Veuillez choisir une date.</small>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label class="form-label">Heure de début *</label>
                                <input type="time" id="input_heure_debut" class="form-input" x-model="heureDebut" min="06:00" max="22:00" @change="calculatePrice(); clearFieldErr('heure_debut')">
                                <small id="err_heure_debut" style="color:#ef4444;display:none;font-size:12px;margin-top:5px;font-weight:600;">⚠ Veuillez saisir l'heure de début.</small>
                            </div>
                            <div>
                                <label class="form-label">Heure de fin *</label>
                                <input type="time" id="input_heure_fin" class="form-input" x-model="heureFin" min="06:00" max="23:00" @change="calculatePrice(); clearFieldErr('heure_fin')">
                                <small id="err_heure_fin" style="color:#ef4444;display:none;font-size:12px;margin-top:5px;font-weight:600;">⚠ Veuillez saisir l'heure de fin.</small>
                            </div>
                        </div>
                        <div x-show="heureDebut && heureFin && heureFin > heureDebut" style="margin-top: 10px; padding: 8px 12px; background: rgba(79,126,247,0.07); border-radius: 8px; font-size: 13px; color: var(--accent); font-weight: 600;">
                            ⏱ Durée : <span x-text="getHourDuration()"></span>
                        </div>
                    </div>

                    <!-- Availability status -->
                    <div x-show="availabilityStatus !== ''" class="alert"
                         :class="availabilityStatus === 'available' ? 'alert-success' : availabilityStatus === 'unavailable' ? 'alert-error' : 'alert-info'">
                        <template x-if="availabilityStatus === 'available'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                        </template>
                        <template x-if="availabilityStatus === 'unavailable'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        </template>
                        <template x-if="availabilityStatus === 'checking'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </template>
                        <span x-text="availabilityMessage"></span>
                    </div>

                    <div class="step-nav">
                        <button class="btn btn-outline" @click="goToStep(1)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            Retour
                        </button>
                        <button class="btn btn-primary" @click="goToStep(3)">
                            Suivant : Options
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ========== STEP 3: Options ========== -->
            <div x-show="step === 3" x-transition:enter="fade-in">
                <div class="card">
                    <h2 style="font-family: 'Poppins', sans-serif; font-size: 18px; font-weight: 800; margin-bottom: 4px;">Options & Code promo</h2>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 22px;">Ajoutez un code promo ou des notes si besoin.</p>

                    <!-- Promo code -->
                    <div style="margin-bottom: 22px;">
                        <label class="form-label">Code promo</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" class="form-input" x-model="promoCode" placeholder="Ex: WELCOME10" style="flex: 1; text-transform: uppercase;">
                            <button type="button" class="btn btn-outline" @click="validatePromo()" style="white-space: nowrap;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                Appliquer
                            </button>
                        </div>
                        <div x-show="promoMessage !== ''" class="alert"
                             :class="promoValid ? 'alert-success' : 'alert-error'"
                             style="margin-top: 10px; padding: 9px 12px;">
                            <template x-if="promoValid">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                            </template>
                            <template x-if="!promoValid">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            </template>
                            <span x-text="promoMessage"></span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div style="margin-bottom: 22px;">
                        <label class="form-label">Notes <span style="font-weight: 400; color: var(--text-muted); text-transform: none;">(optionnel)</span></label>
                        <textarea class="form-input" x-model="notes" rows="3" placeholder="Informations supplémentaires pour l'équipe..."></textarea>
                    </div>

                    <!-- Space amenities reminder -->
                    <div x-show="selectedEspace && selectedEspace.amenities && selectedEspace.amenities.length > 0"
                         style="padding: 14px; background: var(--surface3); border-radius: 10px; border: 1px solid var(--border);">
                        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 10px;">Équipements inclus</div>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            <template x-for="a in (selectedEspace ? selectedEspace.amenities : [])" :key="a">
                                <span class="amenity-tag">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span x-text="a"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                    <div class="step-nav">
                        <button class="btn btn-outline" @click="goToStep(2)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            Retour
                        </button>
                        <button class="btn btn-primary" @click="goToStep(4)">
                            Confirmer et payer
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ========== STEP 4: Confirmation ========== -->
            <div x-show="step === 4" x-transition:enter="fade-in">
                <div class="card">
                    <h2 style="font-family: 'Poppins', sans-serif; font-size: 18px; font-weight: 800; margin-bottom: 4px;">Récapitulatif final</h2>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 22px;">Vérifiez votre réservation avant de procéder au paiement.</p>

                    <!-- Space info -->
                    <div style="display: flex; gap: 14px; padding: 14px; background: var(--surface3); border-radius: 12px; border: 1px solid var(--border); margin-bottom: 18px;">
                        <div style="width: 80px; height: 60px; border-radius: 8px; overflow: hidden; flex-shrink: 0; background: var(--border);">
                            <template x-if="selectedEspace && selectedEspace.image">
                                <img :src="selectedEspace.image" style="width: 100%; height: 100%; object-fit: cover;">
                            </template>
                        </div>
                        <div>
                            <div style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 15px;" x-text="selectedEspace ? selectedEspace.nom : ''"></div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;" x-text="selectedEspace ? selectedEspace.adresse : ''"></div>
                        </div>
                    </div>

                    <table style="width: 100%; font-size: 14px; border-collapse: collapse; margin-bottom: 18px;">
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 10px 0; color: var(--text-muted);">Type</td>
                            <td style="padding: 10px 0; font-weight: 600; text-align: right;" x-text="typeReservation === 'daily' ? 'Journalier' : 'Horaire'"></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 10px 0; color: var(--text-muted);" x-text="typeReservation === 'hourly' ? 'Date et horaire' : 'Dates'"></td>
                            <td style="padding: 10px 0; font-weight: 600; text-align: right;" x-text="getPeriodText()"></td>
                        </tr>
                        <template x-if="pricing">
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 10px 0; color: var(--text-muted);">Prix de base</td>
                                <td style="padding: 10px 0; font-weight: 600; text-align: right;" x-text="formatPrice(pricing.prix_base)"></td>
                            </tr>
                        </template>
                        <template x-if="pricing && pricing.remise > 0">
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 10px 0; color: var(--success);">Code promo</td>
                                <td style="padding: 10px 0; font-weight: 600; text-align: right; color: var(--success);" x-text="'- ' + formatPrice(pricing.remise)"></td>
                            </tr>
                        </template>
                        <tr>
                            <td style="padding: 14px 0 0; font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 16px;">Total à payer</td>
                            <td style="padding: 14px 0 0; font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 22px; color: var(--accent); text-align: right;" x-text="pricing ? formatPrice(pricing.prix_final) : '—'"></td>
                        </tr>
                    </table>

                    <!-- Submit form -->
                    <form method="POST" action="{{ route('reservations.store') }}" id="booking-form">
                        @csrf
                        <input type="hidden" name="id_espace" x-bind:value="selectedId">
                        <input type="hidden" name="date_debut" x-bind:value="dateDebut">
                        <input type="hidden" name="date_fin" x-bind:value="dateFin">
                        <input type="hidden" name="heure_debut" x-bind:value="heureDebut">
                        <input type="hidden" name="heure_fin" x-bind:value="heureFin">
                        <input type="hidden" name="type_reservation" x-bind:value="typeReservation">
                        <input type="hidden" name="notes" x-bind:value="notes">
                        <input type="hidden" name="code_promo" x-bind:value="promoValid ? promoCode : ''">

                        <div class="step-nav">
                            <button type="button" class="btn btn-outline" @click="goToStep(3)">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                                Retour
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg" style="gap: 10px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                Procéder au paiement
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- ========== SUMMARY SIDEBAR ========== -->
        <div class="summary-card" x-show="step !== 1" x-transition>
            <div style="font-family: 'Poppins', sans-serif; font-size: 15px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Votre réservation
            </div>

            <!-- Space -->
            <div class="summary-row">
                <span style="color: var(--text-muted);">Espace</span>
                <span style="font-weight: 600; font-size: 13px; text-align: right; max-width: 160px;" x-text="selectedEspace ? selectedEspace.nom : 'Non sélectionné'" :style="!selectedEspace ? 'color: var(--text-muted); font-style: italic; font-weight: 400;' : ''"></span>
            </div>

            <!-- Type -->
            <div class="summary-row">
                <span style="color: var(--text-muted);">Type</span>
                <span style="font-weight: 500; font-size: 13px;" x-text="typeReservation === 'daily' ? 'Journalier' : 'Horaire'"></span>
            </div>

            <!-- Dates / horaires -->
            <template x-if="typeReservation === 'daily'">
                <div>
                    <div class="summary-row">
                        <span style="color: var(--text-muted);">Arrivée</span>
                        <span style="font-weight: 500; font-size: 13px;" x-text="dateDebut ? formatDate(dateDebut) : '—'"></span>
                    </div>
                    <div class="summary-row">
                        <span style="color: var(--text-muted);">Départ</span>
                        <span style="font-weight: 500; font-size: 13px;" x-text="dateFin ? formatDate(dateFin) : '—'"></span>
                    </div>
                </div>
            </template>
            <template x-if="typeReservation === 'hourly'">
                <div>
                    <div class="summary-row">
                        <span style="color: var(--text-muted);">Date</span>
                        <span style="font-weight: 500; font-size: 13px;" x-text="dateDebut ? formatDate(dateDebut) : '—'"></span>
                    </div>
                    <div class="summary-row">
                        <span style="color: var(--text-muted);">Horaire</span>
                        <span style="font-weight: 500; font-size: 13px;" x-text="heureDebut && heureFin ? heureDebut + ' → ' + heureFin : '—'"></span>
                    </div>
                </div>
            </template>

            <!-- Duration -->
            <div class="summary-row" x-show="dateDebut && dateFin">
                <span style="color: var(--text-muted);">Durée</span>
                <span style="font-weight: 500; font-size: 13px;" x-text="getDuration()"></span>
            </div>

            <!-- Pricing -->
            <div x-show="pricing" style="margin-top: 14px; padding-top: 14px; border-top: 2px solid var(--border);">
                <div class="summary-row" x-show="pricing && pricing.prix_base !== pricing.prix_final">
                    <span style="color: var(--text-muted);">Sous-total</span>
                    <span style="font-size: 13px;" x-text="pricing ? formatPrice(pricing.prix_base) : ''"></span>
                </div>
                <div class="summary-row" x-show="pricing && pricing.remise > 0" style="color: var(--success);">
                    <span>Réduction</span>
                    <span style="font-weight: 600;" x-text="pricing ? '- ' + formatPrice(pricing.remise) : ''"></span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px;">
                    <span style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 700;">Total</span>
                    <span style="font-family: 'Poppins', sans-serif; font-size: 22px; font-weight: 800; color: var(--accent);" x-text="pricing ? formatPrice(pricing.prix_final) : '—'"></span>
                </div>
            </div>

            <!-- Step progress indicator -->
            <div style="margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--border);">
                <div style="display: flex; gap: 6px;">
                    <template x-for="s in [1,2,3,4]" :key="s">
                        <div style="flex: 1; height: 4px; border-radius: 2px; transition: background 0.3s;"
                             :style="s <= step ? 'background: var(--accent);' : 'background: var(--border);'"></div>
                    </template>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 8px; text-align: center;">
                    Étape <span x-text="step"></span> sur 4
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function bookingWizard() {
    return {
        step: 1,
        espaces: @json($espacesJs),
        selectedId: {{ old('id_espace', $selectedEspace?->id_espace ?? 'null') }},
        selectedEspace: null,
        typeReservation: '{{ old('type_reservation', 'daily') }}',
        dateDebut: '{{ old('date_debut', request('date_debut', '')) }}',
        dateFin: '{{ old('date_fin', request('date_fin', request('date_debut', ''))) }}',
        heureDebut: '08:00',
        heureFin: '18:00',
        promoCode: '',
        promoValid: false,
        promoMessage: '',
        pricing: null,
        availabilityStatus: '',
        availabilityMessage: '',
        notes: '',
        today: new Date().toISOString().split('T')[0],

        init() {
            if (this.selectedId) {
                this.selectedEspace = this.espaces.find(e => e.id == this.selectedId) || null;
                this.step = 2;
                if (this.dateDebut && this.dateFin) {
                    this.calculatePrice();
                }
            }
        },

        selectEspace(espace) {
            this.selectedId = espace.id;
            this.selectedEspace = espace;
            this.pricing = null;
            this.availabilityStatus = '';
            this.goToStep(2);
        },

        goToStep(s) {
            // ── Step 1 → 2: espace must be selected ──────────────────
            if (s === 2 && !this.selectedId) {
                this._showStepError('Veuillez sélectionner un espace avant de continuer.');
                return;
            }

            // ── Step 2 → 3: dates + times required and valid ──────────
            if (s > 2) {
                let hasError = false;

                if (this.typeReservation === 'daily') {
                    // Date début
                    if (!this.dateDebut) {
                        this._showFieldErr('date_debut', 'Veuillez choisir une date de début.');
                        hasError = true;
                    } else {
                        this._clearFieldErr('date_debut');
                    }

                    // Date fin
                    if (!this.dateFin) {
                        this._showFieldErr('date_fin', 'Veuillez choisir une date de fin.');
                        hasError = true;
                    } else if (this.dateDebut && new Date(this.dateFin) < new Date(this.dateDebut)) {
                        this._showFieldErr('date_fin', 'La date de fin ne peut pas être avant la date de début.');
                        hasError = true;
                    } else {
                        this._clearFieldErr('date_fin');
                    }

                    // Dates not in the past
                    if (this.dateDebut && new Date(this.dateDebut) < new Date(this.today)) {
                        this._showFieldErr('date_debut', 'La date de début ne peut pas être dans le passé.');
                        hasError = true;
                    }

                } else {
                    // Hourly mode

                    // Date
                    if (!this.dateDebut) {
                        this._showFieldErr('date_heure', 'Veuillez choisir une date.');
                        hasError = true;
                    } else if (new Date(this.dateDebut) < new Date(this.today)) {
                        this._showFieldErr('date_heure', 'La date ne peut pas être dans le passé.');
                        hasError = true;
                    } else {
                        this._clearFieldErr('date_heure');
                    }

                    // Heure début
                    if (!this.heureDebut) {
                        this._showFieldErr('heure_debut', "Veuillez saisir l'heure de début.");
                        hasError = true;
                    } else {
                        this._clearFieldErr('heure_debut');
                    }

                    // Heure fin
                    if (!this.heureFin) {
                        this._showFieldErr('heure_fin', "Veuillez saisir l'heure de fin.");
                        hasError = true;
                    } else if (this.heureDebut && this.heureFin <= this.heureDebut) {
                        this._showFieldErr('heure_fin', "L'heure de fin doit être après l'heure de début.");
                        hasError = true;
                    } else {
                        this._clearFieldErr('heure_fin');
                    }

                    // Minimum duration 30 min
                    if (this.heureDebut && this.heureFin && this.heureFin > this.heureDebut) {
                        const [h1,m1] = this.heureDebut.split(':').map(Number);
                        const [h2,m2] = this.heureFin.split(':').map(Number);
                        const diff = (h2*60+m2) - (h1*60+m1);
                        if (diff < 30) {
                            this._showFieldErr('heure_fin', 'La réservation doit durer au moins 30 minutes.');
                            hasError = true;
                        }
                    }
                }

                // Availability check
                if (!hasError && this.availabilityStatus === 'unavailable') {
                    this._showStepError("Cet espace n'est pas disponible pour les dates sélectionnées.");
                    return;
                }

                if (hasError) {
                    this._showStepError('Veuillez corriger les champs indiqués ci-dessus.');
                    return;
                }
            }

            this._clearStepError();
            this.step = s;
            if (s === 2 && this.dateDebut && this.dateFin) this.checkAvailability();
            if (s === 3 || s === 4) this.calculatePrice();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        _showFieldErr(fieldId, msg) {
            const errEl = document.getElementById('err_' + fieldId);
            const inpEl = document.getElementById('input_' + fieldId);
            if (errEl) { errEl.textContent = '⚠ ' + msg; errEl.style.display = 'block'; }
            if (inpEl) { inpEl.style.borderColor = '#ef4444'; inpEl.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)'; }
        },

        _clearFieldErr(fieldId) {
            const errEl = document.getElementById('err_' + fieldId);
            const inpEl = document.getElementById('input_' + fieldId);
            if (errEl) errEl.style.display = 'none';
            if (inpEl) { inpEl.style.borderColor = ''; inpEl.style.boxShadow = ''; }
        },

        clearFieldErr(fieldId) {
            this._clearFieldErr(fieldId);
        },

        _showStepError(msg) {
            let el = document.getElementById('stepError');
            if (!el) {
                el = document.createElement('div');
                el.id = 'stepError';
                el.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#ef4444;color:white;padding:12px 24px;border-radius:40px;font-size:14px;font-weight:600;box-shadow:0 8px 24px rgba(239,68,68,0.4);z-index:9999;';
                document.body.appendChild(el);
                const style = document.createElement('style');
                style.textContent = '@keyframes slideUp{from{opacity:0;transform:translateX(-50%) translateY(16px)}to{opacity:1;transform:translateX(-50%) translateY(0)}}';
                document.head.appendChild(style);
            }
            el.textContent = '⚠️ ' + msg;
            el.style.display = 'block';
            el.style.animation = 'none';
            void el.offsetWidth; // reflow
            el.style.animation = 'slideUp 0.2s ease';
            clearTimeout(this._errTimer);
            this._errTimer = setTimeout(() => { el.style.display = 'none'; }, 4000);
        },

        _clearStepError() {
            const el = document.getElementById('stepError');
            if (el) el.style.display = 'none';
        },

        onDateChange() {
            // For hourly reservations, we keep only one date: date_fin = date_debut.
            if (this.typeReservation === 'hourly') {
                this.dateFin = this.dateDebut;
            }
            if (this.dateDebut && this.dateFin) {
                this.checkAvailability();
                this.calculatePrice();
            }
        },

        async checkAvailability() {
            if (!this.selectedId || !this.dateDebut || !this.dateFin) return;
            this.availabilityStatus = 'checking';
            this.availabilityMessage = 'Vérification en cours...';
            try {
                const resp = await fetch('{{ route('api.availability') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ id_espace: this.selectedId, date_debut: this.dateDebut, date_fin: this.dateFin })
                });
                const data = await resp.json();
                this.availabilityStatus = data.available ? 'available' : 'unavailable';
                this.availabilityMessage = data.available
                    ? 'Disponible pour les dates sélectionnées.'
                    : "Cet espace n'est pas disponible pour ces dates.";
            } catch(e) { this.availabilityStatus = ''; }
        },

        async calculatePrice() {
            if (!this.selectedId || !this.dateDebut || !this.dateFin) return;
            try {
                const resp = await fetch('{{ route('api.price') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({
                        id_espace: this.selectedId,
                        date_debut: this.dateDebut,
                        date_fin: this.dateFin,
                        type_reservation: this.typeReservation,
                        heure_debut: this.heureDebut,
                        heure_fin: this.heureFin,
                        code_promo: this.promoValid ? this.promoCode : ''
                    })
                });
                const data = await resp.json();
                if (data.success) this.pricing = data.pricing;
            } catch(e) {}
        },

        async validatePromo() {
            if (!this.promoCode) return;
            try {
                const resp = await fetch('{{ route('api.promo') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ code: this.promoCode.toUpperCase(), montant: this.pricing?.prix_total || 0 })
                });
                const data = await resp.json();
                this.promoValid = data.valid;
                this.promoMessage = data.valid ? `Code appliqué : ${data.label}` : (data.message || 'Code invalide.');
                if (data.valid) this.calculatePrice();
            } catch(e) {}
        },

        formatDate(d) {
            if (!d) return '—';
            return new Date(d + 'T00:00:00').toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },

        formatPrice(p) {
            if (p === undefined || p === null) return '—';
            return parseFloat(p).toFixed(3) + ' DT';
        },

        getPeriodText() {
            if (!this.dateDebut) return '—';
            if (this.typeReservation === 'hourly') {
                return this.formatDate(this.dateDebut) + ' • ' + this.heureDebut + ' → ' + this.heureFin;
            }
            return this.formatDate(this.dateDebut) + ' → ' + this.formatDate(this.dateFin);
        },

        getDuration() {
            if (!this.dateDebut || !this.dateFin) return '—';
            if (this.typeReservation === 'hourly') return this.getHourDuration() || '—';
            const d1 = new Date(this.dateDebut + 'T00:00:00');
            const d2 = new Date(this.dateFin + 'T00:00:00');
            const days = Math.round((d2 - d1) / 86400000) + 1;
            return days + ' jour' + (days > 1 ? 's' : '');
        },

        getHourDuration() {
            if (!this.heureDebut || !this.heureFin) return '';
            const [h1, m1] = this.heureDebut.split(':').map(Number);
            const [h2, m2] = this.heureFin.split(':').map(Number);
            const totalMins = (h2 * 60 + m2) - (h1 * 60 + m1);
            if (totalMins <= 0) return 'Horaires invalides';
            const hours = Math.floor(totalMins / 60);
            const mins = totalMins % 60;
            return (hours > 0 ? hours + 'h' : '') + (mins > 0 ? mins + 'min' : '');
        },
    }
}
</script>
@endpush
@endsection
