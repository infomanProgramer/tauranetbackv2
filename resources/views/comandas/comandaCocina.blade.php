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
      width: 55%;
      padding: 0 2px;
      white-space: normal;
      word-break: break-word;
    }
    .notas {
      width: 30%;
      font-style: italic;
      color: #555;
      font-size: 10px;
    }
  </style>
</head>
<body>
  {{-- <h2>{{ $nombre_restaurant }}</h2>
  <h3>{{ $sucursal }} - {{ $caja }}</h3> --}}
  <h3 class="center"><strong>Pedido # {{ $numero }}</strong></h3>
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
  <table>
    <thead>
      <tr>
        <th class="cantidad">Cant</th>
        <th class="producto">Prod</th>
        <th class="notas">Notas</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $item)
        <tr>
          <td class="cantidad">{{ $item->cantidad }}x</td>
          <td class="producto">{{ $item->detalle }}</td>
          <td class="notas">{{ $item->nota }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <hr>
</body>
</html>
