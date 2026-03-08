<?php
class RegistrationModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. ฟังก์ชันสำหรับกดจองเข้าร่วมกิจกรรม
    public function joinEvent($user_id, $event_id) {
        // เช็คว่าเคยจองหรือยัง
        $check_sql = "SELECT reg_id FROM registration WHERE user_id = ? AND event_id = ?";
        $stmt = $this->conn->prepare($check_sql);
        $stmt->bind_param("ii", $user_id, $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // ใช้ num_rows เพื่อเช็คจำนวนแถว
        if($result->num_rows > 0) { 
            return false; 
        }

        // ถ้าไม่ซ้ำ ให้บันทึกข้อมูล
        $sql = "INSERT INTO registration (user_id, event_id, status) VALUES (?, ?, 'pending')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $event_id);
        return $stmt->execute();
    }

    // 2. ฟังก์ชันดึงกิจกรรมทั้งหมดของ User คนนั้น
    public function getUserEvents($user_id) {
        $sql = "SELECT e.*, r.status, r.reg_id 
                FROM registration r 
                JOIN event e ON r.event_id = e.event_id 
                WHERE r.user_id = ? 
                ORDER BY r.reg_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

// 3. ฟังก์ชันดึงรายชื่อคนจองตาม ID กิจกรรม (สำหรับแอดมิน)
    public function getParticipantsByEvent($event_id) {
        // ดึง firstname และ lastname แยกออกมา และดึง event_id มาเพื่อใช้ทำ Link Redirect
        $sql = "SELECT r.reg_id, r.event_id, r.status, u.firstname, u.lastname, u.email 
                FROM registration r 
                JOIN user u ON r.user_id = u.user_id 
                WHERE r.event_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 4. ฟังก์ชันอัปเดตสถานะ (Approve/Reject)
    public function updateStatus($reg_id, $status) {
        $sql = "UPDATE registration SET status = ? WHERE reg_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $reg_id);
        return $stmt->execute();
    }

    // 5. ดึงประวัติการจองของ User 
    public function getUserRegistrations($user_id) {
        $sql = "SELECT e.title, e.location, e.start_time, r.status 
                FROM registration r 
                JOIN event e ON r.event_id = e.event_id 
                WHERE r.user_id = ?
                ORDER BY r.reg_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>