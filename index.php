<?php
session_start();

require_once "google-api-client-src/Google_Client.php";
require_once "google-api-client-src/contrib/Google_CalendarService.php";

$apiClient = new Google_Client();
$apiClient->setUseObjects(true);
$service = new Google_CalendarService($apiClient);

if (isset($_SESSION['oauth_access_token'])) {
  $apiClient->setAccessToken($_SESSION['oauth_access_token']);
} else {
  $token = $apiClient->authenticate();
  $_SESSION['oauth_access_token'] = $token;
}