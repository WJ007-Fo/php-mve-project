<?php

declare(strict_types=1);

$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$keyword       = trim($_GET['keyword'] ?? '');

$searchedUsers = searchUsersWithEventCount($keyword, $currentUserId);

$title = "ค้นหาผู้ใช้งาน";
if (!empty($keyword)) {
    $title = "ผลการค้นหาผู้ใช้: " . htmlspecialchars($keyword);
}

renderView('search-users-view', [
    'searched_users' => $searchedUsers,
    'keyword'        => $keyword,
    'title'          => $title
]);
