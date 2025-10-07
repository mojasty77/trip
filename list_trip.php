<?php
require 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'trip_id';
$order = $_GET['order'] ?? 'DESC';
$limit = $_GET['limit'] ?? 10;

function formatThaiDate($datetime) {
  $months = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
             "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
  $timestamp = strtotime($datetime);
  $day = date("j", $timestamp);
  $month = $months[(int)date("n", $timestamp)];
  $year = date("Y", $timestamp) + 543;
  return "$day $month $year";
}

$sql = "SELECT * FROM trip_requests 
        WHERE trip_id LIKE ? OR purpose LIKE ? OR transport LIKE ?
        ORDER BY $sort $order
        LIMIT ?";

$stmt = $conn->prepare($sql);
$search_term = "%$search%";
$stmt->bind_param("sssi", $search_term, $search_term, $search_term, $limit);
$stmt->execute();
$result = $stmt->get_result();

function sortLink($label, $column, $currentSort, $currentOrder, $search, $limit) {
  $nextOrder = ($currentSort === $column && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
  return "<a href=\"?search=" . urlencode($search) . "&sort=$column&order=$nextOrder&limit=$limit\">$label</a>";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>รายการย้อนหลัง</title>
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
      margin-bottom: 30px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 10px 12px;
      vertical-align: top;
    }
    th {
      background-color: #e6f0ff;
      font-weight: bold;
      border-bottom: 1px solid #ccc;
    }
    td {
      border-bottom: 1px solid #eee;
    }
    .actions a {
      margin-right: 8px;
      text-decoration: none;
      color: #007BFF;
      padding: 4px 8px;
      border-radius: 6px;
      background-color: #eaf4ff;
      transition: background-color 0.2s;
    }
    .actions a:hover {
      background-color: #d0e7ff;
    }
    form {
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }
    input[type="text"], select {
      padding: 6px 10px;
      font-size: 16pt;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: #fff;
    }
    button {
      padding: 6px 12px;
      font-size: 16pt;
      border: none;
      border-radius: 6px;
      background-color: #007BFF;
      color: white;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    button:hover {
      background-color: #0056b3;
    }
    th:nth-child(3), td:nth-child(3) {
      width: 30%;
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
        <a href="submit_typ.php">📝 บันทึกไปราชการ</a>
        <a href="manage_people.php">👥 จัดการรายชื่อบุคคล</a>
      </nav>

      <!-- เนื้อหาด้านขวา -->
      <main class="col-md-9 col-lg-10 form-area">
        <h2>📋 รายการคำขอไปราชการย้อนหลัง</h2>

        <!-- 🔍 ฟอร์มค้นหา -->
        <form method="get">
          <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="ค้นหา trip_id, วัตถุประสงค์ หรือพาหนะ">
          <select name="limit" onchange="this.form.submit()">
            <?php foreach ([10, 20, 50, 100, 100000] as $opt): ?>
              <option value="<?= $opt ?>" <?= ($limit == $opt) ? 'selected' : '' ?>>
                <?= $opt == 100000 ? 'ทั้งหมด' : $opt ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit">🔍 ค้นหา</button>
        </form>

        <!-- 🔁 ตารางรายการ -->
        <table class="table table-bordered">
          <thead>
            <tr>
              <th><?= sortLink("ID", "trip_id", $sort, $order, $search, $limit) ?></th>
              <th><?= sortLink("คน", "person_count", $sort, $order, $search, $limit) ?></th>
              <th><?= sortLink("วัตถุประสงค์", "purpose", $sort, $order, $search, $limit) ?></th>
              <th><?= sortLink("วันเวลาเริ่มต้น", "start_datetime", $sort, $order, $search, $limit) ?></th>
              <th><?= sortLink("วันเวลาสิ้นสุด", "end_datetime", $sort, $order, $search, $limit) ?></th>
              <th><?= sortLink("พาหนะ", "transport", $sort, $order, $search, $limit) ?></th>
              <th><?= sortLink("งบประมาณ", "estimated_cost", $sort, $order, $search, $limit) ?></th>
              <th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['trip_id']) ?></td>
                <td><?= $row['person_count'] ?></td>
                <td><?= htmlspecialchars($row['purpose']) ?></td>
                <td><?= formatThaiDate($row['start_datetime']) ?></td>
                <td><?= formatThaiDate($row['end_datetime']) ?></td>
                <td><?= htmlspecialchars($row['transport']) ?></td>
                <td><?= number_format($row['estimated_cost'], 2) ?> บาท</td>
                <td class="actions">
                    <a href="edit_trip.php?trip_id=<?= urlencode($row['trip_id']) ?>">✏️</a>
                    <a href="delete_trip.php?trip_id=<?= urlencode($row['trip_id']) ?>" onclick="return confirm('คุณต้องการลบรายการนี้จริงหรือไม่?')">🗑️</a>
                    <a href="form_a4.php?trip_id=<?= urlencode($row['trip_id']) ?>">🖨️</a>
                  </td>

              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <!-- 🔙 ปุ่มกลับหน้าแรก -->
        <div class="text-center mt-4">
          <a href="index.php" class="btn btn-primary">กลับไปหน้าแรก</a>
        </div>
      </main>
    </div>
  </div>
</body>
</html>

