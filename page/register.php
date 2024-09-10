<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pj_webapp";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
$name = $_POST['name'];
$lastname = $_POST['lastname'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$confirmPassword = $_POST['confirm_password'];

// ตรวจสอบว่ามีอีเมลนี้อยู่ในฐานข้อมูลหรือไม่
$checkEmailSQL = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($checkEmailSQL);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($password !== $confirmPassword) {
    $_SESSION['error'] = 'password_mismatch';
    header("Location: register.html");
    exit();
}
if ($result->num_rows > 0) {
    // ส่งผู้ใช้กลับไปยังหน้า register พร้อมกับ query string ที่บอกว่าอีเมลซ้ำ
    header("Location: register.html?error=email_exists");
    exit();
} else {
    // สร้าง SQL query
    $sql = "INSERT INTO users (name, lastname, phone, email, password) VALUES (?, ?, ?, ?, ?)";

    // เตรียม statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $lastname, $phone, $email, $password);

    // Execute statement
    if ($stmt->execute()) {
        header("Location: login.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// ปิด statement และการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
