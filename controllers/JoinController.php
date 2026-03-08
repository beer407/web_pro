<?php
session_start();
require_once '../config/database.php';
require_once '../models/RegistrationModel.php';

// ... (เช็ค login, รับค่า event_id) ...

$regModel = new RegistrationModel($conn);
$user_id = $_SESSION['user_id'];
$event_id = $_POST['event_id'];

// ทำการจอง (สถานะเริ่มต้นมักจะเป็น 'pending')
$result = $regModel->joinEvent($user_id, $event_id);

if ($result) {
    // จองสำเร็จ! ให้เด้งกลับไปหน้าแรก (index.php) ไม่ใช่หน้าจัดการผู้เข้าร่วม
    header("Location: ../views/events/index.php"); 
    exit();
} else {
    // กรณีจองไม่สำเร็จ
    echo "เกิดข้อผิดพลาดในการจอง";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id']; 

    // ตัวแปร $conn มาจากไฟล์ database.php
    $registrationModel = new RegistrationModel($conn);
    
if ($registrationModel->joinEvent($user_id, $event_id)) {
    echo "<script>
            alert('🎉 ส่งคำขอเข้าร่วมสำเร็จ!'); 
            window.location.href='../controllers/ManageParticipantsController.php?event_id=" . $event_id . "';
          </script>";
} else {
    echo "<script>
            alert('⚠️ คุณเคยจองกิจกรรมนี้ไปแล้ว'); 
            window.location.href='../views/events/index.php';
          </script>";
}
} else {
    header("Location: ../views/events/index.php");
    exit();
}
?>