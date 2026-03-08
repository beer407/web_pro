
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการผู้เข้าร่วม - EventMatch</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; padding: 30px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        h1 { margin: 0; color: #2c3e50; font-size: 22px; }
        .btn-back { text-decoration: none; color: #3498db; font-weight: bold; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f1f3f5; color: #495057; padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #dee2e6; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        
        .user-info { display: flex; flex-direction: column; }
        .user-name { font-weight: bold; color: #212529; }
        .user-email { font-size: 12px; color: #6c757d; }
        
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        
        .action-btns { display: flex; gap: 8px; }
        .btn-action { padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: bold; transition: 0.2s; }
        .btn-app { background-color: #28a745; color: white; }
        .btn-rej { background-color: #dc3545; color: white; }
        .btn-app:hover { background-color: #218838; opacity: 0.9; }
        .btn-rej:hover { background-color: #c82333; opacity: 0.9; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h1>👥 จัดการผู้ขอเข้าร่วมกิจกรรม</h1>
        <a href="index.php" class="btn-back">⬅️ กลับหน้าหลัก</a>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>ข้อมูลผู้สมัคร</th>
                <th style="text-align: center;">สถานะ</th>
                <th style="text-align: right;">การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($participants)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #999;">ยังไม่มีคนสมัครเข้าร่วมครับ</td>
                </tr>
            <?php else: ?>
                <?php $i = 1; foreach($participants as $p): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td>
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($p['firstname'] ?? 'ไม่ระบุชื่อ') ?> <?= htmlspecialchars($p['lastname'] ?? '') ?></span>
                            <span class="user-email"><?= htmlspecialchars($p['email'] ?? '-') ?></span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <?php 
                            $status = $p['status'] ?? 'pending';
                            if($status == 'pending') echo '<span class="status-badge status-pending">⏳ รอการพิจารณา</span>';
                            elseif($status == 'approved') echo '<span class="status-badge status-approved">✅ อนุมัติแล้ว</span>';
                            else echo '<span class="status-badge status-rejected">❌ ปฏิเสธแล้ว</span>';
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php if(($p['status'] ?? 'pending') == 'pending'): ?>
                            <div class="action-btns">
                                <form method="POST" action="ManageParticipantsController.php" style="margin-left: auto;">
                                    <input type="hidden" name="reg_id" value="<?= $p['reg_id'] ?>">
                                    <input type="hidden" name="event_id" value="<?= $p['event_id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn-action btn-app">อนุมัติ</button>
                                </form>
                                <form method="POST" action="ManageParticipantsController.php">
                                    <input type="hidden" name="reg_id" value="<?= $p['reg_id'] ?>">
                                    <input type="hidden" name="event_id" value="<?= $p['event_id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn-action btn-rej" onclick="return confirm('ยืนยันการปฏิเสธ?')">ปฏิเสธ</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <span style="color: #ccc; font-size: 12px; font-style: italic;">พิจารณาแล้ว</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>