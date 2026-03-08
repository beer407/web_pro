<?php
$host = "localhost";
$username = "root";
$password = ""; 
$dbname = "test"; // ตรวจสอบชื่อ DB ให้ตรงกับใน phpMyAdmin นะครับ

// 1. สร้างการเชื่อมต่อแบบ MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// 2. ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// 3. ตั้งค่าให้รองรับภาษาไทย (แก้ปัญหาเครื่องหมายคำถาม ???? )
$conn->set_charset("utf8mb4"); 
?>