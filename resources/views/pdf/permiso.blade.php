<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Permiso</title>
    <style>
        @page {
            margin: 0;
            size: letter portrait;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            text-transform: uppercase;
        }
        .container {
            position: relative;
            width: 215.9mm;
            height: 279.4mm;
        }
        .background-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .field {
            position: absolute;
            color: #000;
            /* background: rgba(255,0,0,0.1); // Descomenta para depurar posiciones */
        }

        /* --- MAPEO DE CAMPOS (Ajustar según necesidad) --- */

        .id { top: 127px; left: 33px; width: 600px; }
        .fecha { top: 150px; left: 110px; width: 600px; }
        .senor { top: 180px; left: 140px; width: 600px; }
        .yo { top: 208px; left: 100px; width: 450px; }
        .oni { top: 208px; left: 640px; width: 100px; }
        .cargo { top: 238px; left: 140px; width: 250px; }
        .unidad { top: 238px; left: 568px; width: 210px; }
        .departamento { top: 270px; left: 615px; width: 400px; }
        .motivo_texto { top: 720px; left: 450px; width: 400px; }

        /* Periodos */
        .meses { top: 330px; left: 460px; }
        .dias { top: 330px; left: 660px; }
        .horas { top: 360px; left: 115px; }
        .minutos { top: 360px; left: 280px; }
        .desde { top: 360px; left: 415px; }
        .hasta { top: 360px; left: 615px; }
        .dia_letras { top: 393px; left: 120px; width: 250px; }

        /* Checkboxes (X) */
        .check { font-size: 16px; font-weight: bold; }
        .check-personal { top: 430px; left: 240px; }
        .check-compensatorio { top: 460px; left: 240px; }
        .check-cumple { top: 490px; left: 240px; }
        .check-maternidad { top: 523px; left: 240px; }
        .check-delegacion { top: 566px; left: 240px; }
        .check-tratamiento { top: 607px; left: 240px; }
        .check-consulta { top: 435px; left: 501px; }
        .check-enfermedad { top: 493px; left: 501px; }
        .check-estudios { top: 561px; left: 501px; }
        .check-diligencias { top: 596px; left: 501px; }
        .check-marcacion { top: 633px; left: 501px; }
        .check-licencia { top: 430px; left: 758px; }
        .check-mision { top: 475px; left: 758px; }
        .check-paternidad { top: 508px; left: 760px; }
        .check-lactancia { top: 538px; left: 760px; }
        .check-impartir { top: 568px; left: 760px; }
        .check-matrimonio { top: 530px; left: 501px; }
        .check-singocesueldo { top: 600px; left: 572px; width: 230px; }


        .comentarios { top: 765px; left: 150px; width: 400px; }
        .observacion { top: 780px; left: 170px; width: 600px; }

        /* Firmas */
        .firma-solicitante { top: 843px; left: 60px; width: 220px; text-align: center; }
        .firma-vb { top: 843px; left: 520px; width: 220px; text-align: center; }
        .firma-autoriza { top: 900px; left: 224px; width: 220px; text-align: center; }
        .firma-img {
            width: 140px;
            height: 70px;
            object-fit: contain;
            /* Intentamos forzar transparencia si el motor lo permite */
            background-color: transparent;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Imagen de fondo -->
        <img src="{{ public_path('formatos/permisos.jpg') }}" class="background-img">

        @php
            $jefeDivision = \App\Models\Empleado::where('nivel_id', 4)
                ->whereHas('unidad', function($q) use ($permiso) {
                    $q->where('division_id', $permiso->empleado->unidad?->division_id);
                })->first();
        @endphp

        <!-- Datos -->
        <div class="field id">ID: {{ $permiso->id }}</div>
        <div class="field fecha">{{ $permiso->fecha_creacion?->format('d/m/Y') }}</div>
        <div class="field senor">{{ $jefeDivision?->nombre ?? '________________________________________________' }}</div>
        <div class="field yo">{{ $permiso->empleado->nombre }}</div>
        <div class="field oni">{{ $permiso->empleado->oni }}</div>
        <div class="field cargo">{{ $permiso->empleado->categoria?->nombre }}</div>
        <div class="field unidad">{{ $permiso->empleado->unidad?->division?->nombre }}</div>
        <div class="field departamento">{{ $permiso->empleado->unidad?->nombre }}</div>

        <div class="field meses">{{ $permiso->meses ?? '0' }}</div>
        <div class="field dias">{{ $permiso->dias ?? '0' }}</div>
        <div class="field horas">{{ $permiso->horas ?? '0' }}</div>
        <div class="field minutos">{{ $permiso->minutos ?? '0' }}</div>
        <div class="field desde">{{ $permiso->desde?->format('H:i') }}</div>
        <div class="field hasta">{{ $permiso->hasta?->format('H:i') }}</div>
        <div class="field dia_letras">{{ $permiso->desde?->format('d/m/Y') }}</div>

        <!-- Checkboxes (X) tipo permisos-->
        @if($permiso->tipo_permiso_id == 1) {{-- PERMISO PERSONAL --}}
            <div class="field check check-personal">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 2) {{-- POR TIEMPO COMPENSATORIO --}}
            <div class="field check check-compensatorio">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 3) {{-- CUMPLEAÑOS --}}
            <div class="field check check-cumple">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 4) {{-- LICENCIA DE 8 DIAS POR MATERNIDAD --}}
            <div class="field check check-maternidad">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 5) {{-- DELEGACIONES DEPORTIVAS, CULTURAL O CIENTIFICAS --}}
            <div class="field check check-delegacion">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 6) {{-- TRATAMIENTO DE ENFERMEDAD EN EL EXTRANJERO --}}
            <div class="field check check-tratamiento">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 7) {{-- CONSULTA MEDICA --}}
            <div class="field check check-consulta">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 8) {{-- ENFERMEDAD O DUELO --}}
            <div class="field check check-enfermedad">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 9) {{-- ESTUDIOS/HORAS SOCIALES --}}
            <div class="field check check-estudios">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 10) {{-- DILIGENCIAS JUDICIALES/EXTRAJUDICIALES --}}
            <div class="field check check-diligencias">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 11) {{-- FALTA DE MARCACION --}}
            <div class="field check check-marcacion">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 12) {{-- LICENCIA POR ENFERMEDAD SIN INCAPACIDAD --}}
            <div class="field check check-licencia">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 13) {{-- MISION OFICIAL --}}
            <div class="field check check-mision">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 14) {{-- PATERNIDAD --}}
            <div class="field check check-paternidad">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 15) {{-- POR LACTANCIA --}}
            <div class="field check check-lactancia">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 16) {{-- POR IMPARTIR CLASES --}}
            <div class="field check check-impartir">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 17) {{-- MATRIMONIO --}}
            <div class="field check check-matrimonio">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 18) {{-- LICENCIA SIN GOCE DE SUELDO --}}
            <div class="field check check-singocesueldo" style="font-size: 12px; font-weight: normal;">
                Licencia sin goce </br>de sueldo
                <span style="border: 1px solid black; width: 32px; height: 15px; display: inline-block; text-align: center; line-height: 14px; font-weight: bold; font-size: 16px; margin-left: 102px; padding-top: 4px;">X</span>
            </div>
        @endif

        <div class="field motivo_texto">{{ $permiso->motivo }}</div>
        <div class="field comentarios">comentarios{{ $permiso->comentarios }}</div>


        <!-- Firmas -->
        @if($permiso->empleado && $permiso->empleado->firma)
            <div class="field firma-solicitante">
                <img src="{{ $permiso->empleado->firma }}" class="firma-img">
            </div>
        @endif

        @if($permiso->jefeAprobacion && $permiso->jefeAprobacion->firma)
            <div class="field firma-vb">
               <img src="{{ $permiso->jefeVb->firma }}" class="firma-img">
            </div>
        @endif

        @if($permiso->jefeDivision && $permiso->jefeDivision->firma)
            <div class="field firma-autoriza">
               <img src="{{ $permiso->jefeAprobacion->firma }}" class="firma-img">
            </div>
        @endif
    </div>
</body>
</html>
