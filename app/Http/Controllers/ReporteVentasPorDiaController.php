<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Log;
use PDF;
use Illuminate\Http\Request;


class ReporteVentasPorDiaController extends ApiController
{
    //Reporte de ventas por día para un restaurante específico
    //Reporte general de ventas por día
    public function getReportePerDay($idRestaurante, $fecha){
        Log::info('getReportePerDay - idRestaurante: ' . $idRestaurante . ', fecha: ' . $fecha);
        if($fecha == 'null'){
            $response = Response::json(['error' => ['fecha' => ['Fecha es requerido']]], 200);
            return $response;
        }
        $ventasPorDia = DB::table('venta_productos as vp')->selectRaw("
                CAST(vp.created_at AS TIME) as hora,
                vp.nro_venta as nro_pedido,
                c.nombre_completo as cliente,
                c2.nombre_usuario as atendido_por,
                (SELECT descripcion FROM diccionario_datos WHERE tabla='pagos' AND campo='tipo_pago' AND codigo=p.tipo_pago) as tipo_pago,
                p.importe as importe,
                (SELECT descripcion FROM diccionario_datos WHERE tabla='pagos' AND campo='tipo_servicio' AND codigo=p.tipo_servicio) as tipo_servicio
            ")->leftJoin('clientes as c', 'c.id_cliente', '=', 'vp.id_cliente')
            ->leftJoin('cajeros as c2', 'c2.id_cajero', '=', 'vp.id_cajero')
            ->join('pagos as p', 'p.id_venta_producto', '=', 'vp.id_venta_producto')
            ->join('historial_caja as h', 'h.id_historial_caja', '=', 'vp.id_historial_caja')
            ->join('cajas as cj', 'cj.id_caja', '=', 'h.id_caja')
            ->join('sucursals as s', 's.id_sucursal', '=', 'cj.id_sucursal')
            ->whereDate('vp.created_at', '=', $fecha)
            ->where('s.id_restaurant', $idRestaurante)
            ->where('vp.estado_venta', '=', 0)
            ->orderByDesc('hora')
            ->paginate(10);

        $totalDia = DB::table('venta_productos as vp')
            ->join('pagos as p', 'p.id_venta_producto', '=', 'vp.id_venta_producto')
            ->join('historial_caja as h', 'h.id_historial_caja', '=', 'vp.id_historial_caja')
            ->join('cajas as cj', 'cj.id_caja', '=', 'h.id_caja')
            ->join('sucursals as s', 's.id_sucursal', '=', 'cj.id_sucursal')
            ->whereDate('vp.created_at', $fecha)
            ->where('s.id_restaurant', $idRestaurante)
            ->where('vp.estado_venta', '=', 0)
            ->selectRaw('
                SUM(p.importe) as importe_total,
                SUM(p.importe_base) as importe_neto_total,
                (SUM(p.importe) - SUM(p.importe_base)) as ganancia_total,
                (
                    SELECT COUNT(*) FROM pagos as p
                    inner join venta_productos as vp on vp.id_venta_producto = p.id_venta_producto
                    WHERE p.tipo_pago = 0 and vp.estado_venta = 0
                    AND DATE(p.created_at) = ?
                ) as total_efectivo,
                (
                    SELECT COUNT(*) FROM pagos as p
                    inner join venta_productos as vp on vp.id_venta_producto = p.id_venta_producto
                    WHERE p.tipo_pago = 1 and vp.estado_venta = 0
                    AND DATE(p.created_at) = ?
                ) as total_tarjeta,
                (
                    SELECT COUNT(*) FROM pagos as p
                    inner join venta_productos as vp on vp.id_venta_producto = p.id_venta_producto
                    WHERE p.tipo_pago = 2 and vp.estado_venta = 0
                    AND DATE(p.created_at) = ?
                ) as total_qr
            ', [$fecha, $fecha, $fecha])
            ->first();
        $response = Response::json(['ventasPorDia' => $ventasPorDia, 'totalDia' => $totalDia], 200);
        return $response;
    }

    //Exportar reporte de ventas por día a Excel
    public function getReportePerDayExcel($idRestaurante, $fecha){
        Log::info('exportReportePerDay - idRestaurante: ' . $idRestaurante . ', fecha: ' . $fecha);
        if($fecha == 'null'){
            return response()->json(['error' => ['fecha' => ['Fecha es requerido']]], 200);
        }

        $ventasPorDiaXlsx = DB::table('venta_productos as vp')->selectRaw("
                CAST(vp.created_at AS TIME) as hora,
                vp.nro_venta as nro_pedido,
                c.nombre_completo as cliente,
                c2.nombre_usuario as atendido_por,
                (SELECT descripcion FROM diccionario_datos WHERE tabla='pagos' AND campo='tipo_pago' AND codigo=p.tipo_pago) as tipo_pago,
                p.importe as importe,
                (SELECT descripcion FROM diccionario_datos WHERE tabla='pagos' AND campo='tipo_servicio' AND codigo=p.tipo_servicio) as tipo_servicio
            ")->leftJoin('clientes as c', 'c.id_cliente', '=', 'vp.id_cliente')
            ->leftJoin('cajeros as c2', 'c2.id_cajero', '=', 'vp.id_cajero')
            ->join('pagos as p', 'p.id_venta_producto', '=', 'vp.id_venta_producto')
            ->join('historial_caja as h', 'h.id_historial_caja', '=', 'vp.id_historial_caja')
            ->join('cajas as cj', 'cj.id_caja', '=', 'h.id_caja')
            ->join('sucursals as s', 's.id_sucursal', '=', 'cj.id_sucursal')
            ->whereDate('vp.created_at', '=', $fecha)
            ->where('s.id_restaurant', $idRestaurante)
            ->where('vp.estado_venta', '=', 0)
            ->orderByDesc('hora')
            ->get();

        // Usar Maatwebsite\Excel para exportar
        // Crear un array de arrays para exportar
        $exportData = [];
        foreach ($ventasPorDiaXlsx as $row) {
            $exportData[] = [
                'Hora' => $row->hora,
                'Nro. Pedido' => $row->nro_pedido,
                'Cliente' => $row->cliente,
                'Atendido Por' => $row->atendido_por,
                'Tipo Pago' => $row->tipo_pago,
                'Servicio' => $row->tipo_servicio,
                'Importe' => $row->importe,
            ];
        }

        // Crear una clase exportadora anónima
        $export = new class($exportData) implements FromCollection, WithHeadings {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return collect($this->data); }
            public function headings(): array {
                return ['Hora', 'Nro. Pedido', 'Cliente', 'Atendido Por', 'Tipo Pago', 'Servicio', 'Importe'];
            }
        };

        return Excel::download($export, 'reporte_ventas_'.$fecha.'.xlsx');
    }

    //Exportar reporte de ventas por día a PDF
    public function getReportePerDayPDF(Request $request){
        //$nom_restaurante, $fecha, $sucursal, $caja
        $validated = $request->validate([
            'idRestaurante' => 'required|integer|exists:restaurants,id_restaurant',
            'nombre_restaurante' => 'required|string',
            'fecha' => 'required|date',
            'sucursal' => 'required|string',
            'caja' => 'required|string',
        ]);
        if ($validated === false) {
            return response()->json(['error' => ['validacion' => ['Error de validación de datos de entrada']]], 422);
        }
        Log::info('getReportePerDayPDF - request:', $request->all());
        $idRestaurante = $request->input('idRestaurante');
        $nombre_restaurante = $request->input('nombre_restaurante');
        $fecha = $request->input('fecha');
        $sucursal = $request->input('sucursal');
        $caja = $request->input('caja');
        
        $ventasPorDiaXlsx = DB::table('venta_productos as vp')->selectRaw("
                CAST(vp.created_at AS TIME) as hora,
                vp.nro_venta as nro_pedido,
                c.nombre_completo as cliente,
                c2.nombre_usuario as atendido_por,
                (SELECT descripcion FROM diccionario_datos WHERE tabla='pagos' AND campo='tipo_pago' AND codigo=p.tipo_pago) as tipo_pago,
                p.importe as importe,
                (SELECT descripcion FROM diccionario_datos WHERE tabla='pagos' AND campo='tipo_servicio' AND codigo=p.tipo_servicio) as tipo_servicio
            ")->leftJoin('clientes as c', 'c.id_cliente', '=', 'vp.id_cliente')
            ->leftJoin('cajeros as c2', 'c2.id_cajero', '=', 'vp.id_cajero')
            ->join('pagos as p', 'p.id_venta_producto', '=', 'vp.id_venta_producto')
            ->join('historial_caja as h', 'h.id_historial_caja', '=', 'vp.id_historial_caja')
            ->join('cajas as cj', 'cj.id_caja', '=', 'h.id_caja')
            ->join('sucursals as s', 's.id_sucursal', '=', 'cj.id_sucursal')
            ->whereDate('vp.created_at', '=', $fecha)
            ->where('s.id_restaurant', $idRestaurante)
            ->where('vp.estado_venta', '=', 0)
            ->orderByDesc('hora')
            ->get();

        //Obtiene totales

        $totalDia = DB::table('venta_productos as vp')
            ->join('pagos as p', 'p.id_venta_producto', '=', 'vp.id_venta_producto')
            ->join('historial_caja as h', 'h.id_historial_caja', '=', 'vp.id_historial_caja')
            ->join('cajas as cj', 'cj.id_caja', '=', 'h.id_caja')
            ->join('sucursals as s', 's.id_sucursal', '=', 'cj.id_sucursal')
            ->whereDate('vp.created_at', $fecha)
            ->where('s.id_restaurant', $idRestaurante)
            ->where('vp.estado_venta', '=', 0)
            ->selectRaw('
                SUM(p.importe) as importe_total,
                SUM(p.importe_base) as importe_neto_total,
                (SUM(p.importe) - SUM(p.importe_base)) as ganancia_total,
                (
                    SELECT COUNT(*) FROM pagos as p
                    inner join venta_productos as vp on vp.id_venta_producto = p.id_venta_producto
                    WHERE p.tipo_pago = 0 and vp.estado_venta = 0
                    AND DATE(p.created_at) = ?
                ) as total_efectivo,
                (
                    SELECT COUNT(*) FROM pagos as p
                    inner join venta_productos as vp on vp.id_venta_producto = p.id_venta_producto
                    WHERE p.tipo_pago = 1 and vp.estado_venta = 0
                    AND DATE(p.created_at) = ?
                ) as total_tarjeta,
                (
                    SELECT COUNT(*) FROM pagos as p
                    inner join venta_productos as vp on vp.id_venta_producto = p.id_venta_producto
                    WHERE p.tipo_pago = 2 and vp.estado_venta = 0
                    AND DATE(p.created_at) = ?
                ) as total_qr
            ', [$fecha, $fecha, $fecha])
            ->first();

        $pdf = PDF::loadView('reportes.ventas_por_dia.detalle_general', [
            'nom_restaurante' => $nombre_restaurante,
            'sucursal' => $sucursal,
            'caja' => $caja,
            'ventasPorDia' => $ventasPorDiaXlsx,
            'fecha' => $fecha,
            'totalDia' => $totalDia,
        ]);

        return $pdf->download('ventas_por_dia_detelle_general'.$fecha.'.pdf');
    }

    // Graficos
    public function productoCantidad($idRestaurante, $idCategoria, $fechaIni, $fechaFin){
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
            $productoCantidad = DB::table(DB::raw("function_producto_cantidad(" . $idRestaurante . ", " . $idCategoria . ",'" . $fechaIni . "', '" . $fechaFin . "')"))->get();
            //Table
            $productoCantidadTable = DB::table(DB::raw("function_producto_cantidad(" . $idRestaurante . ", " . $idCategoria . ", '" . $fechaIni . "', '" . $fechaFin . "')"))->paginate(6);
            $response = Response::json(['data' => $productoCantidad, 'dataT' => $productoCantidadTable], 200);
            return $response;
        }else{
            $response = Response::json(['error' => ['ini' => ['Fecha ini debe ser menor que Fecha fin']]], 200);
            return $response;
        }
    }

    public function productoCantidadExcel($idRestaurante, $idCategoria,$fechaIni, $fechaFin){
        if($fechaIni == 'null'){
            $response = Response::json(['error' => ['ini' => ['Fecha ini es requerido']]], 200);
            return $response;
        }
        if($fechaFin == 'null'){
            $response = Response::json(['error' => ['fin' => ['Fecha fin es requerido']]], 200);
            return $response;
        }
        if($fechaIni <= $fechaFin) {
            //Table
            $productoCantidadTable = DB::table(DB::raw("function_producto_cantidad(" . $idRestaurante . ", " . $idCategoria . ", '" . $fechaIni . "', '" . $fechaFin . "')"))->get();
            $exportData = [];
            foreach ($productoCantidadTable as $row) {
                $exportData[] = [
                    'Producto' => $row->nom_producto,
                    'Categoria' => $row->nom_categoria,
                    'Cantidad' => $row->cantidad,
                ];
            }

            // Crear una clase exportadora anónima
            $export = new class($exportData) implements FromCollection, WithHeadings {
                private $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() { return collect($this->data); }
                public function headings(): array {
                    return ['Producto', 'Categoria', 'Cantidad'];
                }
            };
            return Excel::download($export, 'cantidadPorProducto_'.$fechaFin.'.xlsx');
        }else{
            $response = Response::json(['error' => ['ini' => ['Fecha ini debe ser menor que Fecha fin']]], 200);
            return $response;
        }
    }

    public function productoCantidadPDF(Request $request){
        $validated = $request->validate([
            'idRestaurante' => 'required|integer|exists:restaurants,id_restaurant',
            'fechaIni' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaIni',
            'idCategoria' => 'required|integer',
            'restaurante' => 'required|string',
            'sucursal' => 'required|string',
            'caja' => 'required|string',
        ]);
        if ($validated === false) {
            return response()->json(['error' => ['validacion' => ['Error de validación de datos de entrada']]], 422);
        }
        Log::info('productoCantidadPDF - request:', $request->all());
        $idRestaurante = $request->input('idRestaurante');
        $fechaIni = $request->input('fechaIni');
        $fechaFin = $request->input('fechaFin');
        $idCategoria = $request->input('idCategoria');
        $restaurante = $request->input('restaurante');
        $sucursal = $request->input('sucursal');
        $caja = $request->input('caja');
        $chartBase64 = $request->input('chartBase64', null);

        $productoCantidadPDF = DB::table(DB::raw("function_producto_cantidad(" . $idRestaurante . ", " . $idCategoria . ", '" . $fechaIni . "', '" . $fechaFin . "')"))->get();
        $exportData = [];
        foreach ($productoCantidadPDF as $row) {
            $exportData[] = [
                'Producto' => $row->nom_producto,
                'Categoria' => $row->nom_categoria,
                'Cantidad' => $row->cantidad,
            ];
        }

        $pdf = PDF::loadView('reportes.ventas_por_dia.cantidad_producto', [
            'titulo_reporte' => 'REPORTE DE CANTIDAD POR PRODUCTO',
            'nom_restaurante' => $restaurante,
            'sucursal' => $sucursal,
            'caja' => $caja,
            'productoCantidadPDF' => $productoCantidadPDF,
            'fechaIni' => $fechaIni,
            'fechaFin' => $fechaFin,
            'chartBase64' => $chartBase64
        ]);

        return $pdf->download('cantidadPorProducto_'.$fechaFin.'.pdf');
    }

    public function productoImporte($idRestaurante, $idCategoria, $fechaIni, $fechaFin){
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
            $productoImporte = DB::table(DB::raw("function_producto_importe(" . $idRestaurante . ", " . $idCategoria . ",'" . $fechaIni . "', '" . $fechaFin . "')"))->get();
            //Table
            $productoImporteTable = DB::table(DB::raw("function_producto_importe(" . $idRestaurante . ", " . $idCategoria . ", '" . $fechaIni . "', '" . $fechaFin . "')"))->paginate(6);
            $response = Response::json(['data' => $productoImporte, 'dataT' => $productoImporteTable], 200);
            return $response;
        }else{
            $response = Response::json(['error' => ['ini' => ['Fecha ini debe ser menor que Fecha fin']]], 200);
            return $response;
        }
    }

    //productoImportExcel
    public function productoImportExcel($idRestaurante, $idCategoria, $fechaIni, $fechaFin){
        if($fechaIni == 'null'){
            $response = Response::json(['error' => ['ini' => ['Fecha ini es requerido']]], 200);
            return $response;
        }
        if($fechaFin == 'null'){
            $response = Response::json(['error' => ['fin' => ['Fecha fin es requerido']]], 200);
            return $response;
        }
        if($fechaIni <= $fechaFin) {
             //Table
            $productoImporteTable = DB::table(DB::raw("function_producto_importe(" . $idRestaurante . ", " . $idCategoria . ", '" . $fechaIni . "', '" . $fechaFin . "')"))->get();
            $exportData = [];
            foreach ($productoImporteTable as $row) {
                $exportData[] = [
                    'Producto' => $row->nom_producto,
                    'Categoria' => $row->nom_categoria,
                    'Importe' => $row->importe
                ];
            }

            // Crear una clase exportadora anónima
            $export = new class($exportData) implements FromCollection, WithHeadings {
                private $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() { return collect($this->data); }
                public function headings(): array {
                    return ['Producto', 'Categoria', 'Importe'];
                }
            };
            return Excel::download($export, 'gananciaPorProducto_'.$fechaFin.'.xlsx');

        }else{
            $response = Response::json(['error' => ['ini' => ['Fecha ini debe ser menor que Fecha fin']]], 200);
            return $response;
        }
    }

    public function productoImportePDF(Request $request){
        $validated = $request->validate([
            'idRestaurante' => 'required|integer|exists:restaurants,id_restaurant',
            'fechaIni' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaIni',
            'idCategoria' => 'required|integer',
            'restaurante' => 'required|string',
            'sucursal' => 'required|string',
            'caja' => 'required|string',
        ]);
        if ($validated === false) {
            return response()->json(['error' => ['validacion' => ['Error de validación de datos de entrada']]], 422);
        }
        Log::info('productoImportePDF - request:', $request->all());
        $idRestaurante = $request->input('idRestaurante');
        $fechaIni = $request->input('fechaIni');
        $fechaFin = $request->input('fechaFin');
        $idCategoria = $request->input('idCategoria');
        $restaurante = $request->input('restaurante');
        $sucursal = $request->input('sucursal');
        $caja = $request->input('caja');
        $chartBase64 = $request->input('chartBase64', null);

        $productoImportePDF = DB::table(DB::raw("function_producto_importe(" . $idRestaurante . ", " . $idCategoria . ", '" . $fechaIni . "', '" . $fechaFin . "')"))->get();
        $exportData = [];
        foreach ($productoImportePDF as $row) {
            $exportData[] = [
                'Producto' => $row->nom_producto,
                'Categoria' => $row->nom_categoria,
                'Importe' => $row->importe
            ];
        }

        $pdf = PDF::loadView('reportes.ventas_por_dia.importe_producto', [
            'titulo_reporte' => 'REPORTE DE GANANCIA POR PRODUCTO',
            'nom_restaurante' => $restaurante,
            'sucursal' => $sucursal,
            'caja' => $caja,
            'productoImportePDF' => $productoImportePDF,
            'fechaIni' => $fechaIni,
            'fechaFin' => $fechaFin,
            'chartBase64' => $chartBase64
        ]);

        return $pdf->download('gananciaPorProducto_'.$fechaFin.'.pdf');
    }
}
