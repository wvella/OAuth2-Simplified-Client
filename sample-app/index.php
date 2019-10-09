<?php
function apiRequest($url, $post=FALSE, $headers=array()) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

  if($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

  $headers = [
    'Accept: application/vnd.github.v3+json, application/json',
    'User-Agent: https://example-app.com/'
  ];

  if(isset($_SESSION['access_token']))
    $headers[] = 'Authorization: Bearer '.$_SESSION['access_token'];

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_exec($ch);
  return json_decode($response, true);
}

//The $githubClientSecret variable is stored in the client-secret.php file. This is so this file is not uploaded
//to the public GIT repo. If you are building this simple web application locally, you need to create a a file
//with the following contents:

/*<?php
//$githubClientSecret = '<<yoursecret>>';
?>*/
include 'client-secret.php';

// Fill these out with the values from Github
$githubClientID = '7c4d14bbba5ef0b6d4b1';

// This is the URL we'll send the user to first
// to get their authorization
$authorizeURL = 'https://github.com/login/oauth/authorize';

// This is the endpoint we'll request an access token from
$tokenURL = 'https://github.com/login/oauth/access_token';

// This is the Github base URL for API requests
$apiURLBase = 'https://api.github.com/';

// The URL for this script, used as the redirect URL
$baseURL = 'http://' . $_SERVER['SERVER_NAME'] . ':8000'
   . $_SERVER['PHP_SELF'];

    // Start a session so we have a place to
// store things between redirects
session_start();

// If there is an access token in the session
// the user is already logged in
if(!isset($_GET['action'])) {
  if(!empty($_SESSION['access_token'])) {
    echo '<h3>Logged In</h3>';
    echo '<p><a href="?action=repos">View Repos</a></p>';
    echo '<p><a href="?action=logout">Log Out</a></p>';
  } else {
    echo '<h3>Not logged in</h3>';
    echo '<p><a href="?action=login">Log In</a></p>';
  }
  die();
}

// Start the login process by sending the user
// to Github's authorization page
if(isset($_GET['action']) && $_GET['action'] == 'login') {
  unset($_SESSION['access_token']);

  // Generate a random hash and store in the session
  $_SESSION['state'] = bin2hex(random_bytes(16));

  $params = array(
    'response_type' => 'code',
    'client_id' => $githubClientID,
    'redirect_uri' => $baseURL,
    'scope' => 'user public_repo',
    'state' => $_SESSION['state']
  );

  //print_r($params);

  // Redirect the user to Github's authorization page
  header('Location: '.$authorizeURL.'?'.http_build_query($params));
  die();
}
  ?>