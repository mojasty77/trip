<?php
// 🔗 เชื่อมกับฐานข้อมูล
require 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 📥 รับ trip_id จาก URL
$trip_id = $_GET['trip_id'] ?? '';
if (!$trip_id) die("ไม่พบ trip_id");

// 🔍 ดึงข้อมูลจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM trip_requests WHERE trip_id = ?");
$stmt->bind_param("s", $trip_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
if (!$data) die("ไม่พบข้อมูล");

// 🔄 แปลง JSON กลับเป็น array
$full_names = json_decode($data['full_names'], true);
$positions = json_decode($data['positions'], true);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขข้อมูลไปราชการ</title>
</head>
<body>
  <h2>✏️ แก้ไขข้อมูลไปราชการ</h2>
  <form method="post" action="update_trip.php">
    <!-- 🔑 ส่ง trip_id กลับไปเพื่ออ้างอิง -->
    <input type="hidden" name="trip_id" value="<?= htmlspecialchars($trip_id) ?>">

    <label>จำนวนผู้เดินทาง</label>
    <input type="number" name="person_count" value="<?= count($full_names) ?>" required>

    <?php for ($i = 0; $i < count($full_names); $i++): ?>
      <label>ชื่อ-นามสกุล <?= $i + 1 ?></label>
      <input type="text" name="full_name_<?= $i + 1 ?>" value="<?= htmlspecialchars($full_names[$i]) ?>" required>
      <label>ตำแหน่ง</label>
      <input type="text" name="position_<?= $i + 1 ?>" value="<?= htmlspecialchars($positions[$i]) ?>" required>
    <?php endfor; ?>

    <label>วัตถุประสงค์</label>
    <textarea name="purpose" required><?= htmlspecialchars($data['purpose']) ?></textarea>

    <label>วันเวลาเริ่มต้น</label>
    <input type="datetime-local" name="start_datetime" value="<?= date('Y-m-d\TH:i', strtotime($data['start_datetime'])) ?>" required>

    <label>วันเวลาสิ้นสุด</label>
    <input type="datetime-local" name="end_datetime" value="<?= date('Y-m-d\TH:i', strtotime($data['end_datetime'])) ?>" required>

    <label>พาหนะ</label>
    <input type="text" name="transport" value="<?= htmlspecialchars($data['transport']) ?>" required>

    <label>งบประมาณหมวด</label>
    <input type="text" name="budget_type" value="<?= htmlspecialchars($data['budget_type']) ?>">

    <label>โครงการ</label>
    <input type="text" name="project" value="<?= htmlspecialchars($data['project']) ?>">

    <label>ค่าใช้จ่ายโดยประมาณ</label>
    <input type="number" step="0.01" name="estimated_cost" value="<?= $data['estimated_cost'] ?>">

    <button type="submit">💾 บันทึกการแก้ไข</button>
  </form>
</body>
</html>
