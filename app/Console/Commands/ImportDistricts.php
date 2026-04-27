<?php

namespace App\Console\Commands;

use App\Models\District;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportDistricts extends Command
{
    protected $signature = 'districts:import {file?}';
    protected $description = 'Importa ubigeos limpiando caracteres especiales (ó, ñ, í, etc.)';

    public function handle()
    {
        $filePath = $this->argument('file') ?? 'storage/app/ubigeo_clean_v2.csv';

        if (!file_exists(base_path($filePath))) {
            $this->error("❌ Archivo no encontrado: {$filePath}");
            return 1;
        }

        $this->info('🗑️  Vaciando tabla districts...');
        District::truncate();

        $this->info('📤 Procesando CSV y limpiando caracteres especiales...');

        $lines = file(base_path($filePath), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line))
                continue;

            // Ignorar encabezados
            if (
                str_contains($line, 'ubigeo;departamento') ||
                str_contains($line, 'DEPARTAMENTOPROVINCIADISTRITO')
            ) {
                continue;
            }

            $parts = array_map('trim', explode(';', $line));

            if (count($parts) >= 4 && strlen($parts[0]) === 6 && is_numeric($parts[0])) {
                District::create([
                    'ubigeo' => $parts[0],
                    'department' => Str::ascii($parts[1]),
                    'province' => Str::ascii($parts[2]),
                    'district' => Str::ascii($parts[3]),
                    'delivery_cost' => 0.00,
                    'is_active' => true,
                ]);
                $count++;
            }
        }

        $total = District::count();
        $this->info("✅ ¡Importación completada!");
        $this->info("→ Distritos importados: {$count}");
        $this->info("→ Total en base de datos: {$total}");
    }
}