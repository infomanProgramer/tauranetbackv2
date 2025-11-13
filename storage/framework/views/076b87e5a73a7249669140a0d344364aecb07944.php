<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de ventas por producto</title>
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
        <p style="font-size: 13px; color: #555;">Desde: <strong><?php echo e($fecha_ini); ?></strong> Hasta: <strong><?php echo e($fecha_fin); ?></strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Producto</th>
                <th>Precio unitario</th>
                <th>Cantidad vendida</th>
                <th>Ingreso total</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $listItem; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($detalle->categoria); ?></td>
                    <td><?php echo e($detalle->producto); ?></td>
                    <td><?php echo e(number_format($detalle->precio_unitario, 2)); ?></td>
                    <td><?php echo e($detalle->cantidad_vendida); ?></td>
                    <td><?php echo e(number_format($detalle->ingreso_total, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
