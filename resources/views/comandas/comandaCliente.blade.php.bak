<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    @page {
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
  <h2>{{ $nombre_restaurant }}</h2>
  <h3>{{ $sucursal }} - {{ $caja }}</h3>
  <p>
    @if(!empty($datosCliente['nombre_completo']))
      Cliente: {{ $datosCliente['nombre_completo'] }} <br>
    @endif
    @if(!empty($datosCliente['dni']))
      {{ $identificacion }}: {{ $datosCliente['dni'] }}<br>
    @endif
    Servicio: @if($paymentDetails['tipo_servicio'] == 0) Mesa @elseif($paymentDetails['tipo_servicio'] == 1) Delivery @else Para llevar @endif <br>
    Fecha: {{ $fechaActual }} - {{ date('H:i') }}
  </p>
  <p class="center"><strong>Pedido # {{ $numero }}</strong></p>
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
      @foreach($items as $item)
        <tr>
          <td class="cantidad">{{ $item->cantidad }}x</td>
          <td class="producto">{{ $item->detalle }}</td>
          <td class="punit">{{ $item->p_unit }}</td>
          <td class="importe">{{ $item->importe }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <hr>
  <p class="total">TOTAL: {{ $paymentDetails['importe'] }} Bs</p>

  <p class="center">¡Gracias por su pedido!</p>
</body>
</html>
