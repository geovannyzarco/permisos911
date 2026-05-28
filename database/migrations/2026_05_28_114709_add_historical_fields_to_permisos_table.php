<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Agregar las nuevas columnas de snapshot histórico a la tabla 'permisos'
        Schema::table('permisos', function (Blueprint $table) {
            // Datos históricos del solicitante
            $table->string('empleado_nombre')->nullable()->comment('Nombre del empleado al tramitar');
            $table->string('empleado_oni')->nullable()->comment('ONI del empleado al tramitar');
            $table->string('cargo_nombre')->nullable()->comment('Categoría/cargo del empleado al tramitar');
            $table->string('unidad_nombre')->nullable()->comment('Unidad organizativa al tramitar');
            $table->string('division_nombre')->nullable()->comment('División organizativa al tramitar');
            $table->string('empleado_firma')->nullable()->comment('Ruta de archivo físico de firma del solicitante');

            // Datos históricos de visto bueno (Jefe Grupo)
            $table->string('jefe_vb_nombre')->nullable()->comment('Nombre de jefe de visto bueno');
            $table->string('jefe_vb_oni')->nullable()->comment('ONI de jefe de visto bueno');
            $table->string('jefe_vb_firma')->nullable()->comment('Ruta de archivo de firma de visto bueno');

            // Datos históricos de aprobación (Jefe Unidad)
            $table->string('jefe_aprobacion_nombre')->nullable()->comment('Nombre de jefe de aprobación');
            $table->string('jefe_aprobacion_oni')->nullable()->comment('ONI de jefe de aprobación');
            $table->string('jefe_aprobacion_firma')->nullable()->comment('Ruta de archivo de firma de aprobación');

            // Datos históricos de división (Jefe División)
            $table->string('jefe_division_nombre')->nullable()->comment('Nombre de jefe de división');
            $table->string('jefe_division_oni')->nullable()->comment('ONI de jefe de división');
            $table->string('jefe_division_firma')->nullable()->comment('Ruta de archivo de firma de división');
        });

        // 2. Poblar los campos históricos para todos los permisos existentes
        try {
            // Asegurar que el directorio privado para firmas exista en storage/app/firmas_historicas
            if (!Storage::disk('local')->exists('firmas_historicas')) {
                Storage::disk('local')->makeDirectory('firmas_historicas');
            }

            // Obtener todos los permisos existentes en lote para evitar problemas de memoria
            DB::table('permisos')->orderBy('id')->chunk(100, function ($permisos) {
                foreach ($permisos as $permiso) {
                    // Obtener los datos actuales del empleado solicitante
                    $empleado = DB::table('empleados')->where('id', $permiso->empleado_id)->first();
                    $cargo = $empleado ? DB::table('categorias')->where('id', $empleado->categoria_id)->first() : null;
                    $unidad = $empleado ? DB::table('unidades')->where('id', $empleado->unidad_id)->first() : null;
                    $division = $unidad ? DB::table('divisiones')->where('id', $unidad->division_id)->first() : null;

                    // Obtener los datos actuales del jefe de Visto Bueno (si aplica, usando la columna id_jefe_vb)
                    $jefeVb = $permiso->id_jefe_vb ? DB::table('empleados')->where('id', $permiso->id_jefe_vb)->first() : null;

                    // Obtener los datos actuales del jefe de Aprobación (si aplica, usando la columna id_jefe_aprobacion)
                    $jefeAprobacion = $permiso->id_jefe_aprobacion ? DB::table('empleados')->where('id', $permiso->id_jefe_aprobacion)->first() : null;

                    // Determinar el jefe de división (nivel_id = 4 dentro de la división correspondiente)
                    $jefeDivision = null;
                    if ($unidad) {
                        $jefeDivision = DB::table('empleados')
                            ->where('nivel_id', 4)
                            ->whereExists(function ($query) use ($unidad) {
                                $query->select(DB::raw(1))
                                    ->from('unidades')
                                    ->whereColumn('unidades.id', 'empleados.unidad_id')
                                    ->where('unidades.division_id', $unidad->division_id);
                            })
                            ->first();
                    }

                    // Decodificar y guardar firmas como archivos PNG en storage privado
                    $empleadoFirmaPath = $this->saveSignatureToFile($permiso->id, 'empleado', $empleado?->firma);
                    $jefeVbFirmaPath = $this->saveSignatureToFile($permiso->id, 'jefe_vb', $jefeVb?->firma);
                    $jefeAprobacionFirmaPath = $this->saveSignatureToFile($permiso->id, 'jefe_aprobacion', $jefeAprobacion?->firma);
                    $jefeDivisionFirmaPath = $this->saveSignatureToFile($permiso->id, 'jefe_division', $jefeDivision?->firma);

                    // Actualizar el registro del permiso con su snapshot
                    DB::table('permisos')->where('id', $permiso->id)->update([
                        'empleado_nombre' => $empleado?->nombre,
                        'empleado_oni' => $empleado?->oni,
                        'cargo_nombre' => $cargo?->nombre,
                        'unidad_nombre' => $unidad?->nombre,
                        'division_nombre' => $division?->nombre,
                        'empleado_firma' => $empleadoFirmaPath,
                        'jefe_vb_nombre' => $jefeVb?->nombre,
                        'jefe_vb_oni' => $jefeVb?->oni,
                        'jefe_vb_firma' => $jefeVbFirmaPath,
                        'jefe_aprobacion_nombre' => $jefeAprobacion?->nombre,
                        'jefe_aprobacion_oni' => $jefeAprobacion?->oni,
                        'jefe_aprobacion_firma' => $jefeAprobacionFirmaPath,
                        'jefe_division_nombre' => $jefeDivision?->nombre,
                        'jefe_division_oni' => $jefeDivision?->oni,
                        'jefe_division_firma' => $jefeDivisionFirmaPath,
                    ]);
                }
            });
        } catch (\Exception $e) {
            // Registrar error pero permitir que continúe la migración de base de datos
            Log::error("Error migrando firmas históricas: " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Obtener todas las firmas asociadas a permisos antes de eliminar columnas
        try {
            DB::table('permisos')->orderBy('id')->chunk(100, function ($permisos) {
                foreach ($permisos as $permiso) {
                    // Si el archivo físico de la firma existe, eliminarlo para limpiar el disco
                    if ($permiso->empleado_firma && Storage::disk('local')->exists($permiso->empleado_firma)) {
                        Storage::disk('local')->delete($permiso->empleado_firma);
                    }
                    if ($permiso->jefe_vb_firma && Storage::disk('local')->exists($permiso->jefe_vb_firma)) {
                        Storage::disk('local')->delete($permiso->jefe_vb_firma);
                    }
                    if ($permiso->jefe_aprobacion_firma && Storage::disk('local')->exists($permiso->jefe_aprobacion_firma)) {
                        Storage::disk('local')->delete($permiso->jefe_aprobacion_firma);
                    }
                    if ($permiso->jefe_division_firma && Storage::disk('local')->exists($permiso->jefe_division_firma)) {
                        Storage::disk('local')->delete($permiso->jefe_division_firma);
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error("Error limpiando firmas físicas: " . $e->getMessage());
        }

        // Eliminar las columnas históricas agregadas a la tabla 'permisos'
        Schema::table('permisos', function (Blueprint $table) {
            $table->dropColumn([
                'empleado_nombre',
                'empleado_oni',
                'cargo_nombre',
                'unidad_nombre',
                'division_nombre',
                'empleado_firma',
                'jefe_vb_nombre',
                'jefe_vb_oni',
                'jefe_vb_firma',
                'jefe_aprobacion_nombre',
                'jefe_aprobacion_oni',
                'jefe_aprobacion_firma',
                'jefe_division_nombre',
                'jefe_division_oni',
                'jefe_division_firma',
            ]);
        });
    }

    /**
     * Decodifica una firma Base64 y la guarda como archivo físico en almacenamiento privado.
     */
    private function saveSignatureToFile(int $permisoId, string $rol, ?string $firmaBase64): ?string
    {
        // Validar que el string sea un Base64 correcto
        if (!$firmaBase64 || !str_starts_with($firmaBase64, 'data:image/')) {
            return null;
        }

        try {
            // Encontrar la coma divisoria del formato data URI
            $commaPos = strpos($firmaBase64, ',');
            if ($commaPos === false) {
                return null;
            }

            // Obtener el contenido binario decodificando el Base64
            $data = substr($firmaBase64, $commaPos + 1);
            $decodedData = base64_decode($data);

            // Generar un hash único de 8 caracteres para el nombre del archivo
            $hash = substr(md5($firmaBase64 . microtime()), 0, 8);
            $fileName = "firmas_historicas/permiso_{$permisoId}_{$rol}_{$hash}.png";

            // Guardar usando Laravel Storage en el disco 'local' (privado, storage/app/)
            Storage::disk('local')->put($fileName, $decodedData);

            return $fileName;
        } catch (\Exception $e) {
            Log::error("Error guardando firma física en migración: " . $e->getMessage());
            return null;
        }
    }
};

