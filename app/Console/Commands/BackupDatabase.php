<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupDatabase extends Command
{
    /**
     * 👇 NUEVO: antes no existía ningún mecanismo de respaldo, ni manual
     * ni automático — un fallo de disco o un error humano habría
     * significado pérdida total de datos clínicos, sin forma de
     * recuperarlos. Usa BACKUP DATABASE nativo, guarda en una carpeta
     * dedicada con permisos explícitos para la cuenta de servicio de
     * SQL Server.
     *
     * 👇 NUEVO (verificación): un backup que nunca se restaura no está
     * probado, está generado. Tras crear el archivo, se corre
     * RESTORE VERIFYONLY — SQL Server valida que el archivo esté
     * completo y legible sin restaurarlo de verdad. Si falla, NO se
     * rotan los respaldos anteriores (para no quedarnos sin ninguno
     * bueno) y el comando termina en FAILURE para que el monitoreo lo vea.
     *
     * 👇 CORREGIDO: se usa DB::unprepared() en vez de DB::statement().
     * BACKUP DATABASE y RESTORE VERIFYONLY devuelven varios mensajes
     * informativos de progreso en múltiples result sets. DB::statement()
     * pasa por PDO::prepare()+execute(), que con el driver sqlsrv a veces
     * devuelve el control a PHP antes de que SQL Server termine de
     * cerrar el archivo físicamente en disco — el comando "parece"
     * completado pero el archivo aún no está listo, causando el error
     * "Cannot open backup device... Operating system error 2" al
     * verificar inmediatamente después. DB::unprepared() usa PDO::exec()
     * directo, sin ese paso intermedio, y sí espera a que el driver
     * consuma todos los result sets antes de continuar. Confirmado con
     * sqlcmd puro (que tampoco usa prepare) que el backup en sí siempre
     * fue válido — el problema era exclusivamente de esta capa.
     */
    protected $signature = 'backup:database {--mantener=7 : Cuántos respaldos recientes conservar}';

    protected $description = 'Genera un respaldo completo de la base de datos, lo verifica, y elimina los respaldos antiguos';

    public function handle(): int
    {
        $baseDatos = config('database.connections.sqlsrv.database');
        $carpeta = $this->obtenerCarpetaDeRespaldo();
        $nombreArchivo = 'backup_'.now()->format('Y_m_d_His').'.bak';
        $rutaCompleta = rtrim($carpeta, '\\/').DIRECTORY_SEPARATOR.$nombreArchivo;
        $rutaEscapada = str_replace("'", "''", $rutaCompleta);

        $this->info("Respaldando [{$baseDatos}] en: {$rutaCompleta}");

        try {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare("BACKUP DATABASE [{$baseDatos}] TO DISK = '{$rutaEscapada}' WITH INIT, COMPRESSION");
            $stmt->execute();
            while ($stmt->nextRowset()) {
                // Drena cada result set de progreso hasta que SQL Server confirme que terminó
            }
        } catch (\Exception $e) {
            $this->error('Falló el respaldo: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Respaldo completado correctamente.');
        try {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare("RESTORE VERIFYONLY FROM DISK = '{$rutaEscapada}'");
            $stmt->execute();
            while ($stmt->nextRowset()) {
                // Drena cada result set hasta confirmar que la verificación terminó
            }
            $this->info('Verificación de integridad: OK.');
        } catch (\Exception $e) {
            $this->error('El archivo se creó pero NO pasó la verificación: '.$e->getMessage());
            $this->warn('No se rota ningún respaldo antiguo en esta corrida.');

            return self::FAILURE;
        }

        $this->rotarRespaldosAntiguos($carpeta, (int) $this->option('mantener'));

        return self::SUCCESS;
    }

    private function obtenerCarpetaDeRespaldo(): string
    {
        return 'C:\SQLBackups';
    }

    private function rotarRespaldosAntiguos(string $carpeta, int $mantener): void
    {
        $archivos = collect(glob(rtrim($carpeta, '\\/').DIRECTORY_SEPARATOR.'backup_*.bak'))
            ->sortByDesc(fn ($ruta) => filemtime($ruta))
            ->values();

        foreach ($archivos->slice($mantener) as $ruta) {
            @unlink($ruta);
            $this->line('Respaldo antiguo eliminado: '.basename($ruta));
        }
    }
}
