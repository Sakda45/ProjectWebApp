<?php
// เปิดการแสดงผลข้อผิดพลาด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// เชื่อมต่อฐานข้อมูล
$servername = "151.106.124.154";
$username = "u583789277_wag19";
$password = "2567Inspire";
$dbname = "u583789277_wag19";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// เตรียม SQL query เพื่อดึงข้อมูลทั้งหมดจากตาราง zones
$sql = "SELECT * FROM booth";
$result = $conn->query($sql);

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($result->num_rows > 0) {
    $zones = $result->fetch_all(MYSQLI_ASSOC); // ดึงข้อมูลทั้งหมดในรูปแบบ associative array
    $total_zones = $result->num_rows; // นับจำนวนโซนทั้งหมด
    echo json_encode(["status" => "success", "total_zones" => $total_zones, "zones" => $zones]);
} else {
    echo json_encode(["status" => "error", "message" => "No zones found"]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
