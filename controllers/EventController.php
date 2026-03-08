<?php
// app/controllers/EventController.php
session_start(); 

require_once '../config/database.php';
require_once '../models/EventModel.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. รับค่าจากฟอร์ม
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $location = $_POST['location'];
    $max_attendees = $_POST['max_attendees'];
    
    // ดึง user_id จาก Session (ถ้าไม่มีให้เด้งไป Login)
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('กรุณาเข้าสู่ระบบก่อนสร้างกิจกรรม'); window.location.href='../views/auth/login.php';</script>";
        exit();
    }
    $user_id = $_SESSION['user_id']; 

    $eventModel = new EventModel($conn);
    
    // 2. บันทึกกิจกรรมหลัก
    $new_event_id = $eventModel->createEvent($title, $description, $start_time, $end_time, $location, $max_attendees, $user_id);

    if ($new_event_id) {
        // ✅ Path นี้จะเก็บรูปไว้ที่โฟลเดอร์ uploads นอกสุดของโปรเจกต์
        $upload_dir = '../../uploads/'; 
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (isset($_FILES['event_images']) && !empty($_FILES['event_images']['name'][0])) {
            foreach ($_FILES['event_images']['tmp_name'] as $key => $tmp_name) {
                if ($key >= 3) break; // จำกัด 3 รูป

                if ($_FILES['event_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_extension = pathinfo($_FILES['event_images']['name'][$key], PATHINFO_EXTENSION);
                    $new_filename = 'event_' . $new_event_id . '_' . time() . '_' . $key . '.' . $file_extension;
                    $target_file = $upload_dir . $new_filename;

                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $eventModel->addEventImage($new_event_id, $new_filename);
                    }
                }
            }
        }

        echo "<script>alert('🎉 สร้างกิจกรรมสำเร็จแล้ว!'); window.location.href='../views/events/index.php';</script>";
    } else {
        echo "<script>alert('❌ บันทึกข้อมูลไม่สำเร็จ'); window.history.back();</script>";
    }
}
?>