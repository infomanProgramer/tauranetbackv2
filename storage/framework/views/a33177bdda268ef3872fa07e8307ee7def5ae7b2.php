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
        <?php if($fechaIni == $fechaFin): ?>
            <p style="font-size: 13px; color: #555;">Fecha: <strong><?php echo e($fechaIni); ?></strong></p>
        <?php else: ?>
            <p style="font-size: 13px; color: #555;">Desde: <strong><?php echo e($fechaIni); ?></strong> Hasta: <strong><?php echo e($fechaFin); ?></strong></p>
        <?php endif; ?>
    </div>

    <?php if(!empty($chartBase64)): ?>
        <div style="text-align:center; margin-bottom: 20px;">
            <img src="<?php echo e($chartBase64); ?>" alt="Gráfico" style="max-width: 600px; width: 100%; height: auto; border: 1px solid #ccc;" />
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoria</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $productoCantidadPDF; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->nom_producto); ?></td>
                    <td><?php echo e($item->nom_categoria); ?></td>
                    <td><?php echo e($item->cantidad); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <!-- <div class="footer"> -->
        <!-- <p>Generado por: leslie</p> -->
    <!-- </div> -->
</body>
</html>
