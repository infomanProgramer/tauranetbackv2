<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    @page  {
      margin: 5px;
      size: 72mm 1000mm; /* ancho fijo de 72mm, alto dinámico */
    }
    body {
      font-family: sans-serif;
      font-size: 12px;
    }
    p {
      margin-left: 5px;
    }
    h2, h3 {
      text-align: center;
      margin: 5px 0;
    }
    .center {
      text-align: center;
    }
    .total {
      text-align: right;
      font-weight: bold;
      margin-top: 10px;
    }
    hr {
      border: none;
      border-top: 1px dashed #000;
      margin: 5px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 2px 0;
    }
    th {
      text-align: left;
      font-size: 11px;
      border-bottom: 1px solid #000;
    }
    .cantidad {
      width: 15%;
      text-align: center;
    }
    .producto {
      width: 40%;
    }
    .punit {
      width: 20%;
      text-align: right;
    }
    .importe {
      width: 25%;
      text-align: right;
    }
  </style>
</head>
<body>
  <h2><?php echo e($nombre_restaurant); ?></h2>
  <h3><?php echo e($sucursal); ?> - <?php echo e($caja); ?></h3>
  <p>
    <?php if(!empty($datosCliente['nombre_completo'])): ?>
      Cliente: <?php echo e($datosCliente['nombre_completo']); ?> <br>
    <?php endif; ?>
    <?php if(!empty($datosCliente['dni'])): ?>
      <?php echo e($identificacion); ?>: <?php echo e($datosCliente['dni']); ?><br>
    <?php endif; ?>
    Servicio: <?php if($paymentDetails['tipo_servicio'] == 0): ?> Mesa <?php elseif($paymentDetails['tipo_servicio'] == 1): ?> Delivery <?php else: ?> Para llevar <?php endif; ?> <br>
    Fecha: <?php echo e($fechaActual); ?> - <?php echo e(date('H:i')); ?>

  </p>
  <p class="center"><strong>Pedido # <?php echo e($numero); ?></strong></p>
  <table>
    <thead>
      <tr>
        <th class="cantidad">Cant</th>
        <th class="producto">Prod</th>
        <th class="punit">P.Unit</th>
        <th class="importe">Importe</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td class="cantidad"><?php echo e($item->cantidad); ?>x</td>
          <td class="producto"><?php echo e($item->detalle); ?></td>
          <td class="punit"><?php echo e($item->p_unit); ?></td>
          <td class="importe"><?php echo e($item->importe); ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>

  <hr>
  <p class="total">TOTAL: <?php echo e($paymentDetails['importe']); ?> Bs</p>

  <p class="center">¡Gracias por su pedido!</p>
</body>
</html>
