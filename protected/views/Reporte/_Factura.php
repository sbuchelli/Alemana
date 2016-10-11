<?php

Yii::import("ext.PHPExcel.XPHPExcel");
$objPHPExcel = XPHPExcel::createPHPExcel();

set_time_limit(900);

$objPHPExcel->getProperties()->setCreator("Arunsri")
        ->setLastModifiedBy("Arunsri")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'N° de Factura')
        ->setCellValue('B1', 'Cliente')
        ->setCellValue('C1', 'Fecha Facturada')
        ->setCellValue('D1', 'Fecha de Pago')
        ->setCellValue('E1', 'Estado')
        ->setCellValue('F1', 'N° de Guia Documento')
        ->setCellValue('G1', 'N° de O/C')
        ->setCellValue('H1', 'Total');

$sql = "SELECT COD_FACT,
 (SELECT DES_CLIE FROM MAE_CLIEN Z WHERE Z.COD_CLIE= Y.COD_CLIE) CLIENTE,
 FEC_FACT,
 Y.FEC_PAGO,
 CASE  Y.IND_ESTA
    WHEN Y.IND_ESTA='1' THEN 'PENDIENTE PAGO'
    WHEN Y.IND_ESTA='2' THEN 'COBRADO'
    WHEN Y.IND_ESTA='9' THEN 'ANULADO'
   END AS  ESTADO,
  Z.COD_GUIA,
  M.NUM_ORDE,
  Y.TOT_FACT
FROM fac_factu Y,fac_guia_remis Z,fac_orden_compr M
WHERE Y.COD_GUIA= Z.COD_GUIA
 AND M.COD_ORDE= Z.COD_ORDE;";
$datas = Yii::app()->db->createCommand($sql)->queryAll();

for ($i = 0; $i < count($datas); $i++) {
    $j = $i + 2;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $j, $datas[$i]['COD_FACT'])
            ->setCellValue('B' . $j, $datas[$i]['CLIENTE'])
            ->setCellValue('C' . $j, $datas[$i]['FEC_FACT'])
            ->setCellValue('D' . $j, $datas[$i]['FEC_PAGO'])
            ->setCellValue('E' . $j, $datas[$i]['ESTADO'])
            ->setCellValue('F' . $j, $datas[$i]['COD_GUIA'])
            ->setCellValue('G' . $j, $datas[$i]['NUM_ORDE'])
            ->setCellValue('H' . $j, $datas[$i]['TOT_FACT']);
}

$styleArray = array(
    'font' => array(
        'bold' => true,
        'color' => array('rgb' => '#000000'),
        'name' => 'Arial'
    ),
    
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
    ),

);

//$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow('A','H')->setWidth('24');
$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray);


$objPHPExcel->getSheet(0)->setTitle("Facturas-" . date('d-m-Y'));

$objPHPExcel->setActiveSheetIndex(0);

$reporte = ob_get_clean();
header("Content-Type: application/vnd.ms-excel; charset=iso-8859-1");
header("Content-Disposition: attachment; filename=Facturas-" . date('d-m-Y') . ".xls");
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
Yii::app()->end();
echo $reporte;

//$conn = mysqli_connect('sispaal.cnjv4vhhy3or.us-west-2.rds.amazonaws.com', 'root', 'root2016', 'SIS_PANA', '3306');
//if (!$conn) {
//    die('Could not connect to MySQL: ' . mysqli_connect_error());
//}
//mysqli_query($conn, 'SET NAMES \'utf8\'');
//
//
//set_time_limit(900);
//
////$connection = Yii::app()->db;
//$sqlStatement = "SELECT COD_FACT,
// (SELECT DES_CLIE FROM MAE_CLIEN Z WHERE Z.COD_CLIE= Y.COD_CLIE) CLIENTE,
// FEC_FACT,
// Y.FEC_PAGO,
// CASE  Y.IND_ESTA
//    WHEN Y.IND_ESTA='1' THEN 'PENDIENTE PAGO'
//    WHEN Y.IND_ESTA='2' THEN 'COBRADO'
//    WHEN Y.IND_ESTA='9' THEN 'ANULADO'
//   END AS  ESTADO,
//  Z.COD_GUIA,
//  M.NUM_ORDE,
//  Y.TOT_FACT
//FROM fac_factu Y,fac_guia_remis Z,fac_orden_compr M
//WHERE Y.COD_GUIA= Z.COD_GUIA
// AND M.COD_ORDE= Z.COD_ORDE
// ;";
//
//$qry = mysqli_query($conn, $sqlStatement);
//$campos = mysqli_num_fields($qry);
////$command = $connection->createCommand($sqlStatement);
////$reader = $command->query();
//
//ob_start();
//echo "<table border=\"1\" align=\"center\">";
//echo "<tr bgcolor=\"#FFE699\">
//  <td align=\"center\"><font color=\"#000000\"><strong>N&#176 de Factura</strong></font></td>
//  <td align=\"center\"><font color=\"#000000\"><strong>Cliente</strong></font></td>
//  <td align=\"center\"><font color=\"#000000\"><strong>Fecha Facturada/Celular</strong></font></td>
//  <td align=\"center\"><font color=\"#000000\"><strong>Fecha de Pago</strong></font></td>
//  <td align=\"center\"><font color=\"#000000\"><strong>Estado</strong></font></td>
//  <td align=\"center\"><font color=\"#000000\"><strong>N&#176 de Guia Documento</strong></font></td>
//  <td align=\"center\"><font color=\"#000000\"><strong>N&#176 de O/C</strong></font></td>
//  <td align=\"center\"><font color=\"#000000\"><strong>Total</strong></font></td>
//</tr>";
//
//while($row=mysqli_fetch_array($qry))
//{
//    echo "<tr>";
//     for($j=0; $j<$campos; $j++) {
//         echo "<td align=\"left\">".$row[$j]."</td>";
//     }
//     echo "</tr>";
////     
////while ($row = $reader->read()) {
////
////
////     
//}
//
//echo "</table>";
//$reporte = ob_get_clean();
//header("Content-type: application/vnd.ms-excel; charset=iso-8859-1");
//header("Content-Disposition: attachment; filename=Facturas-" . date('d-m-Y') . ".xls");
//header("Pragma: no-cache");
//header("Expires: 0");
//echo $reporte;
?>