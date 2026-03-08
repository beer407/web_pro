<?php
session_start();

// 1. เช็คว่าล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อน'); window.location.href='../views/auth/login.php';</script>";
    exit();
}

// 2. เชื่อมต่อฐานข้อมูลและ Model (Path ถูกต้องแน่นอน)
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/RegistrationModel.php';

$registrationModel = new RegistrationModel($conn);
$user_id = $_SESSION['user_id'];

// 3. ดึงข้อมูลกิจกรรมของฉัน
$my_events = $registrationModel->getUserRegistrations($user_id);

// 4. ส่งข้อมูลไปแสดงผลที่หน้า View
require_once __DIR__ . '/../views/events/my_events.php';
?>