<?php
session_start();
require_once __DIR__ . '/../config/database.php'; 
require_once __DIR__ . '/../models/RegistrationModel.php'; 

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$registrationModel = new RegistrationModel($conn);
$user_id = $_SESSION['user_id'];

// --- ส่วนจัดการข้อมูล (POST) สำหรับ Approve/Reject ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $reg_id = $_POST['reg_id'];
    $action = $_POST['action'];
    $event_id = $_POST['event_id']; 

    if ($action == 'approve') {
        $registrationModel->updateStatus($reg_id, 'approved');
    } elseif ($action == 'reject') {
        $registrationModel->updateStatus($reg_id, 'rejected');
    }
    
    // Redirect เพื่อล้างค่า POST และกำจัด Error Path
    header("Location: ManageParticipantsController.php?event_id=" . $event_id);
    exit();
}

// --- ส่วนแสดงผล (GET) เลือกหน้า View ---
if (isset($_GET['mode']) && $_GET['mode'] == 'history') {
    // ดึงประวัติการจองของผู้ใช้มาแสดง
    $userEvents = $registrationModel->getUserEvents($user_id);
    require_once '../views/events/history_view.php'; 
} else {
    // หน้าจัดการคนสมัครเข้าร่วม
    $event_id = isset($_GET['event_id']) ? $_GET['event_id'] : 8;
    $participants = $registrationModel->getParticipantsByEvent($event_id);
    require_once '../views/events/manage_participants.php';
}
?>