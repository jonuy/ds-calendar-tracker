<?php
session_start();

require_once "google-api-client-src/Google_Client.php";
require_once "google-api-client-src/contrib/Google_CalendarService.php";

global $apiConfig;

$apiClient = new Google_Client();
$apiClient->setUseObjects(true);

if (isset($_SESSION['access_token'])) {
  $apiClient->setAccessToken($_SESSION['access_token']);
}

// Redirect back to main index if no access token is available
if (!$apiClient->getAccessToken()) {
  header("Location: index.php");
}

$calService = new Google_CalendarService($apiClient);
$calEvents = $calService->events;
$calendarId = $_REQUEST['id'];
print "<h1>$calendarId</h1>";

$currentDate = new DateTime();
$optParams = array(
  "timeMin" => $currentDate->format('Y-m-d') . 'T09:30:00Z',
  "timeMax" => $currentDate->format('Y-m-d') . 'T18:30:00Z',
  "timeZone" => "America/New_York",
);

$timeMinDate = new DateTime($optParams['timeMin']);
$timeMaxDate = new DateTime($optParams['timeMax']);

$calEventsList = $calEvents->listEvents($calendarId, $optParams)->items;
$validEvents = array();

// Cull any invalid calendar events off the list
foreach ($calEventsList as $event) {
  // Check that start time is within the range we searched for
  $eventStartDate = new DateTime($event->start->dateTime);
  $eventEndDate = new DateTime($event->end->dateTime);

  if ($eventStartDate >= $timeMinDate && $eventStartDate <= $timeMaxDate) {
    print "<p><strong>" . $event->summary . "</strong> from: " . $eventStartDate->format('Y-m-d H:i:s') . " to: " . $eventEndDate->format('Y-m-d H:i:s') . "</p>";
    $validEvents[] = $event;
  }
}

$totalHours = 0;
foreach ($validEvents as $event) {
  $startTime = new DateTime($event->start->dateTime);
  $endTime = new DateTime($event->end->dateTime);

  // Get total of time in meetings
  $diff = $endTime->diff($startTime);
  $totalHours += $diff->h;
}

print "<h3>RESULTS</h3><ul>";
print "<li>Total # of meetings: " . count($validEvents) . "</li>";
print "<li>Total time in meetings: $totalHours hours</li>";
print "<li>Average length of meetings: " . $totalHours / count($validEvents) . " hours</li>";
print "</ul>";
