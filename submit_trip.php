<?php
require 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 🔧 ฟังก์ชันรวมวันที่และเวลาเป็น datetime
function toDatetime($date, $time) {
  return date("Y-m-d H:i:s", strtotime("$date $time"));
}

// 📥 รับค่าจากฟอร์ม
$trip_id = $_POST['trip_id'] ?? '';
$person_count = intval($_POST['person_count'] ?? 0);
$full_names = [];
$positions = [];

for ($i = 1; $i <= $person_count; $i++) {
  $full_names[] = $_POST["full_name_{$i}"] ?? '';
  $positions[] = $_POST["position_{$i}"] ?? '';
}

// ✅ ตรวจ trip_id ซ้ำ
$check = $conn->prepare("SELECT COUNT(*) FROM trip_requests WHERE trip_id = ?");
$check->bind_param("s", $trip_id);
$check->execute();
$check->bind_result($count);
$check->fetch();
$check->close();

if ($count > 0) {
  echo "❌ trip_id นี้มีอยู่แล้วในระบบ!";
  exit;
}

// 📦 รับค่าฟอร์มเพิ่มเติม
$purpose = $_POST['purpose'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$transport = $_POST['transport'] ?? '';
$budget_type = $_POST['budget_type'] ?? '';
$project = $_POST['project'] ?? '';
$estimated_cost = floatval($_POST['estimated_cost'] ?? 0);

// 🧮 คำนวณระยะเวลา
$start_datetime = toDatetime($start_date, $start_time);
$end_datetime = toDatetime($end_date, $end_time);
$diff = strtotime($end_datetime) - strtotime($start_datetime);
$duration_days = floor($diff / (60 * 60 * 24));
$duration_hours = floor(($diff % (60 * 60 * 24)) / (60 * 60));

// ✅ รองรับค่าจาก JavaScript (ถ้ามี)
$duration_days = intval($_POST['duration_days'] ?? $duration_days);
$duration_hours = intval($_POST['duration_hours'] ?? $duration_hours);

// 📝 เตรียม SQL
$sql = "INSERT INTO trip_requests (
  trip_id, person_count, full_names, positions, purpose,
  start_datetime, end_datetime, duration_days, duration_hours,
  transport, budget_type, project, estimated_cost
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  die("❌ Prepare failed: " . $conn->error);
}

// 🔧 เข้ารหัสชื่อและตำแหน่งเป็น JSON
$json_full_names = json_encode($full_names, JSON_UNESCAPED_UNICODE);
$json_positions = json_encode($positions, JSON_UNESCAPED_UNICODE);

// 🔗 bind ตัวแปร
$stmt->bind_param(
  "sissssssisssd",
  $trip_id,
  $person_count,
  $json_full_names,
  $json_positions,
  $purpose,
  $start_datetime,
  $end_datetime,
  $duration_days,
  $duration_hours,
  $transport,
  $budget_type,
  $project,
  $estimated_cost
);

// ✅ บันทึกข้อมูล
if ($stmt->execute()) {
  echo "✅ บันทึกข้อมูลเรียบร้อยแล้ว กำลังกลับไปหน้าแรก";
  echo "<meta http-equiv='refresh' content='2;url=index.php'>";

  // 🧾 บันทึกผู้เดินทางลงตาราง travelers
  $trav_stmt = $conn->prepare("INSERT INTO travelers (trip_id, traveler_index, full_name, position) VALUES (?, ?, ?, ?)");
  for ($i = 1; $i <= $person_count; $i++) {
    $name = $full_names[$i - 1] ?? '';
    $pos = $positions[$i - 1] ?? '';
    if ($name === '') continue;
    $trav_stmt->bind_param("siss", $trip_id, $i, $name, $pos);
    $trav_stmt->execute();
  }
  $trav_stmt->close();
} else {
  echo "❌ เกิดข้อผิดพลาด: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
