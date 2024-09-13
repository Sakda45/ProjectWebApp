<?php
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

// รับข้อมูลจาก JSON ที่ส่งมา
$user_id = $data['user_id'] ?? null; // หรือใช้ email แทนในกรณีที่ต้องการ
$name = $data['name'] ?? null;
$lastname = $data['lastname'] ?? null;
$phone = $data['phone'] ?? null;
$email = $data['email'] ?? null;

// ตรวจสอบว่ามีข้อมูลที่จำเป็นครบหรือไม่
if (!$user_id || !$name || !$lastname || !$phone || !$email) {
    echo json_encode(["status" => "error", "message" => "Required fields are missing"]);
    exit();
}

// เตรียม SQL สำหรับการแก้ไขข้อมูลผู้ใช้
$sql = "UPDATE users SET name = ?, lastname = ?, phone = ?, email = ? WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $name, $lastname, $phone, $email, $user_id);

// ดำเนินการ query
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "User information updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error updating user information: " . $stmt->error]);
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
