<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กิจกรรมของฉัน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">📅 ประวัติการจองกิจกรรมของฉัน</h4>
            <a href="../views/events/index.php" class="btn btn-light btn-sm">⬅️ กลับหน้าแรก</a>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ชื่อกิจกรรม</th>
                        <th>สถานที่</th>
                        <th>วันที่เริ่ม</th>
                        <th>สถานะการจอง</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($my_events)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">คุณยังไม่มีประวัติการจองกิจกรรม</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($my_events as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['title'] ?? $event['event_name'] ?? 'ไม่ระบุ') ?></td>
                                <td><?= htmlspecialchars($event['location'] ?? 'ไม่ระบุ') ?></td>
                                <td><?= htmlspecialchars($event['start_time'] ?? 'ไม่ระบุ') ?></td>
                                <td class="text-center">
                                    <?php if ($event['status'] == 'pending'): ?>
                                        <span class="badge bg-warning text-dark px-3 py-2">⏳ รออนุมัติ</span>
                                    <?php elseif ($event['status'] == 'approved'): ?>
                                        <span class="badge bg-success px-3 py-2">✅ อนุมัติแล้ว</span>
                                    <?php elseif ($event['status'] == 'rejected'): ?>
                                        <span class="badge bg-danger px-3 py-2">❌ ปฏิเสธ</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($event['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>