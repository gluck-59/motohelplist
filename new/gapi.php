<?php
error_reporting(E_ALL);
ini_set('display_errors','On');


require_once '../classes/google-api-php-client/src/Google/autoload.php'; // or wherever autoload.php is located

define('APPLICATION_NAME', 'motohelplist_test');
define('CREDENTIALS_PATH', '/home/motokofr/public_motohelp/app/drive-php-quickstart.json');
define('CLIENT_SECRET_PATH', 'motohelplist_test.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/drive-php-quickstart.json
define('SCOPES', 
implode(' ', array(
  Google_Service_Drive::DRIVE)
));



/*
if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}
*/
/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfigFile(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = file_get_contents($credentialsPath);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    //print_r($authUrl);
    //print_r("Open the following link in your browser:\n%s\n", $authUrl);
    print_r('Enter verification code1: ',$authUrl);
    $authCode = '4/Hq5w34DcZ9v5kyUVR2ZPG_mjLu7Fi9oYM4Htkmu7kKQ';//trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = $client->authenticate($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, $accessToken);
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->refreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, $client->getAccessToken());
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
  }
  return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Drive($client);

// Print the names and IDs for up to 10 files.
/*
$optParams = array(
  'pageSize' => 100,
  'fields' => "nextPageToken, files(id, name)"
);
$results = $service->files->listFiles($optParams);
*/
$results = $service->files->listFiles();



echo '<pre>';
print_r($results);



if (count($results->getFiles()) == 0) {
  echo "No files found.\n";
} else {

  foreach ($results->getFiles() as $file) {
    //printf("%s (%s)\n", $file->getName(), $file->getId());
    //echo $file->getName().' '.$file->getId().' '.$file->getKind().'<br>';
  }
}

 $pic = 'upload/IMG_20160503_195526.jpg';

 $file = new Google_Service_Drive_DriveFile(['name' => '1.jpg',
    'mimeType' => 'image/jpeg']);
// $parent = new Google_Service_Drive_ParentReference();
 
 //$parent->setId('0B1szKmGmcwxcZGt2X3JONUx0dVU');
// $file->setParents('0B1szKmGmcwxcZGt2X3JONUx0dVU');
 //$file->setName("I am service Account");
//print_r($file);



/*
$result = $service->files->create($file, array(
      'data' => file_get_contents($pic),
      'mimeType' => 'application/octet-stream',
      'uploadType' => 'media'
    ));
print_r($result);
exit;  
*/

//gdupload($pic);

//echo '<img src="'.$pic.'">';


?>