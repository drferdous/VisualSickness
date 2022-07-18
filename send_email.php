<?php

/*if (!isset($_POST['code'])) exit();*/
include "inc/header.php";
$sheetAPI = new Google_Service_Sheets($client);
$spreadsheetID = "19X2usqE12rsZGdbNWJ84Xwh7oCrsRhH2xTsOPLn7Dx0";
$range = "Form Responses 1!S3:X";
$response = $sheetAPI->spreadsheets_values->get($spreadsheetID, $range);
$values = $response->getValues();
if (empty($values)){
    echo "No data!";
}
else{
    foreach($values as $key=>$value) {
        if (in_array('DX4i5100', $value)) {
            $location = $key + 3;
        } 
        /*if (in_array($_POST['code'], $value)) {
            $location = $key + 3;
        }*/
    }
    $range = "Form Responses 1!E" . $location;
    $response = $sheetAPI->spreadsheets_values->get($spreadsheetID, $range);
    $value = $response->getValues();
    $email = $value[0][0];
    echo $email;
}