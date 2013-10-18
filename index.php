<?php
session_start();

require_once "google-api-client-src/Google_Client.php";
require_once "google-api-client-src/contrib/Google_CalendarService.php";

global $apiConfig;

$apiClient = new Google_Client();
$apiClient->setUseObjects(true);
$calService = new Google_CalendarService($apiClient);

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}

if (isset($_GET['code'])) {
  $apiClient->authenticate($_GET['code']);
  $_SESSION['access_token'] = $apiClient->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['access_token'])) {
  $apiClient->setAccessToken($_SESSION['access_token']);
}

// We have an access token, now do the things
if ($apiClient->getAccessToken()) {
  $calendarIds = array();
  $calList = $calService->calendarList->listCalendarList();
  if ($calList) {
    foreach($calList->items as $calListItem) {
      $calId = $calListItem->id;

      // We only care about personal calendars (ends with @dosomething.org)
      if (substr($calId, -strlen("@dosomething.org")) === "@dosomething.org") {
        $calendarIds[] = $calId;
      }
    }
  }

  $pageData = array();
  $pageData['calendarIds'] = $calendarIds;

  ob_start();
  require "templates/home.php";
  ob_end_flush();

  $_SESSION['token'] = $apiClient->getAccessToken();
}
// If we don't have an access token, get user authorization
else {
  $authUrl = $apiClient->createAuthUrl();

  $pageData = array();
  $pageData['auth_url'] = $authUrl;

  ob_start();
  require "templates/auth_request.php";
  ob_end_flush();
}
