<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\ItineraryPackageController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\TipoProveedorController;
use App\Http\Controllers\AgenciaController;
use App\Http\Controllers\AgenciaProfileController;
use App\Http\Controllers\DestinoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ContabilidadController;

// Landing and auth routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('landing');
})->name('landing');
Route::get('/login', function () {
    return redirect(route('landing').'#login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', fn() => redirect()->route('dashboard'));

    // Admin only: User management
    Route::middleware(['role:Super Administrador,Administrador'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('agencias', [AgenciaController::class, 'index'])->name('agencias.index');
    });

    // Perfil de marca (RUC, teléfono, logo, colores): agencias y administradores
    Route::middleware(['role:Agencia,Administrador,Super Administrador'])->group(function () {
        Route::get('mi-agencia', [AgenciaProfileController::class, 'edit'])->name('agencia.profile.edit');
        Route::put('mi-agencia', [AgenciaProfileController::class, 'update'])->name('agencia.profile.update');
    });

    // Passengers (solo lectura: se crean/editan desde el formulario del Tour)
    Route::get('passengers', [PassengerController::class, 'index'])->name('passengers.index');

    // Reservas: estado de hospedajes/proveedores por cotización
    Route::middleware(['role:Reservas,Administrador,Super Administrador'])->group(function () {
        Route::get('reservas', [ReservaController::class, 'index'])->name('reservas.index');
        Route::get('reservas/{tour}', [ReservaController::class, 'show'])->name('reservas.show');
        Route::put('reservas/hospedajes/{hospedaje}', [ReservaController::class, 'updateHospedaje'])->name('reservas.hospedaje.update');
        Route::put('reservas/{tour}/proveedores/{proveedor}', [ReservaController::class, 'updateProveedor'])->name('reservas.proveedor.update');
    });

    // Contabilidad: extracto de ingresos general y por cotización
    Route::middleware(['role:Contabilidad,Administrador,Super Administrador'])->group(function () {
        Route::get('contabilidad', [ContabilidadController::class, 'index'])->name('contabilidad.index');
    });

    // Proveedores
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor']);

    // Tipos de Proveedor (catálogo propio)
    Route::resource('tipos-proveedor', TipoProveedorController::class)
        ->parameters(['tipos-proveedor' => 'tipo']);

    // Tours
    Route::get('tours/nuevo', [TourController::class, 'createChoice'])->name('tours.create-choice');
    Route::resource('tours', TourController::class);
    Route::get('tours/{tour}/pdf', [TourController::class, 'pdf'])->name('tours.pdf');
    Route::get('tours/{tour}/itinerario', [TourController::class, 'itinerarioPdf'])->name('tours.itinerario-pdf');
    Route::post('tours/{tour}/duplicate', [TourController::class, 'duplicate'])->name('tours.duplicate');
    Route::get('tours/{tour}/habitaciones', [TourController::class, 'habitaciones'])->name('tours.habitaciones');
    Route::post('tours/{tour}/habitaciones', [TourController::class, 'guardarHabitaciones'])->name('tours.habitaciones.store');

    // Destinos / Categorías / Subcategorías (jerarquía para clasificar Itinerarios)
    Route::resource('destinos', DestinoController::class);
    Route::resource('categorias', CategoriaController::class);

    // Itineraries (catálogo independiente, seleccionable desde Tours)
    Route::get('itineraries/search', [ItineraryController::class, 'search'])->name('itineraries.search');
    Route::resource('itineraries', ItineraryController::class)->except('show');

    // Paquetes de itinerario (plantillas de varios días, reutilizables al crear una cotización)
    Route::get('itinerary-packages/{itineraryPackage}/items', [ItineraryPackageController::class, 'items'])->name('itinerary-packages.items');
    Route::resource('itinerary-packages', ItineraryPackageController::class)->except('show');

    // Hotels (catálogo independiente, seleccionable desde Tours)
    Route::resource('hotels', HotelController::class)
        ->parameters(['hotels' => 'hotel']);

    // Rooms (nested under hotels)
    Route::prefix('hotels/{hotel}/rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/', [RoomController::class, 'store'])->name('store');
        Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::put('/{room}', [RoomController::class, 'update'])->name('update');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
    });
});
