<? 

// Perform a check for development mode
$dev_mode = false;
if (php_sapi_name() == 'cli-server') {
    $dev_mode = true;
} else {
    error_reporting(E_ERROR | E_PARSE);
}

// Include the Composer autoloader
require 'vendor/autoload.php';
require 'private/config.php';

/* 
 * ====================================
 * BEGIN ESTABLISHING GOOGLE CONNECTION
 * ====================================
 */

$client = new Google_Client();
$client->setApplicationName($APP_NAME);

$key = file_get_contents($KEY_FILE_LOCATION);
$cred = new Google_Auth_AssertionCredentials(
    $SERVICE_ACCOUNT_NAME,
    array("https://spreadsheets.google.com/feeds"),
    $key
);
$client->setAssertionCredentials($cred);

if ($client->getAuth()->isAccessTokenExpired()) {
    $client->getAuth()->refreshTokenWithAssertion($cred);
}

$accessToken = json_decode($client->getAccessToken(), true)["access_token"];

/* 
 * ====================================
 * END ESTABLISHING GOOGLE CONNECTION
 * ====================================
 */

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$serviceRequest = new DefaultServiceRequest($accessToken);
ServiceRequestFactory::setInstance($serviceRequest);

$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
$spreadsheets = $spreadsheetService->getSpreadsheets();
$spreadsheet = $spreadsheets->getByTitle($SPREADSHEET_TITLE);
$worksheets = $spreadsheet->getWorksheets();

$results = [];

// Loop through each of the worksheets and pull the user's information
foreach ($WORKSHEET_TITLES as $key => $worksheet_title) {
    $worksheet = $worksheets->getByTitle($worksheet_title);
    $rows = $worksheet->getListFeed();
    $result = array(
        "title" => $worksheet_title,
        "data" => null,
        "total" => $WORKSHEET_TOTALS[$key]
    );
    foreach ($rows->getEntries() as $row) {
        $values = $row->getValues();
        if ($values["email"] == $STUDENT_EMAIL) {
            $result["data"] = $values;
            break;
        }
    }
    array_push($results, $result);
}

?>

<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="./bower_components/bootstrap/dist/css/bootstrap.min.css">
    <title>Grades &middot; CS142 W15</title>
    <style>
        h1, h2, h3, h4, h5, h6 {
            text-transform: uppercase;
        }
        .container {
            margin-top: 20px;
        }
        table {
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
            <h4>Grades for <?= $STUDENT_NAME ?> <small><?= $COURSE_NAME ?> <?= $COURSE_SEASON ?> <?= $COURSE_YEAR ?></small></h4>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Score</th>
                            <th>Late days used</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach ($results as $result) { ?>
                        <tr>
                            <td><?= $result["title"]; ?></td>
                            <td><?= $result["data"]["total"]; ?> / <?= $result["total"]; ?></td>
                            <td><?= $result["data"]["latedays3"]; ?></td>
                        </tr>
                        <? } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
