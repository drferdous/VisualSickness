<?php
if (!isset($_POST['code'])) {
	exit();
}
require_once "google-api-php-client/vendor/autoload.php";

$scopes = [\Google_Service_Sheets::SPREADSHEETS, \Google_Service_Drive::DRIVE, \Google_Service_Docs::DOCUMENTS];
$client = new \Google_Client();
$client->setApplicationName("Get Sheets Data");
$client->setScopes($scopes);
$client->setAccessType("offline");
$client->setAuthConfig("visual-sickness-study-fc1498762739.json");

$sheetAPI = new Google_Service_Sheets($client);
$spreadsheetID = "19X2usqE12rsZGdbNWJ84Xwh7oCrsRhH2xTsOPLn7Dx0";
$range = "Form Responses 1!T2:Y";
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