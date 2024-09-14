<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // เริ่ม session

// เชื่อมต่อฐานข้อมูล
$servername = "151.106.124.154";
$username = "u583789277_wag19";
$password = "2567Inspire";
$dbname = "u583789277_wag19";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// อ่านข้อมูล JSON ที่ส่งมา
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// ตรวจสอบว่าข้อมูล JSON ถูกส่งมาครบถ้วนหรือไม่
if (!$data) {
    die(json_encode(["status" => "error", "message" => "Invalid JSON input"]));
}

// รับข้อมูลจาก JSON
$id = $data['id'] ?? null;
$booth_name = $data['booth_name'] ?? null;
$booth_size = $data['booth_size'] ?? null;
$status = $data['status'] ?? null;
$price = $data['price'] ?? null;
$image_url = $data['image_url'] ?? null;
$zone_id = $data['zone_id'] ?? null;

if (!$id || !$booth_name || !$booth_size || !$status || !$price || !$zone_id) {
    echo json_encode(["status" => "error", "message" => "Required fields are missing or invalid"]);
    exit();
}


// เตรียม SQL สำหรับการอัปเดตข้อมูลบูธ
$sql = "UPDATE booth SET booth_name=?, booth_size=?, status=?, price=?, image_url=?, zone_id=? WHERE id=?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "SQL preparation failed: " . $conn->error]));
}

// ใช้ตัวแปรที่ได้จาก JSON
$stmt->bind_param("sssisis", $booth_name, $booth_size, $status, $price, $image_url, $zone_id, $id);


// ดำเนินการ query
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Booth information updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error updating booth information: " . $stmt->error]);
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
