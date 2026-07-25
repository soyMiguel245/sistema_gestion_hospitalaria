<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\HistorialClinicoController;
use App\Http\Controllers\AtencionMedicaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\EspecialidadController;
use App\Models\ArchivoMedico;
use App\Http\Controllers\BitacoraController;
// Página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
// 🔐 MÓDULOS DEL SISTEMA HOSPITALARIO
Route::middleware('auth')->group(function () {

    // Pacientes
    Route::resource('pacientes', PacienteController::class);

    // Médicos
    Route::resource('medicos', MedicoController::class);
    Route::get('/medicos/by-especialidad/{id}', [MedicoController::class, 'getByEspecialidad']);

    // Citas
    Route::resource('citas', CitaController::class);
    Route::get('/medicos/{especialidad}', [CitaController::class, 'getMedicos']);

     // Historias Clínicas (solo lectura: se arman desde AtencionMedica)
    Route::get('historias', [HistorialClinicoController::class, 'index'])
    ->name('historias.index');
    Route::get('historias/{paciente}', [HistorialClinicoController::class, 'show'])
    ->name('historias.show');


    // Atenciones Médicas
    Route::resource('atenciones', AtencionMedicaController::class)->parameters([
        'atenciones' => 'atencion' // ✅ Aquí arreglamos la pluralización
    ]);

    Route::get('archivos-medicos/{archivo}/descargar', [AtencionMedicaController::class, 'descargar'])
    ->name('archivos.descargar');

    Route::delete('archivos-medicos/{archivo}', [AtencionMedicaController::class, 'destroyArchivo'])
    ->name('archivos.destroy');
    // Especialidades
    Route::resource('especialidades', EspecialidadController::class);

    // Reportes clínicos
    Route::prefix('reportes')->group(function () {
        Route::get('/', [ReporteController::class,'dashboard'])->name('reportes.index');
        Route::get('dashboard', [ReporteController::class,'dashboard'])->name('reportes.dashboard');
        Route::get('pacientes', [ReporteController::class,'pacientes'])->name('reportes.pacientes');
        Route::get('historial', [ReporteController::class,'historial'])->name('reportes.historial');
        Route::get('diagnosticos', [ReporteController::class,'diagnosticos'])->name('reportes.diagnosticos');
        Route::get('procedimientos', [ReporteController::class,'procedimientos'])->name('reportes.procedimientos');
        Route::get('signos', [ReporteController::class,'signos'])->name('reportes.signos');
        Route::get('export/{tipo}', [ReporteController::class,'export'])->name('reportes.export');
        Route::get('exportar-pdf', [ReporteController::class, 'exportPDF'])->name('reportes.exportPDF');
        Route::get('/reportes/tablero', [ReporteController::class, 'dashboard'])->name('reportes.tablero');
    });

});

// Autenticación
require __DIR__.'/auth.php';
