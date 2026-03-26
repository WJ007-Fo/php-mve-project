<?php
// ไฟล์นี้จะได้รับ $context มาจาก dispatcher โดยอัตโนมัติ
$conn = db_connect();
$dbName = $_ENV['DB_DATABASE'];

$tables = [];
$tableList = $conn->query("SHOW TABLES");

while ($row = $tableList->fetch_array()) {
    $tableName = $row[0];
    // ดึงโครงสร้างของแต่ละตาราง
    $columns = $conn->query("DESCRIBE `$tableName`")->fetch_all(MYSQLI_ASSOC);
    
    $tables[] = [
        'name' => $tableName,
        'columns' => $columns
    ];
}

// ส่ง data ไปที่ GUI (View) 
// สมมติว่าระบบคุณใช้ renderView('ชื่อไฟล์', 'ข้อมูลที่จะส่งไป')
renderView('schema', [
    'dbName' => $dbName,
    'tables' => $tables
]);