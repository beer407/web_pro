<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติการจอง - EventMatch</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* สไตล์ให้เหมือนในรูป image_17fb42.png */
        body { font-family: 'Sarabun', sans-serif; background-color: #f4f7f6; padding: 30px; }
        .card { max-width: 900px; margin: auto; background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); overflow: hidden; }
        .card-header { background: #007bff; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #343a40; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .status { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .pending { background: #ffc107; color: #000; }
        .approved { background: #28a745; color: #fff; }
        .rejected { background: #dc3545; color: #fff; }
        .btn-back { color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 5px; font-size: 12px; }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <h3 style="margin:0;">🗓️ ประวัติการจองกิจกรรมของฉัน</h3>
        <a href="index.php" class="btn-back">⬅️ กลับหน้าหลัก</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ชื่อกิจกรรม</th>
                <th>สถานที่</th>
                <th>วันที่เริ่ม</th>
                <th style="text-align:center;">สถานะการจอง</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($userEvents)): ?>
                <tr><td colspan="4" style="text-align:center; padding:30px; color:#999;">คุณยังไม่ได้จองกิจกรรมใดๆ</td></tr>
            <?php else: ?>
                <?php foreach($userEvents as $event): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($event['title']) ?></strong></td>
                    <td><?= htmlspecialchars($event['location']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($event['start_time'])) ?></td>
                    <td style="text-align:center;">
                        <?php 
                            $st = $event['status'];
                            if($st == 'pending') echo "<span class='status pending'>⌛ รออนุมัติ</span>";
                            elseif($st == 'approved') echo "<span class='status approved'>✅ อนุมัติแล้ว</span>";
                            else echo "<span class='status rejected'>❌ ปฏิเสธ</span>";
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>