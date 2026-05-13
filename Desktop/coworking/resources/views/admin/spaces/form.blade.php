@extends('layouts.admin')
@section('title', $espace ? 'Modifier l\'espace' : 'Nouvel espace')
@section('page-title', $espace ? 'Modifier l\'espace' : 'Ajouter un espace')

@section('content')
<div class="fade-in" style="max-width: 800px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.spaces.index') }}" class="btn btn-outline btn-sm">← Retour aux espaces</a>
    </div>
    <div class="card">
        @if ($errors->any())
            <div style="margin-bottom:16px;padding:12px 14px;border-radius:12px;background:#fef2f2;color:#991b1b;font-size:13px;">
                <strong>Le formulaire contient une erreur.</strong> Vérifiez surtout le format et la taille de l'image.
            </div>
        @endif
        <form method="POST" action="{{ $espace ? route('admin.spaces.update', $espace->id_espace) : route('admin.spaces.store') }}" enctype="multipart/form-data" id="spaceForm" novalidate>
            @csrf
            @if($espace) @method('PUT') @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label class="form-label">Nom de l'espace *</label>
                    <input type="text" name="nom" value="{{ old('nom', $espace?->nom) }}" class="form-input nom-input" required>
                    <small class="error-message" style="color: #ef4444; display: none; font-size: 12px; margin-top: 4px;"></small>
                </div>
                <div>
                    <label class="form-label">Type d'espace *</label>
                    <select name="type" class="form-input type-input" required>
                        @foreach(['bureau_prive' => 'Bureau Privé', 'espace_ouvert' => 'Espace Ouvert', 'salle_reunion' => 'Salle de Réunion', 'studio' => 'Studio'] as $val => $label)
                        <option value="{{ $val }}" {{ old('type', $espace?->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small class="error-message" style="color: #ef4444; display: none; font-size: 12px; margin-top: 4px;"></small>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label">Adresse *</label>
                <input type="text" name="adresse" value="{{ old('adresse', $espace?->adresse) }}" class="form-input adresse-input" required>
                <small class="error-message" style="color: #ef4444; display: none; font-size: 12px; margin-top: 4px;"></small>
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                    📍 Coordonnées GPS
                    <span style="font-weight:400;color:var(--text-muted);font-size:11px;">(optionnel — pour afficher sur la carte)</span>
                </label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <input type="number" name="latitude" value="{{ old('latitude', $espace?->latitude) }}" class="form-input latitude-input" step="0.0000001" min="-90" max="90" placeholder="Ex: 36.8065">
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Latitude (ex: 36.8065)</div>
                        <small class="error-message" style="color: #ef4444; display: none; font-size: 12px; margin-top: 4px;"></small>
                    </div>
                    <div>
                        <input type="number" name="longitude" value="{{ old('longitude', $espace?->longitude) }}" class="form-input longitude-input" step="0.0000001" min="-180" max="180" placeholder="Ex: 10.1815">
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Longitude (ex: 10.1815)</div>
                        <small class="error-message" style="color: #ef4444; display: none; font-size: 12px; margin-top: 4px;"></small>
                    </div>
                </div>
                <div style="margin-top:8px;padding:8px 12px;background:rgba(79,126,247,0.06);border-radius:8px;font-size:12px;color:var(--text-muted);">
                    💡 Trouvez les coordonnées sur <a href="https://www.openstreetmap.org" target="_blank" style="color:var(--accent);">openstreetmap.org</a> — clic droit → "Afficher l'adresse"
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label class="form-label">Prix par jour ( DT) *</label>
                    <input type="number" name="prix_jour" value="{{ old('prix_jour', $espace?->prix_jour) }}" class="form-input prix_jour-input" step="0.01" min="0" required>
                    <small class="error-message" style="color: #ef4444; display: none; font-size: 12px; margin-top: 4px;"></small>
                </div>
                <div>
                    <label class="form-label">Prix par heure ( DT) *</label>
                    <input type="number" name="prix_heure" value="{{ old('prix_heure', $espace?->prix_heure) }}" class="form-input prix_heure-input" step="0.01" min="0" required>
                    <small class="error-message" style="color: #ef4444; display: none; font-size: 12px; margin-top: 4px;"></small>
                </div>
                <div>
                    <label class="form-label">Capacité (personnes)</label>
                    <input type="number" name="capacite" value="{{ old('capacite', $espace?->capacite ?? 1) }}" class="form-input capacite-input" min="1" required>
                    <small class="error-message" style="color: #ef4444; display: none; font-size: 12px; margin-top: 4px;"></small>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input description-input" rows="4" placeholder="Décrivez l'espace...">{{ old('description', $espace?->description) }}</textarea>
                <small class="error-message" style="color: #ef4444; display: none; font-size: 12px; margin-top: 4px;"></small>
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label">Équipements (amenities)</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 10px;">
                    @foreach(['WiFi haut débit', 'Parking', 'Climatisation', 'Café/Thé', 'Imprimante', 'Vidéoprojecteur', 'Tableau blanc', 'Salle de bain', 'Cuisine', 'Terrasse'] as $amenity)
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px;">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity }}" style="accent-color: var(--accent);" {{ in_array($amenity, old('amenities', $espace?->amenities ?? [])) ? 'checked' : '' }}>
                        {{ $amenity }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-input image-input" accept="image/jpeg,image/png,image/webp,image/jpg">
                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Formats acceptés : JPG, PNG, WEBP — taille maximale : 10 MB.</div>
                    <small class="error-message" style="color: #ef4444; display: none; font-size: 12px; margin-top: 4px;"></small>
                    @if($espace?->image)
                    <div style="margin-top: 8px;">
                        <img src="{{ route('storage.public', ['path' => $espace->image]) }}" style="height: 80px; border-radius: 8px; object-fit: cover;">
                    </div>
                    @endif
                </div>
                <div style="display: flex; align-items: center; gap: 10px; padding-top: 24px;">
                    <input type="checkbox" name="disponible" value="1" id="disponible" style="width: 18px; height: 18px; accent-color: var(--accent);" {{ old('disponible', $espace?->disponible ?? true) ? 'checked' : '' }}>
                    <label for="disponible" style="font-size: 14px; font-weight: 600; cursor: pointer;">
                        Espace disponible à la réservation
                    </label>
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    {{ $espace ? 'Mettre à jour' : 'Créer l\'espace' }}
                </button>
                <a href="{{ route('admin.spaces.index') }}" class="btn btn-outline btn-lg">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('spaceForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Nom validation
    const nomInput = document.querySelector('.nom-input');
    const nomError = nomInput.parentElement.querySelector('.error-message');
    if (!nomInput.value.trim()) {
        nomError.textContent = 'Le nom est requis.';
        nomError.style.display = 'block';
        isValid = false;
    } else if (nomInput.value.length > 200) {
        nomError.textContent = 'Le nom ne doit pas dépasser 200 caractères.';
        nomError.style.display = 'block';
        isValid = false;
    } else {
        nomError.style.display = 'none';
    }
    
    // Type validation
    const typeInput = document.querySelector('.type-input');
    const typeError = typeInput.parentElement.querySelector('.error-message');
    if (!typeInput.value) {
        typeError.textContent = 'Veuillez sélectionner un type.';
        typeError.style.display = 'block';
        isValid = false;
    } else {
        typeError.style.display = 'none';
    }
    
    // Adresse validation
    const adresseInput = document.querySelector('.adresse-input');
    const adresseError = adresseInput.parentElement.querySelector('.error-message');
    if (!adresseInput.value.trim()) {
        adresseError.textContent = 'L\'adresse est requise.';
        adresseError.style.display = 'block';
        isValid = false;
    } else if (adresseInput.value.length > 255) {
        adresseError.textContent = 'L\'adresse ne doit pas dépasser 255 caractères.';
        adresseError.style.display = 'block';
        isValid = false;
    } else {
        adresseError.style.display = 'none';
    }
    
    // Prix jour validation
    const prixJourInput = document.querySelector('.prix_jour-input');
    const prixJourError = prixJourInput.parentElement.querySelector('.error-message');
    if (prixJourInput.value === '' || parseFloat(prixJourInput.value) < 0) {
        prixJourError.textContent = 'Le prix par jour doit être un nombre positif.';
        prixJourError.style.display = 'block';
        isValid = false;
    } else {
        prixJourError.style.display = 'none';
    }
    
    // Prix heure validation
    const prixHeureInput = document.querySelector('.prix_heure-input');
    const prixHeureError = prixHeureInput.parentElement.querySelector('.error-message');
    if (prixHeureInput.value === '' || parseFloat(prixHeureInput.value) < 0) {
        prixHeureError.textContent = 'Le prix par heure doit être un nombre positif.';
        prixHeureError.style.display = 'block';
        isValid = false;
    } else {
        prixHeureError.style.display = 'none';
    }
    
    // Capacite validation
    const capaciteInput = document.querySelector('.capacite-input');
    const capaciteError = capaciteInput.parentElement.querySelector('.error-message');
    if (!capaciteInput.value || parseInt(capaciteInput.value) < 1) {
        capaciteError.textContent = 'La capacité doit être au moins 1.';
        capaciteError.style.display = 'block';
        isValid = false;
    } else {
        capaciteError.style.display = 'none';
    }
    
    // Image validation
    const imageInput = document.querySelector('.image-input');
    if (imageInput.files.length > 0) {
        const file = imageInput.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB
        const imageError = imageInput.parentElement.querySelector('.error-message');
        
        if (file.size > maxSize) {
            imageError.textContent = 'L\'image doit faire moins de 10 MB.';
            imageError.style.display = 'block';
            isValid = false;
        } else if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            imageError.textContent = 'Format non autorisé. Acceptés: JPG, PNG, WEBP.';
            imageError.style.display = 'block';
            isValid = false;
        } else {
            imageError.style.display = 'none';
        }
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});
</script>
@endsection
