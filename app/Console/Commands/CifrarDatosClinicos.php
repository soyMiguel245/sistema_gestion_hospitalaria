<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class CifrarDatosClinicos extends Command
{
    protected $signature = 'clinico:cifrar {--dry-run : Solo contar, sin escribir en la BD}';

    protected $description = 'Cifra en AES-256 los campos clínicos de atenciones_medicas y diagnosticos. Idempotente: omite lo que ya está cifrado.';

    private array $campos = [
        'atenciones_medicas' => [
            'motivo_consulta', 'diagnostico', 'tratamiento',
            'procedimientos', 'indicaciones', 'observaciones',
        ],
        'diagnosticos' => [
            'descripcion', 'observaciones',
        ],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Modo --dry-run: no se escribe nada, solo se cuenta.');
        }

        foreach ($this->campos as $tabla => $columnas) {
            $this->cifrarTabla($tabla, $columnas, $dryRun);
        }

        $this->info('Listo.');

        return self::SUCCESS;
    }

    private function cifrarTabla(string $tabla, array $columnas, bool $dryRun): void
    {
        $this->line("-> {$tabla}");

        $filasTocadas = 0;
        $camposCifrados = 0;
        $camposOmitidos = 0;

        DB::table($tabla)->chunkById(200, function ($filas) use (
            $columnas, $tabla, $dryRun, &$filasTocadas, &$camposCifrados, &$camposOmitidos
        ) {
            foreach ($filas as $fila) {
                $actualizar = [];

                foreach ($columnas as $columna) {
                    $valor = $fila->{$columna};

                    if ($valor === null) {
                        continue;
                    }

                    if ($this->yaEstaCifrado($valor)) {
                        $camposOmitidos++;

                        continue;
                    }

                    $actualizar[$columna] = Crypt::encryptString($valor);
                    $camposCifrados++;
                }

                if ($actualizar) {
                    $filasTocadas++;

                    if (! $dryRun) {
                        DB::table($tabla)->where('id', $fila->id)->update($actualizar);
                    }
                }
            }
        });

        $this->line("   filas afectadas: {$filasTocadas} | campos cifrados: {$camposCifrados} | ya estaban cifrados: {$camposOmitidos}");
    }

    private function yaEstaCifrado(string $valor): bool
    {
        try {
            Crypt::decryptString($valor);

            return true;
        } catch (DecryptException) {
            return false;
        }
    }
}
