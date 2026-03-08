<?php
session_start();
require_once '../../config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../events/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $role = $_POST['role']; // ให้เลือกตอนสมัครว่าเป็น user หรือ organizer

    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($birthdate)) {
        $error = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    } else {
        // เช็คว่าอีเมลซ้ำไหม
        $check_sql = "SELECT user_id FROM user WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = 'อีเมลนี้ถูกใช้งานแล้ว';
        } else {
            // เข้ารหัสรหัสผ่าน
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // บันทึกลงฐานข้อมูลแบบ MySQLi
            $insert_sql = "INSERT INTO user (firstname, lastname, email, password, gender, birthdate, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            
            if ($stmt) {
                $stmt->bind_param("sssssss", $firstname, $lastname, $email, $hashed_password, $gender, $birthdate, $role);
                if ($stmt->execute()) {
                    $success = 'สมัครสมาชิกสำเร็จ! <a href="login.php" class="underline font-bold">เข้าสู่ระบบคลิกที่นี่</a>';
                } else {
                    $error = 'เกิดข้อผิดพลาดในการสมัครสมาชิก';
                }
            } else {
                $error = 'SQL Error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก - EventMatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Sarabun', sans-serif; } </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-8">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-lg">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">สมัครสมาชิก</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700">ชื่อ</label>
                    <input type="text" name="firstname" required class="w-full border rounded-lg px-4 py-2 mt-1">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">นามสกุล</label>
                    <input type="text" name="lastname" required class="w-full border rounded-lg px-4 py-2 mt-1">
                </div>
            </div>
            
            <div>
                <label class="block text-sm text-gray-700">อีเมล</label>
                <input type="email" name="email" required class="w-full border rounded-lg px-4 py-2 mt-1">
            </div>

            <div>
                <label class="block text-sm text-gray-700">รหัสผ่าน</label>
                <input type="password" name="password" required class="w-full border rounded-lg px-4 py-2 mt-1">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700">เพศ</label>
                    <select name="gender" class="w-full border rounded-lg px-4 py-2 mt-1">
                        <option value="male">ชาย</option>
                        <option value="female">หญิง</option>
                        <option value="other">อื่นๆ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">วันเกิด</label>
                    <input type="date" name="birthdate" required class="w-full border rounded-lg px-4 py-2 mt-1">
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-700">ประเภทบัญชี</label>
                <select name="role" class="w-full border rounded-lg px-4 py-2 mt-1">
                    <option value="user">ผู้เข้าร่วมกิจกรรมทั่วไป (User)</option>
                    <option value="organizer">ผู้จัดกิจกรรม (Organizer)</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg mt-4">สมัครสมาชิก</button>
        </form>
        <p class="text-center mt-4 text-gray-600">มีบัญชีอยู่แล้ว? <a href="login.php" class="text-blue-500 hover:text-blue-700">เข้าสู่ระบบ</a></p>
    </div>
</body>
</html>