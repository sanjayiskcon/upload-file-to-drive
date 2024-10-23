<?php
session_start();
require_once 'vendor/autoload.php'; // Autoload the Google API client library

// Initialize the Google client
$client = new Google_Client();
$client->setAuthConfig('credentials.json'); // Path to credentials.json file
$client->setRedirectUri('http://localhost/upload-file-in-drive'); // Redirect URI
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');

// Check if we already have an access token
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    // If the token is expired, refresh it
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $_SESSION['access_token'] = $client->getAccessToken();
    }

    // Initialize Google Drive service
    $driveService = new Google_Service_Drive($client);
    // Check if a file was submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $_FILES['fileToUpload']['name'] // Set the name of the file
        ));

        $content = file_get_contents($_FILES['fileToUpload']['tmp_name']);

        // Upload the file to Google Drive
        $file = $driveService->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => $_FILES['fileToUpload']['type'], // Set the correct MIME type
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));

        echo "File uploaded successfully. File ID: " . $file->id;
    }
} else {
    echo "<html>
            <head>
            <meta http-equiv='refresh' content='1;url=http://localhost/upload-file-in-drive'>
            <title>Redirecting...</title>
            </head>
            <body>
                <div class='loader-div'>
                <img src='loading.gif' alt='Loading' class='loader'>
                <p>Something Went Wrong. Please wait...</p>
            </div>
            </body>
            </html>";
        exit();
}
?>