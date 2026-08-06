<?php

namespace App\Console\Commands;

use App\Models\AtencionMedica;
use App\Models\Diagnostico;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MedirRendimiento extends Command
{
    /**
     * 👇 NUEVO: antes no existía ninguna medición formal de rendimiento
     * — el sistema "se sentía rápido" con los pocos registros de
     * desarrollo, pero nunca se probó con un volumen de datos
     * representativo. Este comando genera datos de prueba dentro de una
     * transacción que SIEMPRE se revierte al final (rollback), así que
     * no deja ningún rastro permanente en la base de datos real, y mide
     * el tiempo de las operaciones más pesadas del sistema: listados,
     * dashboard, y el dashboard de reportes (que hace agregaciones).
     */
    protected $signature = 'rendimiento:medir
        {--pacientes=500 : Cantidad de pacientes de prueba a generar}
        {--atenciones=1000 : Cantidad de atenciones medicas de prueba a generar}
        {--repeticiones=5 : Cuantas veces repetir cada medicion para promediar}';

    protected $description = 'Mide tiempos de respuesta de las operaciones mas pesadas con un volumen de datos realista (sin dejar datos permanentes)';

    private array $resultados = [];

    public function handle(): int
    {
        $totalPacientes = (int) $this->option('pacientes');
        $totalAtenciones = (int) $this->option('atenciones');
        $repeticiones = (int) $this->option('repeticiones');

        $this->info("Generando datos de prueba: {$totalPacientes} pacientes, {$totalAtenciones} atenciones...");
        $this->warn('Todo esto se revierte al final (rollback) — no queda nada en la base de datos real.');

        DB::beginTransaction();

        try {
            $this->generarDatos($totalPacientes, $totalAtenciones);

            $this->newLine();
            $this->info('Datos generados. Iniciando mediciones...');
            $this->newLine();

            $this->medir('Listado de pacientes (index)', $repeticiones, function () {
                Paciente::orderByDesc('fecha_registro')->limit(50)->get();
            });

            $this->medir('Listado de atenciones con relaciones (paciente + medico)', $repeticiones, function () {
                AtencionMedica::with(['paciente', 'medico'])->orderByDesc('created_at')->limit(50)->get();
            });

            $this->medir('Dashboard principal (4 conteos)', $repeticiones, function () {
                Paciente::count();
                DB::table('citas')->count();
                Paciente::has('atencionesMedicas')->count();
                AtencionMedica::count();
            });

            $this->medir('Dashboard de reportes (diagnosticos + ingresos agregados)', $repeticiones, function () {
                Diagnostico::with('atencionMedica')
                    ->whereHas('atencionMedica', function ($query) {
                        $query->whereBetween('created_at', [now()->subMonth(), now()]);
                    })->get();

                AtencionMedica::selectRaw('tipo_paciente, SUM(costo - descuento) as total')
                    ->whereBetween('created_at', [now()->subMonth(), now()])
                    ->groupBy('tipo_paciente')
                    ->get();
            });

            $this->medir('Busqueda de paciente por DNI (validacion de unicidad)', $repeticiones, function () {
                Paciente::where('dni', '99999999')->exists();
            });

        } finally {
            DB::rollBack();
            $this->newLine();
            $this->info('Datos de prueba revertidos (rollback). La base de datos real quedo intacta.');
        }

        $this->mostrarResumen();
        $this->guardarReporte($totalPacientes, $totalAtenciones, $repeticiones);

        return self::SUCCESS;
    }

    private function generarDatos(int $totalPacientes, int $totalAtenciones): void
    {
        $especialidades = Especialidad::factory()->count(5)->create();
        $medicos = Medico::factory()->count(10)->recycle($especialidades)->create();

        $barra = $this->output->createProgressBar($totalPacientes);
        $pacientes = collect();

        Paciente::factory()->count($totalPacientes)->create()->each(function ($paciente) use (&$pacientes, $barra) {
            $pacientes->push($paciente);
            $barra->advance();
        });
        $barra->finish();
        $this->newLine();

        $barra = $this->output->createProgressBar($totalAtenciones);

        for ($i = 0; $i < $totalAtenciones; $i++) {
            AtencionMedica::factory()->create([
                'paciente_id' => $pacientes->random()->id,
                'medico_id' => $medicos->random()->id,
            ]);
            $barra->advance();
        }
        $barra->finish();
        $this->newLine();
    }

    private function medir(string $nombre, int $repeticiones, \Closure $operacion): void
    {
        $tiempos = [];

        for ($i = 0; $i < $repeticiones; $i++) {
            $inicio = microtime(true);
            $operacion();
            $tiempos[] = (microtime(true) - $inicio) * 1000;
        }

        $this->resultados[$nombre] = [
            'promedio' => round(array_sum($tiempos) / count($tiempos), 2),
            'min' => round(min($tiempos), 2),
            'max' => round(max($tiempos), 2),
        ];

        $this->line("  {$nombre}: {$this->resultados[$nombre]['promedio']} ms promedio");
    }

    private function mostrarResumen(): void
    {
        $this->newLine();
        $this->info('=== Resumen de rendimiento ===');

        $filas = collect($this->resultados)->map(fn ($datos, $nombre) => [
            $nombre, "{$datos['promedio']} ms", "{$datos['min']} ms", "{$datos['max']} ms",
        ])->values()->all();

        $this->table(['Operación', 'Promedio', 'Mínimo', 'Máximo'], $filas);
    }

    private function guardarReporte(int $totalPacientes, int $totalAtenciones, int $repeticiones): void
    {
        $carpeta = storage_path('app/reportes');

        if (! File::exists($carpeta)) {
            File::makeDirectory($carpeta, 0755, true);
        }

        $ruta = $carpeta.DIRECTORY_SEPARATOR.'rendimiento_'.now()->format('Y_m_d_His').'.md';

        $contenido = "# Reporte de rendimiento\n\n";
        $contenido .= '**Fecha:** '.now()->format('d/m/Y H:i')."\n";
        $contenido .= "**Volumen de prueba:** {$totalPacientes} pacientes, {$totalAtenciones} atenciones médicas\n";
        $contenido .= "**Repeticiones por medición:** {$repeticiones}\n\n";
        $contenido .= "| Operación | Promedio | Mínimo | Máximo |\n";
        $contenido .= "|---|---|---|---|\n";

        foreach ($this->resultados as $nombre => $datos) {
            $contenido .= "| {$nombre} | {$datos['promedio']} ms | {$datos['min']} ms | {$datos['max']} ms |\n";
        }

        File::put($ruta, $contenido);

        $this->info("Reporte guardado en: {$ruta}");
    }
}