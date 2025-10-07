<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ระบบบันทึกไปราชการ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      font-family: "TH SarabunPSK", sans-serif;
      font-size: 16pt;
      background-color: #f4f6f8;
    }
    .menu-card {
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 30px;
      text-align: center;
      transition: transform 0.2s;
    }
    .menu-card:hover {
      transform: translateY(-5px);
    }
    .menu-card a {
      display: block;
      margin-top: 10px;
      font-size: 18pt;
      text-decoration: none;
      color: #007BFF;
    }
    .menu-card a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <h2 class="text-center mb-5">📁 ระบบบันทึกคำขอไปราชการ</h2>
    <div class="row justify-content-center g-4">
      <div class="col-md-4">
        <div class="menu-card">
          <div>📝 เพิ่มบันทึกไปราชการใหม่</div>
          <a href="submit_typ.php">ไปยังฟอร์มบันทึก</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="menu-card">
          <div>📋 รายการย้อนหลัง/ปริ้น</div>
          <a href="list_trip.php">ดูรายการทั้งหมด</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="menu-card">
          <div>👥 จัดการรายชื่อบุคคล</div>
          <a href="manage_people.php">แก้ไขรายชื่อ</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
