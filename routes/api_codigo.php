<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Rutas de prueba

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    //Autenticación Super Administrador
    Route::post('login-sadmin', 'SuperAdminAuthController@login');
    Route::post('logout-sadmin', 'SuperAdminAuthController@logout');
    Route::post('refresh-sadmin', 'SuperAdminAuthController@refresh');
    Route::post('me-sadmin', 'SuperAdminAuthController@me');

    //Autenticación Administrador
    Route::post('login-administrador', 'AdministradorAuthController@login')->middleware('restaurante_activo');
    Route::post('logout-administrador', 'AdministradorAuthController@logout');
    Route::post('refresh-administrador', 'AdministradorAuthController@refresh');
    Route::post('me-administrador', 'AdministradorAuthController@me');

    //Autenticacion Cajero
    Route::post('login-cajero', 'CajeroAuthController@login')->middleware('restaurante_activo', 'usuarios_activos');
    Route::post('logout-cajero', 'CajeroAuthController@logout');
    Route::post('refresh-cajero', 'CajeroAuthController@refresh');
    Route::post('me-cajero', 'CajeroAuthController@me');

    //Autenticacion Mozo
    Route::post('login-mozo', 'MozoAuthController@login')->middleware('restaurante_activo', 'usuarios_activos');
    Route::post('logout-mozo', 'MozoAuthController@logout');
    Route::post('refresh-mozo', 'MozoAuthController@refresh');
    Route::post('me-mozo', 'MozoAuthController@me');

    //Autenticacion Cocinero
    Route::post('login-cocinero', 'CocineroAuthController@login')->middleware('restaurante_activo', 'usuarios_activos');
    Route::post('logout-cocinero', 'CocineroAuthController@logout');
    Route::post('refresh-cocinero', 'CocineroAuthController@refresh');
    Route::post('me-cocinero', 'CocineroAuthController@me');

    //Rutas de Super Administrador
    Route::group(['middleware' => 'auth:sadmin'], function () {
        Route::get('restaurant/page/{pag}/{act}', 'RestaurantController@index');
        Route::post('restaurant', 'RestaurantController@store');
        Route::put('restaurant/{id}', 'RestaurantController@update');
        Route::get('restaurantall/', 'RestaurantController@getallrestaurants');

        Route::get('sucursal/all/{pag}', 'SucursalController@todasSucursales');

        Route::post('sucursal', 'SucursalController@store');
        Route::get('sucursal/{id}', 'SucursalController@show');
        Route::put('sucursal/{id}', 'SucursalController@update');

        Route::get('suscripcion', 'SuscripcionController@index');

        Route::get('administrador/page/{pag}', 'AdministradorController@index');
        Route::post('administrador', 'AdministradorController@store');
        Route::get('administrador/{id}', 'AdministradorController@show');
        Route::put('administrador/{id}', 'AdministradorController@update');
        Route::get('administrador/restaurant/{pag}/{id}/', 'AdministradorController@administradorPorRestaurante');

        Route::get('imageupload/{id}/tipo/{tipo}', 'PerfilimagenController@show');
        Route::post('imageupload', 'PerfilimagenController@uploadImagePerfil');
        Route::delete('imageupload/{id}', 'PerfilimagenController@destroy');
        Route::put('imageupload/{idImg}/usuario/{idUsr}/tipo/{tipo}', 'PerfilimagenController@updateImagePerfil');
        Route::get('getallcajas/{pag}', 'CajaController@index');

        //CRUD de Caja
        Route::get('caja/{id}', 'CajaController@show');
        Route::post('caja', 'CajaController@store');
        Route::put('caja/{id}', 'CajaController@update');
        Route::delete('caja/{id}', 'CajaController@destroy');
        
        //CRUD de Cajero
        Route::post('cajero', 'CajeroController@store');
        Route::get('cajero/{id}', 'CajeroController@show');
        Route::put('cajero/{id}', 'CajeroController@update');
        Route::delete('cajero/{id}', 'CajeroController@destroy');
        Route::get('getallcajeros/{id_restaurant}/{id_sucursal}', 'PersonalController@getAllCajeros');
    });

    //Rutas Administrador, SuperAdministrador
    Route::group(['middleware' => 'auth:sadmin,admin'], function () {
        Route::get('sucursal/restaurant/{pag}/', 'SucursalController@sucursalPorRestaurante');
        Route::get('restaurant/{id}', 'RestaurantController@show');
    });

    //Rutas Administrador, Mozo, Cajero, Cocinero
    Route::group(['middleware' => 'auth:mozo,admin,cajero,cocinero'], function () {
        Route::get('restaurantedatos/user/{id}/type/{type_useempleadoPedidor}', 'RestaurantController@getDatosRestauranteSucursal');
        Route::get('productovendido/pedido/{idVentaProducto}', 'ProductoVendidoController@index');
    });

    //Rutas Administrador, Mozo, Cajero
    Route::group(['middleware' => 'auth:mozo,admin,cajero'], function () {
        Route::get('cproducto/sucursal/{idSucursal}', 'CategoriaProductoController@combopersonal');
        Route::get('productoall/sucursal/{idSucursal}/categoria/{idCategoria}', 'ProductoController@listaProductos');
        Route::get('cliente/sucursal/{idSucursal}/identificador/{dni}', 'ClienteController@index');
        Route::post('cliente', 'ClienteController@store')->middleware('pedidos_habilitados');
        Route::post('productovendido', 'ProductoVendidoController@store');
        Route::get('ventaproductos/pedido/{idPedido}', 'VentaProductoController@getPedido');
    });

    //Rutas Administrador, SuperAdministrador, Mozo, Cajero
    Route::group(['middleware' => 'auth:mozo,admin,cajero,sadmin'], function () {
        Route::get('getallsucursalcombo', 'CajaController@getAllSucursals');
        Route::get('personal/{idRestaurant}/page/{pag}/sucursal/{idSucursal}', 'PersonalController@filtra_personal');
        Route::get('allcajas/{idSucursal}', 'CajaController@allcajas');
        Route::get('allcajas', 'CajaController@allallcajas');
        Route::post('changepassword/usuario/{id}/typeuser/{type_user}', 'ChangePasswordController@changePassword');
    });

    //Mozo Routes
    Route::group(['middleware' => 'auth:mozo'], function () {
        Route::get('ventaproductos/mozo/{idMozo}/sucursal/{idSucursal}', 'VentaProductoController@indexMozo'); // Shows the order history
    });

    //Rutas cocinero
    Route::group(['middleware' => 'auth:cocinero'], function () {
        Route::get('ventaproductos/sucursal/{idSucursal}', 'VentaProductoController@getPedidosCocinero');//Muestra el historial de pedidos
        Route::get('estadoatendido/{idVentaProducto}', 'VentaProductoController@cambiaEstadoAtendido');
    });

    //Rutas Cajero
    Route::group(['middleware' => 'auth:cajero'], function () {
        Route::get('historialcaja/caja/{idCaja}/page/{pag}', 'HistorialCajaController@index');
        Route::post('historialcaja/{idCaja}', 'HistorialCajaController@store');
        Route::put('cashclosing/{id}', 'HistorialCajaController@cashClosing');
        Route::get('ventaproductos/caja/{idCaja}', 'VentaProductoController@index');//Muestra el historial de pedidos
        Route::post('pago', 'PagoController@store');
        Route::get('updatecashamount/{idHistorialCaja}', 'HistorialCajaController@updateCashAmount');
        Route::post('clientepago', 'ClienteController@storePago');
        //Ex rutas de Administrador (Categoría Productos)
        Route::get('cproducto/{id}', 'CategoriaProductoController@show');
        Route::post('cproducto', 'CategoriaProductoController@store');
        Route::put('cproducto/{id}', 'CategoriaProductoController@update');
        Route::get('cproducto/{pag}/restaurant/{idRestaurante}', 'CategoriaProductoController@index');
        Route::get('cproducto/restaurant/{idRestaurante}', 'CategoriaProductoController@combo');
        Route::delete('cproducto/{id}', 'CategoriaProductoController@destroy');
        //Ex rutas de Administrador (Productos)
        Route::get('producto/{pag}/restaurant/{idRestaurante}/categoria/{idCategoria}', 'ProductoController@index');
        Route::get('productoexcel/{idRestaurante}/categoria/{idCategoria}', 'ProductoController@exportProductsToExcel');
        Route::get('producto/{id}', 'ProductoController@show');
        Route::post('producto', 'ProductoController@store');
        Route::post('productoupdate/{id}', 'ProductoController@update');
        Route::delete('producto/{id}', 'ProductoController@destroy');
        Route::delete('delimage/{id}', 'ProductoController@destroyImage');
        //Ex rutas de Administrador (Reportes)
        Route::get('aperturacajas/{idRestaurante}/sucursal/{idSucursal}', 'ReporteController@aperturaCajas');
        Route::get('cliente/restaurant/{idRestaurant}', 'ClienteController@getClientesByRestaurant');
        //Detalle ventas por pedido
        Route::get('detalleventas/{idRestaurante}/fechaini/{fecha_ini}/fechafin/{fecha_fin}/tiporeporte/{tipoReporte}', 'ReporteController@detalleVentas');
        Route::post('detalleventaspdf', 'ReporteController@detalleVentasPDF');
        //Detalle ventas por productos
        Route::get('detalleproductos/{idRestaurante}/fechaini/{fecha_ini}/fechafin/{fecha_fin}/tiporeporte/{tipoReporte}', 'ReporteController@detalleProducto');
        Route::get('detalleproductosexcel/{idRestaurante}/fechaini/{fecha_ini}/fechafin/{fecha_fin}/tiporeporte/{tipoReporte}', 'ReporteController@detalleProducto');
        Route::post('detalleproductospdf', 'ReporteController@detalleProductoPDF');
        //Detalle ventas por empleado
        Route::get('empleadopedido/{idRestaurante}/fechaini/{fechaIni}/fechafin/{fechaFin}', 'ReporteController@empleadoPedido');
        //Route::get('fechaimporte/{idRestaurante}/fechaini/{fechaIni}/fechafin/{fechaFin}', 'ReporteController@fechaImporte');
        Route::get('fechapedido/{idRestaurante}/fechaini/{fechaIni}/fechafin/{fechaFin}', 'ReporteController@fechaPedidos');
        Route::get('reportehoy/{idRestaurante}', 'ReporteController@getReportesHoy');
        Route::get('getgestionespagos', 'CajeroController@getGestionesPagos');
        Route::get('sucursalcombo/{idRestaurant}', 'CajaController@sucursalPerRestaurant');

        //REPORTES DE VENTAS POR DIA
        // Tabla detalle general
        Route::get('getreporteperday/{idRestaurante}/fecha/{fecha}', 'ReporteVentasPorDiaController@getReportePerDay');
        Route::get('getreporteperdayexcel/{idRestaurante}/fecha/{fecha}', 'ReporteVentasPorDiaController@getReportePerDayExcel');
        Route::post('getreporteperdaypdf', 'ReporteVentasPorDiaController@getReportePerDayPDF');
        
        //Grafico Cantidad por producto
        Route::get('productocantidad/{idRestaurante}/categoria/{idCategoria}/fechaini/{fechaIni}/fechafin/{fechaFin}', 'ReporteVentasPorDiaController@productoCantidad');
        Route::get('productocantidadexcel/{idRestaurante}/categoria/{idCategoria}/fechaini/{fechaIni}/fechafin/{fechaFin}', 'ReporteVentasPorDiaController@productoCantidadExcel');
        Route::post('productocantidadpdf', 'ReporteVentasPorDiaController@productoCantidadPDF');

        //Grafico Ganancia por producto
        Route::get('productoimporte/{idRestaurante}/categoria/{idCategoria}/fechaini/{fechaIni}/fechafin/{fechaFin}', 'ReporteVentasPorDiaController@productoImporte');
        Route::get('productoimportexcel/{idRestaurante}/categoria/{idCategoria}/fechaini/{fechaIni}/fechafin/{fechaFin}', 'ReporteVentasPorDiaController@productoImportExcel');
        Route::post('productoimportepdf', 'ReporteVentasPorDiaController@productoImportePDF');

        //REPORTES DE VENTAS POR INTERVALO DE FECHA
        // Tabla detalle general
        Route::post('getreportbyinterval', 'ReporteVentasPorRangoFechaController@getReportByInterval');
        Route::post('getreportbyintervalexcel', 'ReporteVentasPorRangoFechaController@getReportByIntervalExcel');
        Route::post('getreportbyintervalpdf', 'ReporteVentasPorRangoFechaController@getReportByIntervalPDF');

        //Ventas por fecha
        Route::post('fechaimporte', 'ReporteVentasPorRangoFechaController@fechaImporte');
        Route::post('fechaimporteexcel', 'ReporteVentasPorRangoFechaController@fechaImporteExcel');
        Route::post('fechaimportepdf', 'ReporteVentasPorRangoFechaController@fechaImportePDF');

        //Ventas por semana
        Route::post('semanaimporte', 'ReporteVentasPorRangoFechaController@semanaImporte');

        //Ventas por mes
        Route::post('mesimporte', 'ReporteVentasPorRangoFechaController@mesImporte');

        //Comanda
        Route::post('printcomanda', 'ComandaController@verComanda');

        //Ventas
        Route::post('updateventaproducto', 'VentaProductoController@updateVentaProducto');
    });

    //Rutas Administrador
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('personal/{idRestaurant}/sucursal/{idSucursal}/perfil/{perfil}', 'PersonalController@filtra_personal_all');
        Route::post('mozo', 'MozoController@store')->middleware('mozos_habilitados');
        Route::get('mozo/{id}', 'MozoController@show');
        Route::put('mozo/{id}', 'MozoController@update');
        Route::delete('mozo/{id}', 'MozoController@destroy');

        Route::post('cocinero', 'CocineroController@store')->middleware('cocineros_habilitados');
        Route::get('cocinero/{id}', 'CocineroController@show');
        Route::put('cocinero/{id}', 'CocineroController@update');
        Route::delete('cocinero/{id}', 'CocineroController@destroy');
        
        Route::put('updatemoneda/{id}', 'RestaurantController@updateMoneda');
        Route::put('udpateidentificacacion/{id}', 'RestaurantController@updateIdentificacion');
    });
});
