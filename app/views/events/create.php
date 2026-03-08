<?php
session_start();
// อนุญาตทั้ง creator และ organizer
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['creator', 'organizer'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างกิจกรรมใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">สร้างกิจกรรมใหม่</h4>
                    </div>
                    <div class="card-body">
                        <form action="../../controllers/EventController.php" method="POST" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label class="form-label">ชื่อกิจกรรม</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">รายละเอียดกิจกรรม</label>
                                <textarea name="description" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">เวลาเริ่มกิจกรรม</label>
                                    <input type="datetime-local" name="start_time" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">เวลาสิ้นสุดกิจกรรม</label>
                                    <input type="datetime-local" name="end_time" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">สถานที่จัดงาน</label>
                                    <input type="text" name="location" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">จำนวนคนที่รับสมัคร (คน)</label>
                                    <input type="number" name="max_attendees" class="form-control" min="1" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <style>
                                    .image-container {
                                        border: 1px solid #ddd;
                                        padding: 15px;
                                        border-radius: 8px;
                                        background: #fafafa;
                                    }

                                    .image-item {
                                        display: flex;
                                        align-items: center;
                                        justify-content: space-between;
                                        background: white;
                                        padding: 8px;
                                        margin-bottom: 8px;
                                        border: 1px solid #eee;
                                        border-radius: 4px;
                                    }

                                    .image-item span {
                                        font-size: 14px;
                                        color: #555;
                                    }

                                    .btn-remove {
                                        color: #ff4d4d;
                                        cursor: pointer;
                                        font-weight: bold;
                                        border: none;
                                        background: none;
                                    }

                                    .upload-area {
                                        margin-top: 15px;
                                        display: flex;
                                        gap: 10px;
                                        align-items: center;
                                    }

                                    .limit-text {
                                        font-size: 12px;
                                        color: #888;
                                        margin-top: 5px;
                                    }
                                </style>

                                <div class="form-group">
                                    <label>รูปภาพกิจกรรม (สูงสุด 3 รูป)</label>
                                    <div class="image-container">
                                        <div id="imageList"></div>

                                        <div class="upload-area">
                                            <input type="file" id="imageInput" accept="image/*" class="form-control">
                                            <button type="button" onclick="addImage()" id="btnAdd" class="btn btn-primary" style="padding: 5px 15px;"> + </button>
                                        </div>
                                        <p class="limit-text">* จะบันทึกเฉพาะ 3 รูปแรกที่อยู่ในรายการนี้เท่านั้น</p>
                                    </div>
                                </div>

                                <div id="fileInputsContainer" style="display: none;"></div>

                                <script>
                                    let imageCount = 0;
                                    const maxImages = 3;

                                    function addImage() {
                                        const input = document.getElementById('imageInput');
                                        const list = document.getElementById('imageList');
                                        const hiddenContainer = document.getElementById('fileInputsContainer');

                                        if (input.files.length === 0) return;
                                        if (imageCount >= maxImages) {
                                            alert("ใส่รูปได้สูงสุด 3 รูปเท่านั้นครับ");
                                            return;
                                        }

                                        const file = input.files[0];
                                        const fileName = file.name;
                                        imageCount++;

                                        // 1. สร้างแถวแสดงชื่อรูปและปุ่มลบ (-)
                                        const div = document.createElement('div');
                                        div.className = 'image-item';
                                        div.id = 'img-item-' + imageCount;
                                        div.innerHTML = `
            <span>🖼️ ${fileName}</span>
            <button type="button" class="btn-remove" onclick="removeImage(${imageCount})">ลบ</button>
        `;
                                        list.appendChild(div);

                                        // 2. ย้ายไฟล์ไปไว้ในช่องที่จะส่งไป PHP (Clone input)
                                        const newHiddenInput = input.cloneNode();
                                        newHiddenInput.name = "event_images[]"; // ใช้ก้ามปูเพื่อส่งเป็น Array
                                        newHiddenInput.id = 'hidden-input-' + imageCount;
                                        hiddenContainer.appendChild(newHiddenInput);

                                        // 3. ล้างช่องเลือกไฟล์เดิมให้ว่างเพื่อเลือกรูปถัดไป
                                        input.value = "";
                                    }

                                    function removeImage(id) {
                                        document.getElementById('img-item-' + id).remove();
                                        document.getElementById('hidden-input-' + id).remove();
                                        imageCount--;
                                    }
                                </script>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">บันทึกกิจกรรม</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>