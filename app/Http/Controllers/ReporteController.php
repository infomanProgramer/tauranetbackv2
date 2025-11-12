<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use PDF;


class ReporteController extends ApiController
{
    public function getReportesHoy($idRestaurante){
        \Log::error("*************** Si esta ingresando al metodo **************");
        $repHoy = DB::table('venta_productos as v')
            ->join('pagos as p', 'p.id_venta_producto', '=', 'v.id_venta_producto')
            ->join('historial_caja as h', 'h.id_historial_caja', '=', 'v.id_historial_caja')
            ->join('cajas as c', 'c.id_caja', '=', 'h.id_caja')
            ->join('sucursals as s', 's.id_sucursal', '=', 'c.id_sucursal')
            ->where('s.id_restaurant', '=', $idRestaurante)
            ->whereNull('c.deleted_at')
            ->where('h.estado', '=', true)
            ->groupBy('h.id_historial_caja', 's.nombre', 'h.monto_inicial', 'h.monto', 'h.fecha', 'nombreCaja')
            ->select('s.nombre', 'h.monto_inicial', 'h.monto', 'h.fecha', 'h.id_historial_caja', 'c.nombre as nombreCaja',DB::raw('count(p.*)'))
            ->get();
        $response = Response::json(['data' => $repHoy], 200);
        return $response;
    }
    public function queryDetalleVentas($idRestaurante, $fecha_ini, $fecha_fin){
        $dventas = DB::table('venta_productos as v')
            ->leftJoin('clientes as cl', 'cl.id_cliente', '=', 'v.id_cliente')
            ->join('cajeros as c', 'c.id_cajero', '=', 'v.id_cajero')
            ->join('pagos as p', 'p.id_venta_producto', '=', 'v.id_venta_producto')
            ->join('historial_caja as h', 'h.id_historial_caja', '=', 'v.id_historial_caja')
            ->join('cajas as a', 'a.id_caja', '=', 'h.id_caja')
            ->join('sucursals as s', 's.id_sucursal', '=', 'a.id_sucursal')
            ->where('s.id_restaurant', $idRestaurante)
            ->where('v.estado_venta', '=', 0)
            ->whereBetween(DB::raw('DATE(h.fecha)'), ["'". $fecha_ini ."'", "'". $fecha_fin ."'"])
            ->orderByDesc('v.created_at')
            ->select(
                'v.id_venta_producto',
                'h.fecha', 
                's.nombre as nombreSucursal', 
                'v.nro_venta', 
                'cl.nombre_completo', //datos cliente
                'c.nombre_usuario', //datos del usuario que lo atendio
                DB::raw("(SELECT descripcion FROM diccionario_datos WHERE tabla='pagos' AND campo='tipo_pago' AND codigo=p.tipo_pago) as tipo_pago"), //tipo de pago
                DB::raw("(SELECT descripcion FROM diccionario_datos WHERE tabla='pagos' AND campo='tipo_servicio' AND codigo=p.tipo_servicio) as tipo_servicio"), //tipo de servicio
                'p.importe' //importe
            );
        return $dventas;
    }
    public function queryDetalleProducto($idRestaurante, $fecha_ini, $fecha_fin){
        $detVentaProductos = DB::table('historial_caja as hc')
        ->join('venta_productos as vp', 'vp.id_historial_caja', '=', 'hc.id_historial_caja')
        ->join('producto_vendidos as pv', 'pv.id_venta_producto', '=', 'vp.id_venta_producto')
        ->join('productos as p', 'p.id_producto', '=', 'pv.id_producto')
        ->join('categoria_productos as cp', 'cp.id_categoria_producto', '=', 'p.id_categoria_producto')
        ->whereBetween(DB::raw('DATE(hc.fecha)'), ["'". $fecha_ini ."'", "'". $fecha_fin ."'"])
        ->where('vp.estado_venta', 0)
        ->select([
            'cp.nombre as categoria',
            'p.nombre as producto',
            'pv.p_unit as precio_unitario',
            DB::raw('SUM(pv.cantidad) as cantidad_vendida'),
            DB::raw('SUM(pv.p_unit*pv.cantidad) as ingreso_total'),
        ])
        ->groupBy('cp.nombre', 'p.nombre', 'pv.p_unit')
        ->orderBy('cp.nombre')
        ->orderBy('p.nombre')
        ->orderBy('pv.p_unit');  
        return $detVentaProductos;
    }
    public function detalleVentas($idRestaurante, $fecha_ini, $fecha_fin, $tipoReporte){
        //Datos de todas las sucursales y roles
        \Log::info('Todas las sucursales y perfiles tipo reporte: '.$tipoReporte);
        $dventas = $this->queryDetalleVentas($idRestaurante, $fecha_ini, $fecha_fin);
        if($tipoReporte == 0){//json html
            $dventas = $dventas->paginate(15);
            $response = Response::json(['data' => $dventas], 200);
            return $response;
        }else if($tipoReporte == 2){//excel
            $dventas = $dventas->get();
            //Log::info('dventas: ', $dventas->toArray());

            $exportData = [];
            foreach ($dventas as $row) {
                //Log::info('row: ', $row->toArray());
                $exportData[] = [
                    'Fecha' => $row->fecha,
                    'Sucursal' => $row->nombreSucursal,
                    'Nro. Pedido' => $row->nro_venta,
                    'Cliente' => $row->nombre_completo,
                    'Atendido Por' => $row->nombre_usuario,
                    'Tipo Pago' => $row->tipo_pago,
                    'Servicio' => $row->tipo_servicio,
                    'Importe' => $row->importe
                ];
            }
    
            // Crear una clase exportadora anónima
            $export = new class($exportData) implements FromCollection, WithHeadings {
                private $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() { return collect($this->data); }
                public function headings(): array {
                    return ['Fecha', 'Sucursal', 'Nro. Pedido', 'Cliente', 'Atendido Por', 'Tipo Pago', 'Servicio', 'Importe'];
                }
            };
            return Excel::download($export, 'reporte_detalle_ventas_'.$fecha_ini.'_'.$fecha_fin.'.xlsx');
        }else{//error
            $response = Response::json(['error' => ['tipoReporte' => ['Tipo de reporte es requerido']]], 200);
            return $response;
        }
    }
    public function detalleProducto($idRestaurante, $fecha_ini, $fecha_fin, $tipoReporte){
        $detVentaProductos = $this->queryDetalleProducto($idRestaurante, $fecha_ini, $fecha_fin);   

        if($tipoReporte == 0)//json html
        {   
            $detVentaProductos = $detVentaProductos->paginate(15);
            $response = Response::json(['data' => $detVentaProductos], 200);
            return $response;
        }else if($tipoReporte == 1){//excel
            $detVentaProductos = $detVentaProductos->get();
            //Log::info('dventas: ', $dventas->toArray());

            $exportData = [];
            foreach ($detVentaProductos as $row) {
                //Log::info('row: ', $row->toArray());
                $exportData[] = [
                    'Categoria' => $row->categoria,
                    'Producto' => $row->producto,
                    'Precio unitario' => $row->precio_unitario,
                    'Cantidad vendida' => $row->cantidad_vendida,
                    'Ingreso total' => $row->ingreso_total
                ];
            }
    
            // Crear una clase exportadora anónima
            $export = new class($exportData) implements FromCollection, WithHeadings {
                private $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() { return collect($this->data); }
                public function headings(): array {
                    return ['Categoria', 'Producto', 'Precio unitario', 'Cantidad vendida', 'Ingreso total'];
                }
            };
            return Excel::download($export, 'reporte_detalle_productos_'.$fecha_ini.'_'.$fecha_fin.'.xlsx');
        }else{//error
            $response = Response::json(['error' => ['tipoReporte' => ['Tipo de reporte es requerido']]], 200);
            return $response;
        }
        return null;
    }
    public function detalleVentasPDF(Request $request){
        Log::info('detalleVentasPDF - request:', $request->all());
        $dventas = $this->queryDetalleVentas($request->idRestaurante, $request->fecha_inicio, $request->fecha_fin);
        $dventas = $dventas->get();
        $pdf = PDF::loadView('reportes.detalle_ventas.detalle_ventas', [
            'nom_restaurante' => $request->nombre_restaurante,
            'titulo_reporte' => 'DETALLE DE VENTAS',
            'sucursal' => $request->sucursal,
            'caja' => $request->caja,
            'fecha_ini' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'listItem' => $dventas
        ]);
        return $pdf->download('reporte_detalle_ventas_'.$request->fecha_inicio.'_'.$request->fecha_fin.'.pdf');
    }
    public function detalleProductoPDF(Request $request){
        Log::info('detalleProductoPDF - request:', $request->all());
        $detVentaProductos = $this->queryDetalleProducto($request->idRestaurante, $request->fecha_inicio, $request->fecha_fin);
        $detVentaProductos = $detVentaProductos->get();
        $pdf = PDF::loadView('reportes.detalle_ventas.detalle_productos', [
            'nom_restaurante' => $request->nombre_restaurante,
            'titulo_reporte' => 'DETALLE DE VENTAS POR PRODUCTO',
            'sucursal' => $request->sucursal,
            'caja' => $request->caja,
            'fecha_ini' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'listItem' => $detVentaProductos
        ]);
        return $pdf->download('reporte_detalle_productos_'.$request->fecha_inicio.'_'.$request->fecha_fin.'.pdf');
    }
    public function empleadoPedido($idRestaurante, $fechaIni, $fechaFin){
        if($fechaIni == 'null'){
            $response = Response::json(['error' => ['ini' => ['Fecha ini es requerido']]], 200);
            return $response;
        }
        if($fechaFin == 'null'){
            $response = Response::json(['error' => ['fin' => ['Fecha fin es requerido']]], 200);
            return $response;
        }
        if($fechaIni <= $fechaFin){
            //Chart
            $empleadoPedido = DB::table(DB::raw("function_empleado_pedido(".$idRestaurante.",'".$fechaIni."', '".$fechaFin."')"))->get();
            //Table
            $empleadoPedidoTable = DB::table(DB::raw("function_empleado_pedido(".$idRestaurante.", '".$fechaIni."', '".$fechaFin."')"))->paginate(6);
            $response = Response::json(['data' => $empleadoPedido, 'dataT' => $empleadoPedidoTable], 200);
            return $response;
        }else{
            $response = Response::json(['error' => ['ini' => ['Fecha ini debe ser menor que Fecha fin']]], 200);
            return $response;
        }
    }
    
    public function fechaPedidos($idRestaurante, $fechaIni, $fechaFin){
        if($fechaIni == 'null'){
            $response = Response::json(['error' => ['ini' => ['Fecha ini es requerido']]], 200);
            return $response;
        }
        if($fechaFin == 'null'){
            $response = Response::json(['error' => ['fin' => ['Fecha fin es requerido']]], 200);
            return $response;
        }
        if($fechaIni <= $fechaFin) {
            //Chart
            $fechaPedido = DB::table(DB::raw("function_fecha_pedidos(" . $idRestaurante . ",'" . $fechaIni . "', '" . $fechaFin . "')"))->get();
            //Table
            $fechaPedidoTable = DB::table(DB::raw("function_fecha_pedidos(" . $idRestaurante . ", '" . $fechaIni . "', '" . $fechaFin . "')"))->paginate(6);
            $response = Response::json(['data' => $fechaPedido, 'dataT' => $fechaPedidoTable], 200);
            return $response;
        }else{
            $response = Response::json(['error' => ['ini' => ['Fecha ini debe ser menor que Fecha fin']]], 200);
            return $response;
        }
    }
    
    public function aperturaCajas($idRestaurante, $idSucursal){
        if($idSucursal == -1){
            $aperturaCajas = DB::table('historial_caja as h')
                ->join('cajas as c', 'c.id_caja', '=', 'h.id_caja')
                ->join('sucursals as s', 's.id_sucursal', '=', 'c.id_sucursal')
                ->where('s.id_restaurant', '=', $idRestaurante)
                ->orderBy('h.fecha', 'desc')
                ->select('s.nombre as nombreSucursal', 'c.nombre as nombreCaja', 'h.fecha', 'h.monto_inicial', 'h.monto')
                ->paginate(10);
            $response = Response::json(['data' => $aperturaCajas], 200);
            return $response;
        }else{
            $aperturaCajas = DB::table('historial_caja as h')
                ->join('cajas as c', 'c.id_caja', '=', 'h.id_caja')
                ->join('sucursals as s', 's.id_sucursal', '=', 'c.id_sucursal')
                ->where('s.id_restaurant', '=', $idRestaurante)
                ->where('s.id_sucursal', '=', $idSucursal)
                ->orderBy('h.fecha', 'desc')
                ->select('s.nombre as nombreSucursal', 'c.nombre as nombreCaja', 'h.fecha', 'h.monto_inicial', 'h.monto')
                ->paginate(10);
            $response = Response::json(['data' => $aperturaCajas], 200);
            return $response;
        }

    }
}
