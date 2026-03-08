<?php
// app/models/EventModel.php
class EventModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createEvent($title, $description, $start_time, $end_time, $location, $max_attendees, $user_id) {
        $sql = "INSERT INTO event (title, description, start_time, end_time, location, max_attendees, user_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssii", $title, $description, $start_time, $end_time, $location, $max_attendees, $user_id);
        return $stmt->execute() ? $this->conn->insert_id : false;
    }

    public function addEventImage($event_id, $filename) {
        $sql = "INSERT INTO eventimage (event_id, filename) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $event_id, $filename);
        return $stmt->execute();
    }

    public function getAllEvents($search_name = '', $start_date = '', $end_date = '') {
        $sql = "SELECT e.*, 
                (SELECT filename FROM eventimage WHERE event_id = e.event_id LIMIT 1) as filename 
                FROM event e WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($search_name)) {
            $sql .= " AND e.title LIKE ?";
            $types .= "s";
            $params[] = "%$search_name%";
        }
        if (!empty($start_date)) {
            $sql .= " AND DATE(e.start_time) >= ?";
            $types .= "s";
            $params[] = $start_date;
        }
        if (!empty($end_date)) {
            $sql .= " AND DATE(e.start_time) <= ?";
            $types .= "s";
            $params[] = $end_date;
        }

        $sql .= " ORDER BY e.start_time DESC";
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) { $stmt->bind_param($types, ...$params); }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getEventImages($event_id) {
        $sql = "SELECT filename FROM eventimage WHERE event_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>