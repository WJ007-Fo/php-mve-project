<?php
$targetId = isset($_GET['target_id']) ? (int)$_GET['target_id'] : 0;

if ($targetId <= 0) {
    header('Location: /newfuture/search-users');
    exit;
}

$events = getEventsByCreatorId($targetId);

$creatorName = !empty($events) ? $events[0]['creator_name'] : 'ผู้ใช้งาน';

$creatorEmail = !empty($events) ? ($events[0]['creator_email'] ?? $events[0]['email'] ?? '') : '';

renderView('target-user-events', [
    'title' => 'กิจกรรมของ ' . $creatorName,
    'events' => $events,
    'creatorName' => $creatorName,
    'creatorEmail' => $creatorEmail
]);
