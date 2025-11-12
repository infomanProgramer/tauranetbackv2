<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\user;
use App\Restaurant;
use App;


class PdfController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("pdf.listado_reportes");
    }


    public function crearPDF($datos,$vistaurl,$tipo)
    {

        $data = $datos;
        $date = date('Y-m-d');
        $view =  \View::make($vistaurl, compact('data', 'date'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);

        if($tipo==1){return $pdf->stream('reporte');}
        if($tipo==2){return $pdf->download('reporte.pdf'); }
    }


    public function crear_reporte_porpais($tipo){

        $vistaurl="pdf.reporte_por_pais";
        $paises=Pais::all();

        return $this->crearPDF($paises, $vistaurl,$tipo);
    }


    public function reportePDF($id){
        \Log::info($id);
        $pdf = App::make('dompdf.wrapper');
        //$pdf = new Dompdf();
        $html = '<div style="margin: 0px;  background-color: red;">
                    <h2>titulo</h2>
                    <ul>
                        <li>uno</li>
                        <li>dos</li>
                        <li>tres</li> 
                        <li>uno</li>
                        <li>dos</li>
                        <li>tres</li>
                        <li>uno</li>
                        <li>dos</li>
                        <li>tres</li> 
                        <li>uno</li>
                        <li>dos</li>
                        <li>tres</li> 
                        <li>uno</li>
                        <li>dos</li>
                        <li>tres</li> 
                        <li>uno</li>
                        <li>dos</li>
                        <li>tres</li>    
                    </ul>
                </div>';
        $pdf->loadHTML($html);
        \Log::info($this->computedHeight($html));
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setPaper(array(0,0,226.77,$this->computedHeight($html)), 'portrait');
        return $pdf->stream();
        //$pdf->download('invoice.pdf');
    }

    public function computedHeight($html){
        $height = 0;
        $pdf = new Dompdf();
        //$pdf = App::make('dompdf.wrapper');
        $pdf->setPaper(array(0,0,226.77,226.77), 'portrait');
        $pdf->loadHtml($html);
        $pdf->render();
        $page_count = $pdf->getCanvas()->get_page_number();
        \Log::info($page_count);
        return $page_count*226.77;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
