<?php
class RMemoriaProgramacionXls
{
	private $docexcel;
	private $objWriter;
	private $nombre_archivo;
	private $hoja;
	private $columnas=array();
	private $fila;
	private $equivalencias=array();
	
	private $indice, $m_fila, $titulo;
	private $swEncabezado=0; //variable que define si ya se imprimi� el encabezado
	private $objParam;
	public  $url_archivo;
	
	var $datos_titulo;
	var $datos_detalle;
	var $ancho_hoja;
	var $gerencia;
	var $numeracion;
	var $ancho_sin_totales;
	var $cantidad_columnas_estaticas;
	var $s1;
	var $t1;
	var $tg1;
	var $total;
	var $datos_entidad;
	var $datos_periodo;
	var $ult_codigo_partida;
    var $ult_concepto;
	var $totales_c1 = 0;
	var $totales_c2 = 0;
	var $totales_c3 = 0;
	var $totales_c4 = 0;
    var $totales_c5 = 0;
    var $totales_c6 = 0;
    var $totales_c7 = 0;
    var $totales_c8 = 0;
    var $totales_c9 = 0;
    var $totales_c10 = 0;    
    var $totales_c11 = 0; 
    var $totales_c12 = 0;
    var $total_general = 0;    	
	
	
	
	function __construct(CTParametro $objParam){
		$this->objParam = $objParam;
		$this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
		//ini_set('memory_limit','512M');
		set_time_limit(400);
		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize'  => '10MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$this->docexcel = new PHPExcel();
		$this->docexcel->getProperties()->setCreator("PXP")
							 ->setLastModifiedBy("PXP")
							 ->setTitle($this->objParam->getParametro('titulo_archivo'))
							 ->setSubject($this->objParam->getParametro('titulo_archivo'))
							 ->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Report File");
							 
		$this->docexcel->setActiveSheetIndex(0);
		
		$this->docexcel->getActiveSheet()->setTitle($this->objParam->getParametro('titulo_archivo'));
		
		$this->equivalencias=array(0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
								9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
								18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
								26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
								34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
								42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
								50=>'AY',51=>'AZ',
								52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
								60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
								68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
								76=>'BY',77=>'BZ');		
									
	}
	
	function datosHeader ( $detalle, $totales, $gestion,$dataEmpresa) {
		
		$this->datos_detalle = $detalle;
		$this->datos_titulo = $totales;
		$this->datos_entidad = $dataEmpresa;
		$this->datos_gestion = $gestion;
		
		
	}
			
	function imprimeDatos(){
		$datos = $this->datos_detalle;
		
		$config = $this->objParam->getParametro('config');
		$columnas = 0;
		
		
		$styleTitulos = array(
							      'font'  => array(
							          'bold'  => true,
							          'size'  => 8,
							          'name'  => 'Arial'
							      ),
							      'alignment' => array(
							          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
							      ),
								   'fill' => array(
								      'type' => PHPExcel_Style_Fill::FILL_SOLID,
								      'color' => array('rgb' => 'c5d9f1')
								   ),
								   'borders' => array(
								         'allborders' => array(
								             'style' => PHPExcel_Style_Border::BORDER_THIN
								         )
								     ));

       $this->docexcel->getActiveSheet()->getStyle('A1:O1')->applyFromArray($styleTitulos);
		
		//*************************************Cabecera*****************************************
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[0])->setWidth(20);		
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,1,'Código');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[1])->setWidth(50);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,1,'Partida');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[2])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,1,'Enero');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[3])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,1,'Febrero');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[4])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,1,'Marzo');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[5])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,1,'Abril');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[6])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,1,'Mayo');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[7])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,1,'Junio');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[8])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,1,'Julio');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[9])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,1,'Agosto');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[10])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,1,'Septiembre');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[11])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,1,'Octubre');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[12])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,1,'Noviembre');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[13])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,1,'Diciembre');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[14])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,1,'Total');
		//*************************************Fin Cabecera*****************************************
		
		$fila = 2;
		$contador = 1;
		
        /////////////////////***********************************Detalle***********************************************
        $this->docexcel->getActiveSheet()->getStyle('C:O')->getNumberFormat()->setFormatCode('#,##0.00');

		foreach($datos as $value) {

            $this->totales_c1 += $value['c1'];
            $this->totales_c2 += $value['c2'];
            $this->totales_c3 += $value['c3'];
            $this->totales_c4 += $value['c4'];
            $this->totales_c5 += $value['c5'];
            $this->totales_c6 += $value['c6'];
            $this->totales_c7 += $value['c7'];
            $this->totales_c8 += $value['c8'];
            $this->totales_c9 += $value['c9'];
            $this->totales_c10 += $value['c10'];    
            $this->totales_c11 += $value['c11']; 
            $this->totales_c12 += $value['c12'];                        				
            							
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$fila,$value['codigo_partida']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$fila,$value['nombre_partida']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$fila,$value['c1']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$fila,$value['c2']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,$value['c3']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,$value['c4']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$fila,$value['c5']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,$value['c6']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$fila,$value['c7']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$fila,$value['c8']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$fila,$value['c9']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$fila,$value['c10']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$fila,$value['c11']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$fila,$value['c12']);			
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$fila,"=SUM(C".$fila.":N".$fila.")");
			
			$fila++;
			$contador++;
        }
        if( $this->objParam->getParametro('nivel') == 5){

        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':O'.$fila.'')->applyFromArray($styleTitulos);        
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$fila,'-');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$fila,'TOTALES');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$fila,$this->totales_c1);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$fila,$this->totales_c2);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,$this->totales_c3);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,$this->totales_c4);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$fila,$this->totales_c5);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,$this->totales_c6);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$fila,$this->totales_c7);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$fila,$this->totales_c8);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$fila,$this->totales_c9);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$fila,$this->totales_c10);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$fila,$this->totales_c11);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$fila,$this->totales_c12);			
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$fila,"=SUM(O2:O".$fila.")");
        }
		//************************************************Fin Detalle**********************************************
		
	}

	
	
	function generarReporte(){
		
		$this->imprimeDatos();
		
		//echo $this->nombre_archivo; exit;
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$this->docexcel->setActiveSheetIndex(0);
		$this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
		$this->objWriter->save($this->url_archivo);		
		
		
	}	
	

}

?>