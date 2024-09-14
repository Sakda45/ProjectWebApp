<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "151.106.124.154";
$username = "u583789277_wag19";
$password = "2567Inspire";
$dbname = "u583789277_wag19";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// ตรวจสอบว่าเป็นการเรียกแบบ PUT
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // รับข้อมูล JSON จาก request body
    $data = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบว่าค่าที่ต้องการถูกส่งมาครบหรือไม่
    if (!isset($data['id']) || !isset($data['zone_name']) || !isset($data['num_booths'])) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit();
    }

    // รับค่า JSON
    $id = $data['id'];
    $zone_name = $data['zone_name'];
    $num_booths = $data['num_booths'];

    // เตรียมคำสั่ง SQL สำหรับการแก้ไขข้อมูล
    $stmt = $conn->prepare("UPDATE zones SET zone_name = ?, num_booths = ? WHERE id = ?");
    $stmt->bind_param("sii", $zone_name, $num_booths, $id);

    // รัน query
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Zone updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update zone"]);
    }

    // ปิด statement และ connection
    $stmt->close();
}

$conn->close();
?>
