<?php
// 1. เปิด Session เพื่อจำค่าการ Login
session_start();

// 2. ดึงตัวเชื่อมต่อฐานข้อมูลและ Model
require_once '../../config/database.php';
require_once '../../models/EventModel.php';
require_once '../../models/RegistrationModel.php'; // เพิ่ม Model ตัวนี้เข้ามา

$eventModel = new EventModel($conn);
$regModel = new RegistrationModel($conn); // สร้าง Object สำหรับเช็คการจอง

// 3. รับค่าจากช่องค้นหา
$search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// 4. ดึงข้อมูลกิจกรรมทั้งหมด
$events = $eventModel->getAllEvents($search_name, $start_date, $end_date);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหาและเข้าร่วมกิจกรรม - EventMatch</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2ecc71;
            --danger: #e74c3c;
            --dark: #2c3e50;
            --light: #f4f7f6;
            --warning: #f1c40f;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--light);
            margin: 0;
            padding: 0;
            color: var(--dark);
        }

        /* Header */
        .main-header {
            background: white;
            padding: 10px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo { font-size: 24px; color: var(--primary); text-decoration: none; }

        .user-nav { display: flex; align-items: center; gap: 10px; }

        .user-nav a {
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 13px;
            transition: 0.3s;
        }

        .btn-create { background: var(--secondary); color: white !important; }
        .btn-create:hover { background: #27ae60; box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3); }

        /* ปุ่มประวัติการจองสีเหลืองทอง */
        .btn-history { background: var(--warning); color: #000 !important; }
        .btn-history:hover { background: #f39c12; box-shadow: 0 4px 12px rgba(241, 196, 15, 0.3); }

        .btn-login { background: var(--primary); color: white; }
        .btn-logout { background: #eee; color: var(--danger); }
        .btn-logout:hover { background: var(--danger); color: white; }

        /* Search Section */
        .search-container {
            background: var(--primary);
            padding: 40px 5%;
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .search-box {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 10px;
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            justify-content: center;
        }

        .search-box input {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            font-family: 'Sarabun';
        }

        .btn-search {
            padding: 10px 25px;
            background: var(--dark);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        /* Event Cards */
        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 0 5% 50px;
            max-width: 1300px;
            margin: auto;
        }

        .event-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: 0.4s;
            display: flex;
            flex-direction: column;
            border: 1px solid #eee;
        }

        .event-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }

        .slider-box {
            width: 100%;
            height: 200px;
            position: relative;
            background: #eee;
        }

        .slides-wrapper {
            display: flex;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }

        .slides-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .slider-nav {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            background: rgba(0,0,0,0.2);
            padding: 5px 10px;
            border-radius: 20px;
        }

        .nav-dot {
            width: 8px;
            height: 8px;
            background: rgba(255,255,255,0.5);
            border-radius: 50%;
            border: none;
            cursor: pointer;
        }

        .nav-dot.active { background: white; transform: scale(1.3); }

        .card-body { padding: 20px; flex-grow: 1; }
        .card-body h3 { margin: 0 0 10px; font-size: 20px; color: var(--dark); }
        .desc { color: #7f8c8d; font-size: 14px; line-height: 1.6; margin-bottom: 15px; height: 45px; overflow: hidden; }

        .info-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            margin-bottom: 8px;
            color: #555;
        }

        .date-badge {
            background: #f0f7ff;
            color: var(--primary);
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 12px;
        }

        .btn-join {
            width: 100%;
            padding: 12px;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 15px;
        }

        .btn-join:hover:not(:disabled) { background: #27ae60; letter-spacing: 1px; }
        .btn-join:disabled { cursor: not-allowed; color: white; }
    </style>
</head>

<body>

    <header class="main-header">
        <a href="index.php" class="logo"><strong>🌟 EventMatch</strong></a>
        
        <div class="user-nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="ManageParticipantsController.php?mode=history" class="btn">📋 ประวัติการจอง</a>
</a>

                <?php if ($_SESSION['role'] === 'organizer' || $_SESSION['role'] === 'creator'): ?>
                    <a href="create.php" class="btn-create">➕ สร้างกิจกรรม</a>
                <?php endif; ?>

                <span style="font-size: 14px; margin-left: 10px;">สวัสดี, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></span>
                <a href="../auth/logout.php" class="btn-logout">ออกจากระบบ</a>
            <?php else: ?>
                <a href="../auth/login.php" class="btn-login">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="search-container">
        <h1 style="margin-bottom: 25px;">ค้นหากิจกรรมที่คุณสนใจ 🔎</h1>
        <form class="search-box" method="GET" action="index.php">
            <input type="text" name="search_name" value="<?= htmlspecialchars($search_name) ?>" placeholder="ชื่อกิจกรรมที่ต้องการ...">
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
            <button type="submit" class="btn-search">ค้นหา</button>
            <a href="index.php" style="text-decoration: none; color: #999; align-self: center; font-size: 14px;">ล้างตัวกรอง</a>
        </form>
    </div>

    <div class="event-grid">
        <?php if (count($events) > 0): ?>
            <?php foreach ($events as $event): ?>
                <?php 
                    $user_status = null;
                    if (isset($_SESSION['user_id'])) {
                        $check_sql = "SELECT status FROM registration WHERE user_id = ? AND event_id = ?";
                        $stmt = $conn->prepare($check_sql);
                        $stmt->bind_param("ii", $_SESSION['user_id'], $event['event_id']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if($row = $res->fetch_assoc()) { 
                            $user_status = $row['status']; 
                        }
                    }
                ?>
                <div class="event-card">
                    <div class="slider-box">
                        <div class="slides-wrapper" id="slider-<?= $event['event_id']; ?>">
                            <?php
                            $images = $eventModel->getEventImages($event['event_id']);
                            if (!empty($images)):
                                foreach ($images as $img): ?>
                                    <img src="../../../uploads/<?php echo $img['filename']; ?>" alt="กิจกรรม">
                                <?php endforeach;
                            else: ?>
                                <img src="https://placehold.co/600x400?text=No+Image" alt="ไม่มีรูปภาพ">
                            <?php endif; ?>
                        </div>
                        
                        <?php if (count($images) > 1): ?>
                            <div class="slider-nav">
                                <?php foreach ($images as $key => $img): ?>
                                    <button class="nav-dot <?= $key === 0 ? 'active' : '' ?>" 
                                            onclick="jumpSlide(<?= $key ?>, <?= $event['event_id'] ?>, this)"></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-body">
                        <div class="date-badge">
                            🗓️ <?= date('d M Y', strtotime($event['start_time'])) ?>
                        </div>
                        <h3><?= htmlspecialchars($event['title']) ?></h3>
                        <p class="desc"><?= htmlspecialchars(mb_strimwidth($event['description'], 0, 100, '...')) ?></p>
                        
                        <div class="info-row">
                            <span>📍</span> <strong>สถานที่:</strong> <?= htmlspecialchars($event['location']) ?>
                        </div>
                        
                        <div class="info-row" style="color: #2980b9;">
                            <span>⏰</span> <strong>เริ่ม:</strong> <?= date('d/m/Y H:i', strtotime($event['start_time'])) ?> น.
                        </div>

                        <?php if (!empty($event['end_time'])): ?>
                        <div class="info-row" style="color: #c0392b;">
                            <span>⌛</span> <strong>สิ้นสุด:</strong> <?= date('d/m/Y H:i', strtotime($event['end_time'])) ?> น.
                        </div>
                        <?php endif; ?>

                        <?php if ($user_status === 'pending'): ?>
                            <button type="button" class="btn-join" disabled style="background-color: #f39c12;">
                                ⏳ รอการอนุมัติ...
                            </button>
                        <?php elseif ($user_status === 'approved'): ?>
                            <button type="button" class="btn-join" disabled style="background-color: #27ae60;">
                                ✅ อนุมัติแล้ว
                            </button>
                        <?php elseif ($user_status === 'rejected'): ?>
                            <button type="button" class="btn-join" disabled style="background-color: #e74c3c;">
                                ❌ ปฏิเสธการเข้าร่วม
                            </button>
                        <?php else: ?>
                            <form action="../../controllers/JoinController.php" method="POST" style="margin:0;">
                                <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                <button type="submit" class="btn-join">จองเข้าร่วมกิจกรรม</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 100px 0; color: #bdc3c7;">
                <h3>ไม่พบกิจกรรมที่คุณกำลังค้นหา...</h3>
                <p>ลองเปลี่ยนคำค้นหา หรือกดล้างตัวกรองดูนะ</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function jumpSlide(index, id, btn) {
            const wrapper = document.getElementById('slider-' + id);
            wrapper.style.transform = `translateX(-${index * 100}%)`;
            
            const dots = btn.parentElement.querySelectorAll('.nav-dot');
            dots.forEach(dot => dot.classList.remove('active'));
            btn.classList.add('active');
        }
    </script>
</body>
</html>