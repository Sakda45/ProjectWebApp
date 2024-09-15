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

// ตรวจสอบว่าเป็นการเรียกแบบ DELETE
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // รับข้อมูล JSON จาก request body
    $data = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบว่า zone_id ถูกส่งมาหรือไม่
    if (!isset($data['zone_name'])) {
        echo json_encode(["status" => "error", "message" => "Zone ID is required"]);
        exit();
    }

    // รับ zone_id
    $zone_name = $data['zone_name'];

    // เตรียมคำสั่ง SQL สำหรับการลบข้อมูล
    $stmt = $conn->prepare("DELETE FROM zones WHERE zone_name = ?");
    $stmt->bind_param("s", $zone_name);

    // รัน query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Zone deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No zone found with that ID"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete zone"]);
    }

    // ปิด statement และ connection
    $stmt->close();
}

$conn->close();
?>
