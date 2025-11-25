<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<style>
    @page { size: letter portrait; margin: 0; }
    body {
        margin: 0;
        padding: 24px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
    }

    /* Outer box like the printed form */
    .form-box {
        border: 2px solid #000;
        height: 100%;
        padding: 18px;
        box-sizing: border-box;
        position: relative;
    }

    /* Header */
    .header {
        display:flex;
        gap:12px;
        align-items:center;
        margin-bottom: 6px;
    }
    .logo {
        width: 110px;
        height: 80px;
        border: 1px solid transparent;
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:700;
        font-size:14px;
    }
    .title {
        flex:1;
        text-align:center;
        font-size:20px;
        font-weight:700;
        letter-spacing:1px;
        text-decoration:underline;
        padding-top: 6px;
    }

    .row { width:100%; display:flex; gap:10px; align-items:center; }
    .field { display:block; }
    .label { font-weight:600; font-size:12px; }
    .line {
        border-bottom: 1px solid #000;
        display:inline-block;
        min-width: 220px;
        padding-left:6px;
        padding-right:6px;
    }

    /* Two-column lines where needed */
    .two-cols { display:flex; gap:12px; align-items:center; margin-top:6px; }
    .two-cols .col { flex:1; }

    /* Big textarea-like lines */
    .big-line { border-bottom:1px solid #000; min-height:18px; padding-left:6px; padding-right:6px; display:block; }

    /* Checkboxes grid */
    .checkboxes {
        display:grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap:12px;
        margin-top:8px;
    }
    .checkbox-column { display:flex; flex-direction:column; gap:8px; }

    .check-item { display:flex; gap:8px; align-items:flex-start; }
    .check-square {
        width:18px; height:18px; border:1px solid #000; display:inline-block;
        text-align:center; line-height:18px; font-size:12px;
    }

    /* Observations & justification */
    .section {
        margin-top:14px;
    }
    .observation {
        border:1px solid #000;
        min-height:70px;
        padding:6px;
    }

    /* Signatures area */
    .signs {
        display:flex;
        justify-content:space-between;
        margin-top:30px;
        gap:20px;
    }
    .sign-block { width:30%; text-align:center; }
    .sign-line { border-top:1px solid #000; margin-top:36px; }

    /* Small note at bottom */
    .note {
        font-size:10px;
        margin-top:8px;
    }

    /* small helpers */
    .right { text-align:right; }
    .inline { display:inline-block; }
</style>
</head>
<body>
<div class="form-box">

    <div class="header">
        <div class="logo">
            {{-- Reemplaza src por tu logo si lo tienes. Ejemplo: file:///mnt/data/logo_pnc.png o asset('images/logo_pnc.png') --}}
            <img src="{{ asset('formatos/logo.png') }}"  style="max-width:100px; max-height:100px; ">
        </div>

        <div class="title">SOLICITUD DE PERMISO CON GOCE DE SUELDO</div>

        <div style="width:110px"></div>
    </div>

    <!-- Fecha -->
    <div style="margin-top:6px;">
        <span class="label">Fecha:</span>
        <span class="line">{{ optional($permiso->fecha_creacion)->format('d/m/Y') ?? '' }}</span>
    </div>

    <!-- Señor(a) -->
    <div style="margin-top:8px;">
        <span class="label">Señor(a):</span>
        <span class="line" style="width:78%">{{ $permiso->dirigido_a ?? $permiso->empleado->nombre ?? '' }}</span>
    </div>

    <!-- Yo, ONI -->
    <div style="margin-top:8px;">
        <span class="label">Yo,</span>
        <span class="line" style="width:52%">{{ $permiso->empleado->nombre ?? '' }}</span>
        <span class="label" style="margin-left:8px;">, ONI</span>
        <span class="line" style="width:22%">{{ $permiso->empleado->oni ?? '' }}</span>
    </div>

    <!-- Cargo / Division -->
    <div class="two-cols" style="margin-top:8px;">
        <div class="col">
            <span class="label">con cargo de</span>
            <span class="line" style="width:88%">{{ $permiso->empleado->cargo ?? '' }}</span>
        </div>
        <div class="col">
            <span class="label">División, Unidad o Delegación:</span>
            <span class="line" style="width:86%">{{ $permiso->empleado->unidad->nombre ?? $permiso->division ?? '' }}</span>
        </div>
    </div>

    <!-- Departamento / Puesto -->
    <div style="margin-top:8px;">
        <span class="label">Departamento, Sección o Puesto Policial</span>
        <span class="line" style="width:78%; display:block; margin-top:6px;">{{ $permiso->departamento ?? '' }}</span>
    </div>

    <!-- Solicitud texto / period -->
    <div style="margin-top:8px;">
        <span class="line" style="width:100%; min-height:38px; display:block;">
            {{ $permiso->texto_inicio ?? 'atentamente solicito a Usted, permiso con goce de sueldo para' }}
        </span>
    </div>

    <div style="margin-top:6px;">
        <span class="label">ausentarme de mis labores por un periodo de:</span>
        <span class="label inline" style="margin-left:8px;">mes(es):</span>
        <span class="line inline" style="width:50px;">{{ $permiso->meses ?? 0 }}</span>

        <span class="label inline" style="margin-left:12px;">día(s):</span>
        <span class="line inline" style="width:50px;">{{ $permiso->dias ?? '' }}</span>
    </div>

    <div style="margin-top:6px;">
        <span class="label">horas</span>
        <span class="line inline" style="width:60px;">{{ $permiso->horas ?? '' }}</span>
        <span class="label inline" style="margin-left:12px;">minuto(s)</span>
        <span class="line inline" style="width:60px;">{{ $permiso->minutos ?? '' }}</span>

        <span class="label inline" style="margin-left:12px;">desde</span>
        <span class="line inline" style="width:110px;">{{ $permiso->desde ?? '' }}</span>

        <span class="label inline" style="margin-left:12px;">hasta</span>
        <span class="line inline" style="width:110px;">{{ $permiso->hasta ?? '' }}</span>
    </div>

    <div style="margin-top:6px;">
        <span class="label">del día</span>
        <span class="line inline" style="width:220px;">{{ $permiso->dia ?? '' }}</span>
        <span class="label inline" style="margin-left:12px;">por el motivo siguiente:</span>
    </div>

    <!-- Checkboxes 3 columnas -->
    <div class="checkboxes">

        <div class="checkbox-column">
            @php
                $leftCol = [
                    'Permiso Personal','Por tiempo compensatorio','Cumpleaños','Licencia de 8 días por maternidad',
                    'Delegaciones deportivas, cultural o científicas','Tratamiento de enfermedad en el extranjero'
                ];
            @endphp

            @foreach($leftCol as $label)
                <div class="check-item">
                    <div class="check-square">
                        @if(strtolower($permiso->motivo ?? '') === strtolower($label)) X @endif
                    </div>
                    <div style="font-size:11px;">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        <div class="checkbox-column">
            @php
                $midCol = [
                    'Consulta médica (Examen de laboratorio, retiro de medicamentos)',
                    'Enfermedad o duelo (Padre, madre Cónyuge o hijos)','Matrimonio','Estudios/horas sociales',
                    'Diligencias judiciales/extrajudiciales','Falta de marcación'
                ];
            @endphp

            @foreach($midCol as $label)
                <div class="check-item">
                    <div class="check-square">
                        @if(strtolower($permiso->motivo ?? '') === strtolower($label)) X @endif
                    </div>
                    <div style="font-size:11px;">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        <div class="checkbox-column">
            @php
                $rightCol = [
                    'Licencia por enfermedad sin incapacidad','Misión oficial','Paternidad','Por lactancia','Por impartir clases'
                ];
            @endphp

            @foreach($rightCol as $label)
                <div class="check-item">
                    <div class="check-square">
                        @if(strtolower($permiso->motivo ?? '') === strtolower($label)) X @endif
                    </div>
                    <div style="font-size:11px;">{{ $label }}</div>
                </div>
            @endforeach
        </div>

    </div>

    <!-- Justificación -->
    <div class="section">
        <div class="label">Se anexa justificación correspondiente según detalle:</div>
        <div class="big-line" style="margin-top:6px;">{{ $permiso->detalle ?? '' }}</div>
    </div>

    <!-- Observación -->
    <div class="section">
        <div class="label">Observacion:</div>
        <div class="observation">{{ $permiso->observacion ?? '' }}</div>
    </div>

    <!-- Signatures -->
    <div class="signs">
        <div class="sign-block">
            <div class="sign-line"></div>
            <div style="margin-top:6px;">Solicitante</div>
        </div>

        <div class="sign-block">
            <div class="sign-line"></div>
            <div style="margin-top:6px;">Visto bueno Jefe Inmediato</div>
        </div>

        <div class="sign-block">
            <div class="sign-line"></div>
            <div style="margin-top:6px;">Autoriza: Jefe Inmediato Superior</div>
        </div>
    </div>

    <div class="note">
        NOTA: Para Solicitudes de permisos mayores de ocho (8) días, deberá contarse con la autorización del Director General.
    </div>

</div>
</body>
</html>
