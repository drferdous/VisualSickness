<?php

if (!isset($_POST['code'])) exit();
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
    $codes = array_filter(explode(',', implode(',', array_map(function ($val) { return implode(',', $val); }, $values))), function ($val) { return strlen($val); });
    if (in_array($_POST['code'], $codes)) echo "exist";
    else echo "not exist";
}