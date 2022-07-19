<?php

require_once "google-api-php-client/vendor/autoload.php";

$scopes = [\Google_Service_Sheets::SPREADSHEETS, \Google_Service_Drive::DRIVE, \Google_Service_Docs::DOCUMENTS];
$client = new \Google_Client();
$client->setApplicationName("Get Sheets Data");
$client->setScopes($scopes);
$client->setAccessType("offline");
$client->setAuthConfig("visual-sickness-study-fc1498762739.json");

$documents = array('assent' => "1THRx32I7v9U1LHVzzpjWSYDSkE5p5MxpCA8NrEB4lO0", 'adultConsent' => "1fMxZWv5-yY3ipnRm27CD81YvVnbxqKUb5lmsoOorFzg", 'parent_guardianPermission' =>  "1IO8SCPtlwr2PcvC5l8ar9Iayc5nPgWMbBTNRa1Cmi7o");
if (!isset($_POST['email']) || !isset($_POST['documentName']) || !isset($documents[$_POST['documentName']])) {
echo 'bad';
print_r($_POST);
    exit();
}

$documentID = $documents[$_POST['documentName']];
$email = $_POST['email'];
$parents = array("1wac5wACNpC42QUSm-8ndgE9zhYQI5gTb");
$driveAPI = new Google_Service_Drive($client);
$docsAPI = new Google_Service_Docs($client);

$copyTitle = "Copied Title";
$copy = new Google_Service_Drive_DriveFile(array(
    "name" => $copyTitle,
    "parents" => $parents
));
$response = $driveAPI->files->copy($documentID, $copy);
$copyDocID = $response->id;
$doc = $docsAPI->documents->get($copyDocID);

$requests = array();
// email, documentName
foreach($_POST as $key => $value){
    if ($key === "email" || $key === "documentName"){
        continue;
    }
    $replaceAllTextRequest = [
        'replaceAllText' => [
            'replaceText' => $value,
            'containsText' => [
                'text' => '{{' . $key . '}}',
                'matchCase' => true,
            ],
        ],
    ];
    $requests[] = new Google_Service_Docs_Request($replaceAllTextRequest);
}
$batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest(['requests' => $requests]);
$response = $docsAPI->documents->batchUpdate($copyDocID, $batchUpdateRequest);

$result = $driveAPI->files->export($copyDocID, "application/pdf");
$title = $_POST['documentName'] . ': ' . $email;
$mimeType = "application/pdf";

$file = $driveAPI->files->create(
    new Google_Service_Drive_DriveFile(array(
        "name" => $title,
        "parents" => $parents,
    )),
    array(
        'data' => (string) $result->getBody(),
        'mimeType' => $mimeType,
        'fields' => 'id'
    )
);

$documentName = $_POST['documentName'];

$to          = $email;
$from        = "Visual Sickness Study <visualsicknessstudy@gmail.com>";
$docName     = preg_replace(array('/([A-Z])/', '/_/'), array(' $1', '/'), $documentName);
$subject     = 'Visual Sickness Study ' . ucwords($docName) . ' Form'; // email subject
$body        = 'Hello,<br><br>
                Thank you for agreeing to participate in the Visual Sickness study. Please find attached your ' . strtolower($docName) . ' form.<br><br>
                Thank you.';
$pdfName     = "$documentName.pdf";
$filetype    = "application/pdf";

$eol = PHP_EOL;
$semi_rand     = md5(time());
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
$headers       = "From: $from$eol" .
  "MIME-Version: 1.0$eol" .
  "Content-Type: multipart/mixed;$eol" .
  " boundary=\"$mime_boundary\"";

$message = "--$mime_boundary$eol" .
"Content-Type: text/html; charset=\"iso-8859-1\"$eol" .
"Content-Transfer-Encoding: 7bit$eol$eol" .
$body . $eol;

$data = (string) $result->getBody();
$pdf = chunk_split(base64_encode($data));

// attach pdf to email
$message .= "--$mime_boundary$eol" .
  "Content-Type: $filetype;$eol" .
  " name=\"$pdfName\"$eol" .
  "Content-Disposition: attachment;$eol" .
  " filename=\"$pdfName\"$eol" .
  "Content-Transfer-Encoding: base64$eol$eol" .
  $pdf . $eol .
  "--$mime_boundary--";

// Send the email
if(mail($to, $subject, $message, $headers)) {
  echo "The email was sent.";
}
else {
  echo "There was an error sending the mail.";
}

$driveAPI->files->delete($copyDocID);