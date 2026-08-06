<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 👇 NUEVO: respaldo automático diario de la base de datos, a las 2:00 AM
// (horario de baja actividad), conservando los últimos 7 respaldos.
// El resultado se registra en storage/logs/backup.log para poder
// confirmar que corrió sin tener que revisar manualmente cada día.
Schedule::command('backup:database --mantener=7')
    ->dailyAt('02:00')
    ->appendOutputTo(storage_path('logs/backup.log'));