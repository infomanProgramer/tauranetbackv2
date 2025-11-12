<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
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
        <h2 style="margin-bottom: 5px; color: #2c3e50; letter-spacing: 1px;">REPORTE DE VENTAS DIARIAS</h2>
        <h3 style="margin: 0; color: #34495e;">{{ $nom_restaurante }}</h3>
        <h4 style="margin: 0 0 10px 0; color: #7f8c8d;">{{ $sucursal }} - {{ $caja }}</h4>
        <p style="font-size: 13px; color: #555;">Fecha: <strong>{{ $fecha }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Nro. de pedido</th>
                <th>Cliente</th>
                <th>Atendido por</th>
                <th>Tipo pago</th>
                <th>Servicio</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventasPorDia as $venta)
                <tr>
                    <td>{{ $venta->hora }}</td>
                    <td>{{ $venta->nro_pedido }}</td>
                    <td>{{ $venta->cliente }}</td>
                    <td>{{ $venta->atendido_por }}</td>
                    <td>{{ $venta->tipo_pago }}</td>
                    <td>{{ $venta->tipo_servicio }}</td>
                    <td>{{ number_format($venta->importe, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

     <div class="totales">
        <p><strong>Total del dia:</strong></p>
        <p>Importe total: {{ number_format($totalDia->importe_total, 2) }}</p>
        <p>Tipo pago - Efectivo: {{ $totalDia->total_efectivo }}</p>
        <p>Tipo pago - Tarjeta: {{ $totalDia->total_tarjeta }}</p>
        <p>Tipo pago - QR: {{ $totalDia->total_qr }}</p>
    </div>
    <!-- <div class="footer"> -->
        <!-- <p>Generado por: leslie</p> -->
    <!-- </div> -->
</body>
</html>
