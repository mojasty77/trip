<?php
require 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 📥 เพิ่มข้อมูลใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
  $name = $_POST['full_name'] ?? '';
  $position = $_POST['position'] ?? '';
  if ($name && $position) {
    $stmt = $conn->prepare("INSERT INTO people (full_name, position) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $position);
    $stmt->execute();
  }
}

// 🗑️ ลบข้อมูล
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM people WHERE id = $id");
}

// 🔍 ดึงข้อมูลทั้งหมด
$result = $conn->query("SELECT * FROM people ORDER BY full_name ASC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการรายชื่อบุคคล</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      font-family: "TH SarabunPSK", sans-serif;
      font-size: 16pt;
      background-color: #f4f6f8;
    }
    .sidebar {
      background-color: #2c3e50;
      color: white;
      min-height: 100vh;
    }
    .sidebar h4 {
      margin-top: 20px;
      margin-bottom: 20px;
      padding-left: 20px;
    }
    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 6px;
      transition: background-color 0.2s;
    }
    .sidebar a:hover {
      background-color: #34495e;
    }
    .form-area {
      padding: 40px;
      background-color: white;
    }
    h2 {
      margin-bottom: 20px;
    }
    form {
      margin-bottom: 30px;
      background-color: #f9f9f9;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    input[type="text"] {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      margin-bottom: 10px;
      font-size: 16pt;
    }
    button {
      padding: 8px 16px;
      font-size: 16pt;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      border: 1px solid #999;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
    .actions a {
      margin-right: 10px;
      text-decoration: none;
      color: #007BFF;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- เมนูด้านซ้าย -->
      <nav class="col-md-3 col-lg-2 sidebar py-4">
        <h4>เมนู</h4>
        <a href="index.php">🏠 กลับหน้าแรก</a>
        <a href="submit_typ.php">📝 ทำบันทึกไปราชการ</a>
        <a href="list_trip.php">📑 แก้ไขรายการย้อนหลัง</a>
      </nav>

      <!-- เนื้อหาด้านขวา -->
      <main class="col-md-9 col-lg-10 form-area">
        <h2>👥 จัดการรายชื่อบุคคล</h2>

        <!-- ➕ ฟอร์มเพิ่มรายชื่อใหม่ -->
        <form method="post">
          <label>ชื่อ-นามสกุล</label>
          <input type="text" name="full_name" placeholder="กรอกชื่อ-นามสกุล" required>

          <label>ตำแหน่ง</label>
          <input type="text" name="position" placeholder="กรอกตำแหน่ง" required>

          <button type="submit" name="add">➕ เพิ่มรายชื่อ</button>
        </form>

        <!-- 📋 ตารางรายชื่อทั้งหมด -->
        <table>
          <tr>
            <th>ชื่อ-นามสกุล</th>
            <th>ตำแหน่ง</th>
            <th>จัดการ</th>
          </tr>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['full_name']) ?></td>
              <td><?= htmlspecialchars($row['position']) ?></td>
              <td class="actions">
                <a href="edit_person.php?id=<?= $row['id'] ?>">✏️ แก้ไข</a>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('คุณต้องการลบรายชื่อนี้จริงหรือไม่?')">🗑️ ลบ</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </table>

        <div class="section text-center mt-4">
          <a href="index.php" class="btn btn-primary">กลับไปหน้าแรก</a>
        </div>
      </main>
    </div>
  </div>
</body>
</html>
