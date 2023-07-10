<?php
$id = $_GET['f'];
include "../connect.inc.php";
$mysql = new MySQL_Connection('localhost', 'root', 'P@ssw0rd', 'helpdesk');
$sql = "SELECT A.*,B.DEPARTMENT_FULL_NAME FROM `tb_fix_asset` A LEFT OUTER JOIN tb_department B ON A.DEPARTMENT_ID = B.ID  WHERE A.ID = '{$id}' ";
$result=$mysql->queryAndFetchAll($sql);

require('fpdf.php');
require('src/autoload.php');

use setasign\Fpdi\Fpdi;

// Create a new instance of the FPDI class
$pdf = new Fpdi();

// Add a page to the PDF
$pdf->AddPage();

// Import the existing PDF file
$pageCount = $pdf->setSourceFile('fix.pdf');
$templateId = $pdf->importPage(1);

function generateRandomString($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
}

// Use the imported page as a template
$pdf->useTemplate($templateId);

// Define the variables containing the Thai text
$name = $result[0]['ASSET_NAME'];
$serial = $result[0]['ASSET_SERIAL'];
$asset_id = $result[0]['ASSET_ID'];
$problam = $result[0]['ASSET_CAUSE'];
$dept = $result[0]['DEPARTMENT_FULL_NAME'];
$user_id = $result[0]['USER_ID'];
$ddd = $result[0]['ASSET_START_DATE'];

// Set the font for the Thai text
$pdf->AddFont('THSarabunNew_b','','THSarabunNew_b.php');
$pdf->SetFont('THSarabunNew_b', '', 12);

$pdf->SetXY(42 , 44); // Adjust the coordinates as per your requirement
$pdf->MultiCell(39, 5, iconv('UTF-8', 'cp874', $dept), 0, 'L');

$pdf->SetXY(10, 65); // Adjust the coordinates as per your requirement
$pdf->MultiCell(39, 5, iconv('UTF-8', 'cp874', $asset_id), 0, 'L');
// Add the Thai text to the PDF using the variables
$pdf->SetXY($pdf->GetX() + 42, 65); // Adjust the coordinates as per your requirement
$pdf->MultiCell(29, 5, iconv('UTF-8', 'cp874', $serial), 0, 'L');

$pdf->SetXY($pdf->GetX() + 70, 65); // Adjust the coordinates as per your requirement
$pdf->MultiCell(60, 5, iconv('UTF-8', 'cp874', $name), 0, 'L');

$pdf->SetXY($pdf->GetX() + 132, 65); // Adjust the coordinates as per your requirement
$pdf->MultiCell(50, 5, iconv('UTF-8', 'cp874', $problam), 0, 'L');

$pdf->SetXY(35 , 98); // Adjust the coordinates as per your requirement
$pdf->MultiCell(39, 5, iconv('UTF-8', 'cp874', $user_id), 0, 'L');

$pdf->SetXY(35 , 104); // Adjust the coordinates as per your requirement
$pdf->MultiCell(39, 5, iconv('UTF-8', 'cp874', $user_id), 0, 'L');

$pdf->AddPage();

// Import the second page of "fix.pdf"
$templateId2 = $pdf->importPage(2);

// Use the imported page as a template on the new page
$pdf->useTemplate($templateId2);
/*
$pdf->AddFont('THSarabunNew_b','','THSarabunNew_b.php');
$pdf->SetFont('THSarabunNew_b', '', 12);

$pdf->SetXY(17 , 137); // Adjust the coordinates as per your requirement
$pdf->MultiCell(39, 5, iconv('UTF-8', 'cp874', $asset_id), 0, 'L');

$pdf->SetXY(66, 137); // Adjust the coordinates as per your requirement
$pdf->MultiCell(29, 5, iconv('UTF-8', 'cp874', $serial), 0, 'L');

$pdf->SetXY($pdf->GetX() + 93, 137); // Adjust the coordinates as per your requirement
$pdf->MultiCell(60, 5, iconv('UTF-8', 'cp874', $name), 0, 'L');
// Move to a new line and set the X coordinate to 80 (adjust as needed)
//$pdf->Cell(0, 10, iconv('UTF-8', 'cp874', $name), 0, 1);
*/

$currentDate = date("Ymd", strtotime($ddd));
// Output the modified PDF file
$pdf->Output('output/fix_'.$currentDate.'-'.$asset_id.'.pdf', 'F');
$df="UPDATE `tb_fix_asset` SET `ASSET_STATUS`='8' WHERE (`ID`='{$id}')";
$upd=$mysql->query($df);
header('Location: show.php?d=output/fix_'.$currentDate.'-'.$asset_id.'.pdf');
?>
