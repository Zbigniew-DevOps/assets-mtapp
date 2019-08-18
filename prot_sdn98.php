<?php

session_start(); // Must start session first thing
// Check if user is logged in
if (!isset($_SESSION['id'])) {
   echo '<font color="#FF00CC">Prosz&#281; <a href="login.php">zaloguj si&#281;</a> do swojego konta.</font><br /><br />';
   exit();
} else {

if (isset($_SESSION['inputField'])) {
    $contract = $_SESSION['contract'];
    $inputField = $_SESSION['inputField'];
}

// Database search precess
include_once "connect_to_mysqli_snst.php";
//include_once "connect_to_mysql_snst.php";

//$order = "SELECT * FROM snst.st WHERE target='$contract' ORDER BY loc";
//$orderst = "SELECT * FROM snst.st WHERE target='$contract' ORDER BY sdnst, vendor";
//$orderitsast = "SELECT * FROM snst.qupast WHERE target='$contract' ORDER BY qupast, vendor";
$orderst     = "SELECT magmall.items.vendor, magmall.items.cust_name, snst.st.sn, snst.st.sdnst, magmall.items.custprice FROM snst.st, magmall.items
                WHERE snst.st.target='$contract' AND snst.st.itemid=magmall.items.id
                UNION ALL
                SELECT magmall.items.vendor, magmall.items.cust_name, snst.stextprojects.sn, snst.stextprojects.sdnst, magmall.items.custprice FROM snst.stextprojects, magmall.items
                WHERE snst.stextprojects.target='$contract' AND snst.stextprojects.itemid=magmall.items.id ORDER BY loc";
$orderitsast = "SELECT magmall.items.vendor, magmall.items.cust_name, snst.qupast.sn, snst.itsast.qupast, magmall.items.custprice FROM snst.qupast, magmall.items
                WHERE snst.itsast.target='$contract' AND snst.itsast.itemid=magmall.items.id ORDER BY loc";
$orderlegal  = "SELECT contractno,date FROM snst.contract WHERE target='$contract'";

$resultst     = $mysqlist->query($orderst);
$resultitsast = $mysqlist->query($orderitsast);
$resultlegal  = $mysqlist->query($orderlegal);

$num_rows_st     = $resultst->num_rows;
$num_rows_itsast = $resultitsast->num_rows;
$num_rows_legal  = $resultlegal->num_rows;

    $changeuser = $_SESSION['user'];
    $changedate = date("Y-m-d H:i");
    $changetype = 'pdf protocol printed';
    $changecont = 'Protokol do kontraktu: ' .$contract;
    // Register a change in a changelog
    $sql = $mysqlist->query("INSERT INTO sdnadmin.userlog (user, date, type, content) VALUES ('$changeuser','$changedate','$changetype','$changecont')");

if ($num_rows_st > 35){
    $adp = 'this->AddPage';
} else {
    $adp = '';
}

//Initialize the 6 columns
    $column_vendor = "";
    $column_type = "";
    $column_sn = "";
    $column_st = "";
    $column_qty = "";
    $column_price = "";

if ($num_rows_legal > 0) {
    while($data = $resultlegal->fetch_array()) {
       $datacon1 = $data["contractno"];
       $datacon2 = $data["date"];
}
}

mysqli_close();

require('tfpdf/tfpdf.php');

$footer = file_get_contents('tfpdf/prosdn/footer.txt');

class nPDF extends tFPDF
{
// Page header
function Header()
{
    // Logo
    $this->Image('images/logo_firmy.png',85,10,35);
    // Line break
    $this->Ln(20);
}

function TopStarter($a)
{
    $this->SetY($a);
    $GLOBALS['b'] = $a;
}

function FirstAcapiteTitle($title)
{
    // Arial 11
    $this->SetFont('arial','BD',11);
    // Title
    $this->Cell(0,10,$title,0,0,'C');
    // Line break
    $this->Ln();
}

function SecondAcapiteTitle($title)
{
    // Arial 11
    $this->SetFont('arial','BD',11);
    // Title
    $this->Cell(0,10,$title,0,0,'C');
    // Line break
    $this->Ln();
}

function AcapiteTitle($title)
{
    // Arial 11
    $this->SetFont('arial','BD',11);
    // Set position X
    $this->SetX(18);
    // Title
    $this->Cell(0,10,$title,0,0,'C');
    // Line break
    $this->Ln();
}

function ParaHead($file1, $file2)
{
    // Declare globals
    global $datacon1;
    global $datacon2;
    // Read text file
    $txt1 = file_get_contents($file1);
    $txt2 = file_get_contents($file2);
    // Arial 11
    $this->SetFont('arial','BD',10);
    // Output justified text
    $this->MultiCell(0,5,$txt1,0,'C',0);
    $this->MultiCell(0,5,'nr ' .$datacon1. ' z dnia ' .$datacon2. ' w Warszawie',0,'C',0);
    $this->MultiCell(0,5,$txt2,0,'C',0);
    // Line break
    $this->Ln(4);
}

function FirstAcapiteBody($file)
{
    // Read text file
    $txt = file_get_contents($file);
    // Arial 11
    $this->SetFont('arial','',11);
    // Output justified text
    $this->MultiCell(0,5,$txt);
    // Line break
    $this->Ln(4);
}

function AcapiteBody($file)
{
    // Read text file
    $txt = file_get_contents($file);
    // Arial 11
    $this->SetFont('arial','',11);
    // Output justified text
    $this->MultiCell(0,5,$txt);
    // Line break
    $this->Ln(4);
}

function PrintFirstAcapite($title, $file)
{
    global $b;
    $y = ($b+30);
    $this->SetXY(20, $y);
    $this->FirstAcapiteTitle($title);
    $this->FirstAcapiteBody($file);
}

function PrintSecondAcapite3($title, $file)
{
    global $num_rows_st;
    global $num_rows_itsast;
    global $d;
    $c = $num_rows_st + $num_rows_itsast;
    $tabh = $c * 4;
    $y = $tabh + $d;
    $GLOBALS['e'] = $y;

    $this->SetXY(20, $y);
    $this->SecondAcapiteTitle($title);
    $this->AcapiteBody($file);
}

function PrintSecondAcapite4($title, $file)
{
    global $b;
    $y = ($b+65);
    $GLOBALS['e'] = $y;

    $this->SetXY(20, $y);
    $this->SecondAcapiteTitle($title);
    $this->AcapiteBody($file);
}

function PrintAcapite($x, $y, $title, $file)
{
    $this->SetXY($x, $y);
    $this->AcapiteTitle($title);
    $this->AcapiteBody($file);
}

function ProtoHead($title, $file1, $file2)
{
    $this->AcapiteTitle($title);
    $this->ParaHead($file1, $file2);
}

function InPrintDate3()
{
    // Declare globals
    global $inputField;
    global $b;
    $y = ($b+40.96);
    $this->SetXY(102, $y);
    // Arial 11
    $this->SetFont('arial','BD',11);
    // Title
    $this->Cell(14,3,$inputField,0,0,'C');
}

function InPrintDate4()
{
    // Declare globals
    global $inputField;
    global $b;
    $y = ($b+45.96);
    $this->SetXY(62, $y);
    // Arial 11
    $this->SetFont('arial','BD',11);
    // Title
    $this->Cell(14,3,$inputField,0,0,'C');
}

function SNTableSt()
{
    global $num_rows_st;
    global $num_rows_itsast;
    global $resultst;
    global $resultitsast;
    global $b;
    $y = ($b+60);
    $GLOBALS['d'] = $y;

    //Fields Name position
    $Y_Fields_Name_position = $y;
    //Table position, under Fields Name
//    $Y_Table_Position = 115;
    $Y_Table_Position = ($y+5);

    //First create each Field Name
    //Gray color filling each Field Name box
    $this->SetFillColor(232,232,232);
    //Bold Font for Field Name
    $this->SetFont('arial','BD',8);
    $this->SetY($Y_Fields_Name_position);
    $this->SetX(35);
    $this->Cell(20,5,'Producent',1,0,'C',1);
    $this->SetX(55);
    $this->Cell(50,5,'Typ',1,0,'C',1);
    $this->SetX(105);
    $this->Cell(30,5,'Numer SN',1,0,'C',1);
    $this->SetX(135);
    $this->Cell(25,5,'Numer ST',1,0,'C',1);
    $this->SetX(160);
    $this->Cell(15,5,'Ilość',1,0,'C',1);
    $this->SetX(175);
//    $this->Cell(20,5,'Wartosc PLN',1,0,'C',1);
//    $this->Ln();

//    if ($num_rows_st > 0) {
    $i = 0;

        while($row = $resultst->fetch_array()){
        $column_vendor = $row['vendor'];
        $column_type = $row['cust_name'];
        $column_sn = $row['sn'];
        $column_st = $row['afost'];
        $column_qty = "1";
        $column_price = $row['custprice'];

    //Now show the 6 columns
    $this->SetFont('arial','',7);
    $this->SetY($Y_Table_Position+$i);
    $this->SetX(35);
    $this->MultiCell(20,4,$column_vendor,1,'L');

    $this->SetY($Y_Table_Position+$i);
    $this->SetX(55);
    $this->MultiCell(50,4,$column_type,1,'L');

    $this->SetY($Y_Table_Position+$i);
    $this->SetX(105);
    $this->MultiCell(30,4,$column_sn,1,'L');

    $this->SetY($Y_Table_Position+$i);
    $this->SetX(135);
    $this->MultiCell(25,4,$column_st,1,'C');

    $this->SetY($Y_Table_Position+$i);
    $this->SetX(160);
    $this->MultiCell(15,4,$column_qty,1,'C');

//    $this->SetY($Y_Table_Position+$i);
//    $this->SetX(165);
//    $this->MultiCell(20,4,$column_price,1,'C');

    $i = $i +4;
    }

    $Y_Table_Positionitsa = ($Y_Table_Position+($num_rows_st * 4));
    $i = 0;
    while($row = $resultitsast->fetch_array()){
     $column_vendor = $row['vendor'];
     $column_type = $row['cust_name'];
     $column_sn = $row['sn'];
     $column_st = $row['itsast'];
     $column_qty = "1";
     $column_price = $row['custprice'];

    //Now show the 6 columns
    $this->SetFont('arial','',7);
    $this->SetY($Y_Table_Positionitsa+$i);
    $this->SetX(25);
    $this->MultiCell(20,4,$column_vendor,1,'L');

    $this->SetY($Y_Table_Positionitsa+$i);
    $this->SetX(45);
    $this->MultiCell(50,4,$column_type,1,'L');

    $this->SetY($Y_Table_Positionitsa+$i);
    $this->SetX(95);
    $this->MultiCell(30,4,$column_sn,1,'L');

    $this->SetY($Y_Table_Positionitsa+$i);
    $this->SetX(125);
    $this->MultiCell(25,4,$column_st,1,'C');

    $this->SetY($Y_Table_Positionitsa+$i);
    $this->SetX(150);
    $this->MultiCell(15,4,$column_qty,1,'C');

//    $this->SetY($Y_Table_Positionitsa+$i);
//    $this->SetX(165);
//    $this->MultiCell(20,4,$column_price,1,'C');

    $i = $i +4;
    }

}

function SignTitle($width,$title)
{
    // Arial 11
    $this->SetFont('arial','BD',8);
    // Title
    $this->Cell($width,8,$title,1,0,'C');
    // Line break
    //$this->Ln();
}
function SignSpace($width, $h)
{
    // Arial 11
    $this->SetFont('arial','BD',8);
    // Title
    $this->Cell($width,$h,'',1,0,'C');
    // Line break
    $this->Ln();
}
function DescrBody($width, $file)
{
    // Arial 11
    $this->SetFont('arial','BD',8);
    // Read text file
    $txt = file_get_contents($file);
    // Title
    $this->MultiCell($width,3,$txt,1,'C',0);
    // Line break
    $this->Ln();
}


function PrintSignField3($width, $title_l, $title_r, $h, $file)
{
    global $e;
    $x = 35;
    $y = $e + 25;
    $this->SetXY($x, $y);
    $this->SignTitle($width, $title_l);
    $this->SetXY($x, $y+8);
    $this->SignSpace($width, $h);
    $this->SetXY($x, $y+8+$h);
    $this->DescrBody($width, $file);

    $z = 105;
    $this->SetXY($z, $y);
    $this->SignTitle($width, $title_r);
    $this->SetXY($z, $y+8);
    $this->SignSpace($width, $h);
    $this->SetXY($z, $y+8+$h);
    $this->DescrBody($width, $file);

}

function PrintSignField4($width, $title_l, $title_r, $h, $file)
{
    global $e;
    $x = 35;
    $y = $e + 56;
    $this->SetXY($x, $y);
    $this->SignTitle($width, $title_l);
    $this->SetXY($x, $y+8);
    $this->SignSpace($width, $h);
    $this->SetXY($x, $y+8+$h);
    $this->DescrBody($width, $file);

    $z = 105;
    $this->SetXY($z, $y);
    $this->SignTitle($width, $title_r);
    $this->SetXY($z, $y+8);
    $this->SignSpace($width, $h);
    $this->SetXY($z, $y+8+$h);
    $this->DescrBody($width, $file);

}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(271);
    // Arial italic 8
    $this->Line(20,270,187,270);
    $this->Line(20,270.5,187,270.5);
    $this->SetFont('arial','BD',8);
    // Footer content
    $this->Cell(0,5,'SDN Networks Bla Bla Sp. z o.o.',0,1,'C');
    $footer = file_get_contents('tfpdf/prosdn/footer.txt');
    $this->SetFont('arial','',8);
    $this->MultiCell(0,3,$footer,0,'C',0);
    // Page number
    $this->SetXY(189,269.5);
    $this->SetFont('arial','',9);
    $this->Cell(0,3,$this->PageNo(),0,0,'C');
}
}
//create the fpdf object and do some initialization
$pdf = new nPDF("P","mm","A4");
$pdf->AddPage();
$pdf->SetDisplayMode(fullpage,single);
//set default font
$pdf->AddFont('arial','','arial.ttf',true);
$pdf->AddFont('arial','I','ariali.ttf',true);
$pdf->AddFont('arial','BD','arialbd.ttf',true);
$pdf->AddFont('arial','BI','arialbi.ttf',true);

//$pdf->SetY(35);
$pdf->TopStarter(35);
$pdf->SetMargins(20,20,20);

$pdf->ProtoHead('Załącznik Nr 3','tfpdf/prosdn/protokol1-1.txt','tfpdf/prosdn/protokol1-2.txt');
$pdf->PrintFirstAcapite('PROTOKÓŁ ODBIORU URZĄDZEŃ','tfpdf/prosdn/protokol2.txt');
$pdf->InPrintDate3();
$pdf->SNTableSt();
$pdf->PrintSecondAcapite3('','tfpdf/prosdn/protokol3.txt');
$pdf->PrintSignField3('70','Dostawca','Odbiorca','25','tfpdf/prosdn/protokol4.txt');
$pdf->AddPage();
$pdf->ProtoHead('Załącznik Nr 4','tfpdf/prosdn/protokol1-1.txt','');
$pdf->PrintFirstAcapite('PROTOKÓŁ ZDAWCZO-ODBIORCZY USŁUG','tfpdf/prosdn/protokol1-3.txt');
$pdf->InPrintDate4();
$pdf->PrintSecondAcapite4('','tfpdf/prosdn/protokol3-4.txt');
$pdf->PrintSignField4('70','Dostawca','Odbiorca','25','tfpdf/prosdn/protokol4.txt');

$pdf->Output();
//$pdf->Output('protokol_'.$contract.'.pdf','D');
}
?>
