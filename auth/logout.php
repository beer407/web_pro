<?php
session_start(); // เริ่มต้น session เพื่อให้รู้จักว่าใครล็อกอินอยู่
session_unset(); // ล้างค่าตัวแปร session ทั้งหมด (เช่น user_id, user_name)
session_destroy(); // ทำลาย session ทิ้ง

// เด้งกลับไปหน้า Login หลังจากออกจากระบบสำเร็จ
header("Location: login.php");
exit();
?>