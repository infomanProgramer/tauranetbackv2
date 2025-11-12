<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Ventas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .logo { width: 100px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .totales { margin-top: 20px; }
        .totales p { margin: 4px 0; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ $logo_url ?? '' }}" alt="Logo" class="logo">
        <h2 style="margin-bottom: 5px; color: #2c3e50; letter-spacing: 1px;">{{ $titulo_reporte }}</h2>
        <h3 style="margin: 0; color: #34495e;">{{ $nom_restaurante }}</h3>
        <h4 style="margin: 0 0 10px 0; color: #7f8c8d;">{{ $sucursal }} - {{ $caja }}</h4>
        <p style="font-size: 13px; color: #555;">Desde: <strong>{{ $fecha_ini }}</strong> Hasta: <strong>{{ $fecha_fin }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Sucursal</th>
                <th>Nro. Pedido</th>
                <th>Cliente</th>
                <th>Atendido Por</th>
                <th>Tipo Pago</th>
                <th>Servicio</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listItem as $detalle)
                <tr>
                    <td>{{ $detalle->fecha }}</td>
                    <td>{{ $detalle->nombreSucursal }}</td>
                    <td>{{ $detalle->nro_venta }}</td>
                    <td>{{ $detalle->nombre_completo }}</td>
                    <td>{{ $detalle->nombre_usuario }}</td>
                    <td>{{ $detalle->tipo_pago }}</td>
                    <td>{{ $detalle->tipo_servicio }}</td>
                    <td>{{ number_format($detalle->importe, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
