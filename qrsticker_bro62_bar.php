<?php

session_start(); // Must start session first thing
// Check if user is logged in
if (!isset($_SESSION['id'])) {
   echo '<font color="#FF00CC">Prosz&#281; <a href="login.php">zaloguj si&#281;</a> do swojego konta.</font><br /><br />';
   exit();
} else {

// Database search precess
include_once "connect_to_mysqli_snst.php";

if (isset($_SESSION['contrid'])) {
    $contrid = $_SESSION['contrid'];
      $tagid = $_SESSION['tagid'];
$resultlegal = $mysqlist->query("SELECT target FROM snst.contract WHERE id='$contrid'");
       $obj1 = $resultlegal->fetch_object();
 $qrcontract = $obj1->target;
      $group = $_SESSION['tablest'];
}

$resultst    = $mysqlist->query("SELECT sdnst,loc FROM snst.$group WHERE contrid='$contrid' AND tag LIKE '$tagid' ORDER BY loc");
$num_rows_st = $resultst->num_rows;

   $changeuser = $_SESSION['user'];
   $changedate = date("Y-m-d H:i");
   $changetype = 'QR codes printed';
   $changecont = 'QR do kontraktu: ' .$qrcontract;
   // Register a change in a changelog
   //$sql = $mysqlist->query("INSERT INTO sdnadmin.userlog (user, date, type, content) VALUES ('$changeuser','$changedate','$changetype','$changecont')");


mysqli_close();
unset($_SESSION['contrid']);
unset($_SESSION['tablest']);

require('tfpdf/tfpdf.php');

class nPDF extends tFPDF
{

function QRSticker()
{

    global $resultst;
    global $num_rows_st;

    //Add first page
    $this->AddPage();

    //Set initial y axis position per page
    $y_axis_initial = 1;

    //Set initial axis for page
    $this->SetY($y_axis_initial);

    //Initialize counter
    $i = 0;

    //Set initial x axis position for 1st column
    $x_axis = 1;

    //Set maximum records per column
    $xmax = 6;

    //Set Row Height
    $row_height = 28;


    while($row = $resultst->fetch_array()) {
    $column_loc = $row['loc'];
    $column_st = $row['sdnst'];
    $column_note1 = file_get_contents('tfpdf/proqupa/sdn_ap1.txt');
    $column_note2 = file_get_contents('tfpdf/proqupa/sdn_ap2.txt');
    $column_note3 = file_get_contents('tfpdf/proqupa/sdn_ap3.txt');
    $column_note4 = file_get_contents('tfpdf/proqupa/sdn_ap4.txt');


    //Set initial column margin
    $this->SetY($y_axis_initial);

    $y_axis_o = $y_axis_initial;

        $this->SetFont('arial','',7);
        $this->SetY($y_axis_o+1.4);
        $this->SetX($x_axis+23);
        $this->MultiCell(35,20.7,'',1,'C');

        $this->Image('qrcodes/' .$column_st. '.png',$x_axis,$y_axis_o+0.5,22.5,22.5);

        $this->SetFont('arial','',5);
        $this->SetY($y_axis_o+22.8);
        $this->SetX($x_axis);
        $this->MultiCell(14,2,$column_st,0,'L');

        $this->SetFont('arial','BD',9.5);
        $this->SetY($y_axis_o+2);
        $this->SetX($x_axis+23.5);
        $this->MultiCell(35,4,$column_note1,0,'C');

        $this->SetFont('arial','BD',7.5);
        $this->SetY($y_axis_o+7);
        $this->SetX($x_axis+23.5);
        $this->MultiCell(35,3.8,$column_note2,0,'C');

        $this->SetFont('arial','BD',7.5);
        $this->SetY($y_axis_o+10);
        $this->SetX($x_axis+23.5);
        $this->MultiCell(35,3.8,$column_note3,0,'C');

        $this->SetFont('arial','BD',8.5);
        $this->SetY($y_axis_o+17);
        $this->SetX($x_axis+23.5);
        $this->MultiCell(35,3.8,$column_note4,0,'C');

        $this->SetFillColor(232,232,232);
        $this->SetFont('arial','',5);
        $this->SetY($y_axis_o+22.8);
        $this->SetX($x_axis+10);
        $this->MultiCell(48,2,$column_loc,0,'C');

    $i = $i + 1;
      if ($i < $num_rows_st) {
            //Add a new page
            $this->AddPage();
      } // exits $i < numrows
    } // exits 'while' loop
} // exits function SNTableSt
} // exits class nPDF

//create the fpdf object and do some initialization
$pdf = new nPDF("L","mm",array(28,62));
//Disable automatic page break
$pdf->SetAutoPageBreak(false);
$pdf->SetDisplayMode(fullpage,single);

//set default font
$pdf->AddFont('arial','','arial.ttf',true);
$pdf->AddFont('arial','I','ariali.ttf',true);
$pdf->AddFont('arial','BD','arialbd.ttf',true);
$pdf->AddFont('arial','BI','arialbi.ttf',true);

$pdf->QRSticker();

//$pdf->Output();
$pdf->Output('qrkody_'.$qrcontract.'.pdf','D');
}
?>
