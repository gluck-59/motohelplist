<?php
//require_once '../../vendor/autoload.php';

require_once '../../include/OAuthRequester.php';

define('OAUTH_HOST', 'http://' . $_SERVER['SERVER_NAME']);
$id = 1;

// Init the OAuthStore
$options = array(
    'consumer_key' => '9bf481d98eff5bbfb1c1e5946ad5ff90056bbbace',
    'consumer_secret' => '86c0f727616063bdb4ed33be5188e801',
    'server_uri' => OAUTH_HOST,
    'request_token_uri' => OAUTH_HOST . '/api/1/public/request_token.php',
    'authorize_uri' => OAUTH_HOST . '/api/1/public/login.php',
    'access_token_uri' => OAUTH_HOST . '/api/1/public/access_token.php'
);
OAuthStore::instance('Session', $options);

if (empty($_GET['oauth_token'])) {
    // get a request token
    $tokenResultParams = OAuthRequester::requestRequestToken($options['consumer_key'], $id);

    header('Location: ' . $options['authorize_uri'] .
        '?oauth_token=' . $tokenResultParams['token'] . 
        '&oauth_callback=' . urlencode('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']));
}
else {
    // get an access token
    $oauthToken = $_GET['oauth_token'];
    $tokenResultParams = $_GET;
    OAuthRequester::requestAccessToken($options['consumer_key'], $tokenResultParams['oauth_token'], $id, 'POST', $_GET);
    $request = new OAuthRequester(OAUTH_HOST . '/api/1/public/test_request.php', 'GET', $tokenResultParams);
    $result = $request->doRequest(0);
    if ($result['code'] == 200) {
        var_dump($result['body']);
    }
    else {
        echo 'Error';
    }
}
