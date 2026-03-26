<?php
declare(strict_types=1);

// 1. รับค่าจาก Query String
$keyword   = trim($_GET['keyword'] ?? '');
$startDate = $_GET['start_date'] ?? '';
$endDate   = $_GET['end_date'] ?? '';

// 2. ดึงข้อมูล Event (ใช้ฟังก์ชัน searchEvents ที่รองรับทั้ง keyword และวันที่)
// ถ้า keyword และวันที่ว่าง ฟังก์ชันนี้จะคืนค่า events ทั้งหมดตาม ORDER BY ที่ตั้งไว้
$events = searchEvents($keyword, $startDate, $endDate);

// 3. ดึงข้อมูลสถานะการเข้าร่วมของผู้ใช้ (ถ้า Login อยู่)
$userId = $_SESSION['user_id'] ?? null;
$joinEvent = getJoinedEventsByUserId($userId);
$joinedEventIds = !empty($joinEvent) ? array_column($joinEvent, 'event_id') : [];

// 4. ดึงจำนวนผู้เข้าร่วมทั้งหมดที่อนุมัติแล้ว
$current_participants = getAllAmountOfApprovedParticipants();

// 5. กำหนด Title ให้สอดคล้องกับการค้นหา
$title = "กิจกรรมทั้งหมด";
if (!empty($keyword)) {
    $title = "ผลการค้นหา: " . htmlspecialchars($keyword);
} elseif (!empty($startDate) || !empty($endDate)) {
    $title = "กิจกรรมในช่วงวันที่เลือก";
}

// 6. ส่งข้อมูลไปที่ View
renderView('events', [
    'events'               => $events, 
    'current_participants' => $current_participants, // ส่งไปเป็นก้อน เพื่อไปเทียบ ID ใน View
    'keyword'              => $keyword,
    'start_date'           => $startDate,
    'end_date'             => $endDate,
    'joined_events'        => $joinedEventIds,
    'title'                => $title
]);