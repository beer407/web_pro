<?php
session_start();
// 1. แก้ Path ให้ตรงกับโครงสร้างคุณ เพื่อดึงไฟล์เชื่อมต่อฐานข้อมูลมาใช้
// บรรทัดที่ 4 ของไฟล์ login.php
require_once '../../config/database.php'; // ถอยออก 2 ชั้น (จาก auth -> views -> app) แล้วเข้า config

// ถ้าเข้าสู่ระบบอยู่แล้ว ให้ไปหน้า index ของคุณ
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

// ส่วนของการประมวลผล Login (ตอนกดปุ่ม Submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

// --- แก้ไขจากของเดิม เป็นแบบนี้ครับ ---
$email = $_POST['email'];
$password = $_POST['password'];

// ... ส่วน Query เดิมของคุณ ...
$sql = "SELECT user_id, firstname, password, role FROM user WHERE email = ?"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
// ... โค้ดส่วนเช็ค password_verify เดิมของคุณ ...
if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['firstname'];
    $_SESSION['role'] = $user['role']; 

// แก้จากของเดิมให้เป็นแบบนี้ครับ เพื่อให้กระโดดข้ามโฟลเดอร์ไปที่ events
// แก้ไขเงื่อนไขให้คนที่เป็น organizer เข้าหน้าสร้างได้ด้วย
if ($user['role'] === 'creator' || $user['role'] === 'organizer') {
    header("Location: ../events/create.php"); 
} else {
    header("Location: ../events/index.php"); 
}
exit();
}
} else {
    $error = "ไม่พบอีเมลนี้";
}
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Event Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-8">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">เข้าสู่ระบบ</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่าน</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition">
                เข้าสู่ระบบ
            </button>
        </form>

        <p class="text-center mt-4 text-gray-600">
            ยังไม่มีบัญชี? <a href="register.php" class="text-blue-500 hover:text-blue-700 font-medium">สมัครสมาชิก</a>
        </p>
    </div>
</body>
</html>