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
        <img src="<?php echo e($logo_url ?? ''); ?>" alt="Logo" class="logo">
        <h2 style="margin-bottom: 5px; color: #2c3e50; letter-spacing: 1px;"><?php echo e($titulo_reporte); ?></h2>
        <h3 style="margin: 0; color: #34495e;"><?php echo e($nom_restaurante); ?></h3>
        <h4 style="margin: 0 0 10px 0; color: #7f8c8d;"><?php echo e($sucursal); ?> - <?php echo e($caja); ?></h4>
        <p style="font-size: 13px; color: #555;">Desde: <strong><?php echo e($fechaIni); ?></strong> Hasta: <strong><?php echo e($fechaFin); ?></strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Pedidos</th>
                <th>Total efectivo</th>
                <th>Total tarjeta</th>
                <th>Total QR</th>
                <th>Total ventas</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $listItem; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($venta->fecha); ?></td>
                    <td><?php echo e($venta->cantidad); ?></td>
                    <td><?php echo e(number_format($venta->total_efectivo, 2)); ?></td>
                    <td><?php echo e(number_format($venta->total_tarjeta, 2)); ?></td>
                    <td><?php echo e(number_format($venta->total_qr, 2)); ?></td>
                    <td><?php echo e(number_format($venta->total_ventas, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <h3 style="margin-top: 20px; margin-bottom: 10px; padding: 10px; background-color: #f2f2f2; border: 1px solid #ccc;"><strong>Totales generales:</strong></h3>
    <table style="margin-top:30px;">
        <tbody>
            <!-- <colgroup>
                <col style="width:20%;">
                <col style="width:40%;">
                <col style="width:40%;">
            </colgroup> -->
            <tr style="background-color: #f9f9f9;">
                <td><strong>Total pedidos</strong></td>
                <td><?php echo e($totalesRangoFecha->total_pedidos); ?></td>
                <td></td>
            </tr>
            <tr style="background-color: #ffffff;">
                <td><strong>Total ventas</strong></td>
                <td><?php echo e(number_format($totalesRangoFecha->total_ventas, 2)); ?></td>
                <td></td>
            </tr>
            <tr style="background-color: #ffffff;">
                <td><strong>Total por tipo de pago</strong></td>
                <td></td>
                <td></td>
            </tr>
            <tr style="background-color: #f9f9f9;">
                <td></td>
                <td><strong>Efectivo</strong></td>
                <td><?php echo e(number_format($totalEfectivo->total_efectivo, 2)); ?></td>
            </tr>
            <tr style="background-color: #ffffff;">
                <td></td>
                <td><strong>Tarjeta</strong></td>
                <td><?php echo e(number_format($totalTarjeta->total_tarjeta, 2)); ?></td>
            </tr>
            <tr style="background-color: #f9f9f9;">
                <td></td>
                <td><strong>QR</strong></td>
                <td><?php echo e(number_format($totalQR->total_qr, 2)); ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
