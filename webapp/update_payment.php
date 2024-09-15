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

// รับข้อมูลที่จำเป็นจาก request (ผ่าน JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$booking_id = $data['booking_id'] ?? null;
$payment_slip = $data['payment_slip'] ?? null;

// ตรวจสอบว่ามีข้อมูลที่จำเป็นครบหรือไม่
if (!$booking_id || !$payment_slip) {
    echo json_encode(["status" => "error", "message" => "Required fields are missing"]);
    exit();
}

// ตรวจสอบสถานะของ booking ก่อน
$sql_check_status = "SELECT status FROM bookings WHERE id = ?";
$stmt_check_status = $conn->prepare($sql_check_status);
$stmt_check_status->bind_param("i", $booking_id);
$stmt_check_status->execute();
$result = $stmt_check_status->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo json_encode(["status" => "error", "message" => "Booking not found"]);
    exit();
}

$status = $booking['status'];

// ไม่อนุญาตให้ชำระเงินถ้าสถานะเป็น 'expired' หรือ 'cancelled'
if ($status === 'expired' || $status === 'cancelled') {
    echo json_encode(["status" => "error", "message" => "Cannot make a payment for a booking that is expired or cancelled"]);
    exit();
}

// เตรียม SQL query เพื่ออัปเดต payment_slip และ payment_status ในตาราง payment โดยใช้ booking_id เป็นคีย์
$sql_update_payment = "UPDATE payment SET payment_slip = ?, payment_status = 'pending' WHERE booking_id = ?";
$stmt_update_payment = $conn->prepare($sql_update_payment);

if (!$stmt_update_payment) {
    die(json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]));
}

// Bind ค่าที่จะใช้ใน query (payment_slip และ booking_id)
$stmt_update_payment->bind_param("si", $payment_slip, $booking_id);

// Execute query
if ($stmt_update_payment->execute()) {
    echo json_encode(["status" => "success", "message" => "Payment slip updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error updating payment: " . $stmt_update_payment->error]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$stmt_check_status->close();
$stmt_update_payment->close();
$conn->close();
?>
