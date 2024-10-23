<?php
session_start();
require_once 'vendor/autoload.php'; // Autoload the Google API client library
// Initialize the Google client
$client = new Google_Client();
$client->setAuthConfig('credentials.json'); // Path to credentials.json file
$client->setRedirectUri('http://localhost/upload-file-in-drive'); // Redirect URI
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');
?>
<html>

<head>
    <base target="_blank">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Google File Upload</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
    <style>
    .disclaimer {
        width: 480px;
        color: #646464;
        margin: 20px auto;
        padding: 0 16px;
        text-align: center;
        font: 400 12px Roboto, Helvetica, Arial, sans-serif
    }

    .disclaimer a {
        color: #009688
    }

    #credit {
        display: none
    }

    .loader-div {
        width: 100%;
        display: block;
        vertical-align: middle;
        text-align: center;
        position: relative;
        height: 100vh;
        padding-top: 100px;
    }

    .loader {
        width: 146px;
        text-align: center;
        display: inline-block;
    }

    .loader-div p {
        display: block;
        text-align: center;
    }
    </style>
</head>

<body>

    <!-- Written by Amit Agarwal amit@labnol.org -->
    <?php if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {?>
    <form class="main" action="process_upload.php" method="POST" enctype="multipart/form-data" style="max-width: 480px;margin: 40px auto;">
        <div id="">
            <div class="row">
                <div class="col s12">
                    <h5 class="center-align teal-text">Upload Files to my Google Drive</h5>
                </div>
            </div>

            <div class="row">
                <div class="file-field input-field col s12">
                    <div class="btn">
                        <span>File</span>
                        <input id="files" type="file" name="fileToUpload" id="fileToUpload" required>
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" placeholder="Select a file on your computer">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s6">
                    <button class="btn submit-btn" type="submit">Submit</button>
                </div>
            </div>
    </form>
    <?php }else{ ?>
    <?php if (!isset($_GET['code'])) {
            $authUrl = $client->createAuthUrl();
            echo "<html>
              <head>
                <meta http-equiv='refresh' content='1;url=$authUrl'>
                <title>Redirecting...</title>
              </head>
              <body>
                 <div class='loader-div'>
                    <img src='loading.gif' alt='Loading' class='loader'>
                    <p>You will be redirected to Google Drive. Please wait...</p>
                </div>
              </body>
              </html>";
            exit();
        } else {
            // Exchange authorization code for access token
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            echo "<html>
              <head>
                <meta http-equiv='refresh' content='1;url=http://localhost/upload-file-in-drive'>
                <title>Redirecting...</title>
              </head>
              <body>
                 <div class='loader-div'>
                    <img src='loading.gif' alt='Loading' class='loader'>
                    <p>You will be redirected to File Upload Page. Please wait...</p>
                </div>
              </body>
              </html>";
            exit();
        }
     } ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>

</body>

</html>