<?php

use App\Http\Controllers\{AuthController, DashboardController, ReservationController, PaymentController, AdminController, AvisController};
use App\Models\{EspaceCoworking, Utilisateur, Avis};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


Route::get('/storage/{path}', function (string $path) {
    abort_unless(Storage::disk('public')->exists($path), 404);
    return response()->file(Storage::disk('public')->path($path));
})->where('path', '.*')->name('storage.public');

Route::get('/', function () {
    $espaces = EspaceCoworking::with('avis')->orderBy('nom')->get();

    $stats = [
        'espaces' => $espaces->count(),
        'utilisateurs' => Utilisateur::where('role', 'user')->count(),
        'villes' => $espaces->pluck('adresse')
            ->filter()
            ->map(function ($adresse) {
                $parts = array_values(array_filter(array_map('trim', explode(',', $adresse))));
                return Str::lower(end($parts) ?: $adresse);
            })
            ->unique()
            ->count(),
    ];

    $espacesJs = $espaces->map(function ($e) {
        return [
            'id'         => $e->id_espace,
            'nom'        => $e->nom,
            'adresse'    => $e->adresse,
            'prix_jour'  => (float) $e->prix_jour,
            'prix_heure' => (float) $e->prix_heure,
            'capacite'   => $e->capacite,
            'type'       => $e->type,
            'type_libelle' => $e->type_libelle,
            'disponible' => (bool) $e->disponible,
            'amenities'  => $e->amenities ?? [],
            'image'      => $e->image ? route('storage.public', ['path' => $e->image]) : asset('images/default-space.jpg'),
            'lat'        => $e->latitude  ? (float) $e->latitude  : null,
            'lng'        => $e->longitude ? (float) $e->longitude : null,
            'avg_note'   => round($e->avis->avg('note') ?? 0, 1),
            'avis_count' => $e->avis->count(),
            'url'        => auth()->check()
                            ? route('reservations.create', ['espace_id' => $e->id_espace])
                            : route('login'),
        ];
    })->values();

    return view('user.onboarding', compact('espaces', 'espacesJs', 'stats'));
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::get('/create', [ReservationController::class, 'create'])->name('create');
        Route::post('/', [ReservationController::class, 'store'])->name('store');
        Route::get('/calendar', [ReservationController::class, 'calendar'])->name('calendar');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        Route::get('/{reservation}/payment', [ReservationController::class, 'payment'])->name('payment');
        Route::post('/{reservation}/payment/cash', [PaymentController::class, 'chooseCash'])->name('payment.cash');
        Route::post('/{reservation}/payment/stripe', [PaymentController::class, 'prepareStripe'])->name('payment.stripe');
        Route::post('/{reservation}/payment/stripe/confirm', [PaymentController::class, 'confirmPublicStripe'])->name('payment.stripe.confirm');
        Route::get('/{reservation}/payment/success', [ReservationController::class, 'paymentSuccess'])->name('payment.success');
        Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
    });

    Route::post('/api/check-availability', [ReservationController::class, 'checkAvailability'])->name('api.availability');
    Route::post('/api/calculate-price', [ReservationController::class, 'calculatePrice'])->name('api.price');
    Route::post('/api/validate-promo', [ReservationController::class, 'validatePromo'])->name('api.promo');

    // Avis (ratings & comments)
    Route::post('/avis', [AvisController::class, 'store'])->name('avis.store');
    Route::delete('/avis/{avis}', [AvisController::class, 'destroy'])->name('avis.destroy');

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/history', [PaymentController::class, 'history'])->name('history');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
        Route::post('/reservations/{reservation}/validate-cash', [AdminController::class, 'validateCashPayment'])->name('reservations.validateCash');

        Route::prefix('spaces')->name('spaces.')->group(function () {
            Route::get('/', [AdminController::class, 'spaces'])->name('index');
            Route::get('/create', [AdminController::class, 'createSpace'])->name('create');
            Route::post('/', [AdminController::class, 'storeSpace'])->name('store');
            Route::get('/{espace}/edit', [AdminController::class, 'editSpace'])->name('edit');
            Route::put('/{espace}', [AdminController::class, 'updateSpace'])->name('update');
            Route::post('/{espace}', [AdminController::class, 'updateSpace'])->name('update.post');
            Route::delete('/{espace}', [AdminController::class, 'deleteSpace'])->name('delete');
        });

        Route::get('/promo', [AdminController::class, 'promoCodes'])->name('promo');
        Route::post('/promo', [AdminController::class, 'storePromo'])->name('promo.store');
        Route::delete('/promo/{code}', [AdminController::class, 'deletePromo'])->name('promo.delete');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
        Route::get('/avis', [AvisController::class, 'adminIndex'])->name('avis');
        Route::delete('/avis/{avis}', [AvisController::class, 'adminDestroy'])->name('avis.delete');
    });
});
