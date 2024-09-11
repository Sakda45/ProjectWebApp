<?php
session_start(); // เริ่มต้น session

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pj_webapp";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
$email = $_POST['email'];
$password = $_POST['password'];

// เตรียม SQL เพื่อตรวจสอบ email และ password
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $user = $result->fetch_assoc();
    
    // ตรวจสอบรหัสผ่าน
    if (password_verify($password, $user['password'])) {
        // ถ้ารหัสผ่านถูกต้อง
        $_SESSION['user'] = $user; // เก็บข้อมูลผู้ใช้ใน session
        header("Location: dashboard.php"); // ไปยังหน้า dashboard หลังจากเข้าสู่ระบบสำเร็จ
        exit();
    } else {
        // ถ้ารหัสผ่านไม่ถูกต้อง
        $_SESSION['error'] = 'invalid_password';
        header("Location: login.html?error=invalid_password");
        exit();
    }
} else {
    // ถ้าไม่พบ email นี้ในฐานข้อมูล
    $_SESSION['error'] = 'email_not_found';
    header("Location: login.html?error=email_not_found");
    exit();
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
