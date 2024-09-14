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

// รับค่า booth_id ที่ต้องการจาก request (เช่นผ่าน URL หรือ JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$booth_id = $data['booth_id'] ?? null; // ตรวจสอบว่ามี booth_id ถูกส่งมาหรือไม่

if (!$booth_id) {
    echo json_encode(["status" => "error", "message" => "booth_id is required"]);
    exit();
}

// เตรียม SQL query เพื่อดึงข้อมูลบูธตาม booth_id
$sql = "SELECT * FROM booth WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booth_id);
$stmt->execute();
$result = $stmt->get_result();

// ตรวจสอบว่ามีข้อมูลบูธหรือไม่
if ($result->num_rows > 0) {
    $booth = $result->fetch_assoc(); // ดึงข้อมูลแถวเดียว (บูธ)
    echo json_encode(["status" => "success", "booth" => $booth]);
} else {
    echo json_encode(["status" => "error", "message" => "Booth not found"]);
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
