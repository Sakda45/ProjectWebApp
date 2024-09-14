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

// รับค่า zone_id ที่ต้องการจาก request (เช่นผ่าน URL หรือ JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$zone_id = $data['zone_id'] ?? null; // ตรวจสอบว่ามี zone_id ถูกส่งมาหรือไม่

if (!$zone_id) {
    echo json_encode(["status" => "error", "message" => "zone_id is required"]);
    exit();
}

// เตรียม SQL query เพื่อดึงข้อมูลบูธตาม zone_id
$sql = "SELECT * FROM booth WHERE zone_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $zone_id);
$stmt->execute();
$result = $stmt->get_result();

// ตรวจสอบว่ามีข้อมูลบูธหรือไม่
if ($result->num_rows > 0) {
    $booths = $result->fetch_all(MYSQLI_ASSOC); // ดึงข้อมูลทั้งหมดในรูปแบบ associative array
    echo json_encode(["status" => "success", "booths" => $booths]);
} else {
    echo json_encode(["status" => "error", "message" => "No booths found for this zone"]);
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
