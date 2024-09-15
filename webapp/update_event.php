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

// ตรวจสอบว่าเป็นการเรียกแบบ PUT
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // รับข้อมูล JSON จาก request body
    $data = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบว่าค่าที่ต้องการถูกส่งมาครบหรือไม่
    if (!isset($data['event_id']) || !isset($data['event_name']) || !isset($data['event_start_date']) || !isset($data['event_end_date'])) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit();
    }

    // รับค่า JSON
    $event_id = $data['event_id'];  // รหัสของงานที่ต้องการแก้ไข
    $event_name = $data['event_name'];
    $event_start_date = $data['event_start_date'];
    $event_end_date = $data['event_end_date'];

    // เตรียมคำสั่ง SQL สำหรับการแก้ไขข้อมูล event
    $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_start_date = ?, event_end_date = ? WHERE event_id = ?");
    $stmt->bind_param("sssi", $event_name, $event_start_date, $event_end_date, $event_id);

    // รัน query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Event updated successfully"]);

            // อัปเดต payment_due_date ในตาราง bookings โดยกำหนดให้เป็น 5 วันก่อนวันเริ่มงานใหม่
            $sql_update_due_date = "UPDATE bookings 
                                    SET payment_due_date = DATE_SUB(?, INTERVAL 5 DAY) 
                                    WHERE event_id = ?";
            $stmt_update_due_date = $conn->prepare($sql_update_due_date);
            $stmt_update_due_date->bind_param("si", $event_start_date, $event_id);
            $stmt_update_due_date->execute();

            // ตรวจสอบสถานะ booking ที่เป็น 'pending' และ payment_due_date เลยวันที่ปัจจุบัน
            $sql_expire_booking = "UPDATE bookings 
                                   SET status = 'expired' 
                                   WHERE event_id = ? 
                                   AND status = 'pending' 
                                   AND payment_due_date < CURDATE()";
            $stmt_expire_booking = $conn->prepare($sql_expire_booking);
            $stmt_expire_booking->bind_param("i", $event_id);
            $stmt_expire_booking->execute();

            $stmt_update_due_date->close();
            $stmt_expire_booking->close();
        } else {
            echo json_encode(["status" => "error", "message" => "No event found with the given ID"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update event"]);
    }

    // ปิด statement และ connection
    $stmt->close();
}

// ปิดการเชื่อมต่อกับฐานข้อมูล
$conn->close();
?>
