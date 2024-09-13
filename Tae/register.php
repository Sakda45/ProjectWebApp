<?php
// เริ่ม session
session_start(); 

// เชื่อมต่อฐานข้อมูล
$servername = "151.106.124.154";
$username = "u583789277_wag20";
$password = "2567Knock";
$dbname = "u583789277_wag20";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// อ่านข้อมูล JSON ที่ส่งมา
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// รับข้อมูลจาก JSON
$name = $data['name'];
$lastname = $data['lastname'];
$phone = $data['phone'];
$email = $data['email'];
$password = $data['password'];
$confirmPassword = $data['confirm_password'] ?? '';

// ตรวจสอบว่ารหัสผ่านกับยืนยันรหัสผ่านตรงกันหรือไม่
if ($password !== $confirmPassword) {
    echo json_encode(["status" => "error_password_mismatch"]);
}

// ตรวจสอบว่ามีอีเมลนี้อยู่ในฐานข้อมูลหรือไม่
$checkEmailSQL = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($checkEmailSQL);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error_email_exists"]);
} else {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO users (name, lastname, phone, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $lastname, $phone, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
}

$stmt->close();
$conn->close();

?>
