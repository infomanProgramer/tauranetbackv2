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
  @if($result->count() > 0)
    <h2>{{ $result[0]->nombre_restaurante }}</h2>
    <h3>{{ $result[0]->nombre_sucursal }} - {{ $result[0]->nombre_caja }}</h3>
    <p>
      @if(!empty($result[0]->nombre_cliente))
        Cliente: {{ $result[0]->nombre_cliente }} <br>
      @endif
      Servicio: {{ $result[0]->tipo_servicio }} <br>
      Fecha: {{ $result[0]->fecha }} <br>
      Hora: {{ $result[0]->hora }}
    </p>
    <p class="center"><strong>Pedido # {{ $result[0]->nro_pedido }}</strong></p>
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
        @foreach($result as $item)
          <tr>
            <td class="cantidad">{{ $item->cantidad }}x</td>
            <td class="producto">{{ $item->nombre_producto }}</td>
            <td class="punit">{{ $item->p_unit }}</td>
            <td class="importe">{{ $item->importe }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <hr>
    <p class="total">TOTAL: {{ $result[0]->total }} {{ $result[0]->tipo_moneda }}</p>

    <p class="center">¡Gracias por su pedido!</p>
  @else
    <p class="center">Error!! al imprimir la comanda</p>
  @endif
</body>
</html>
