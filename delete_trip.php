<?php
// 🔗 เชื่อมกับฐานข้อมูล
require 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 📥 รับ trip_id จาก URL
$trip_id = $_GET['trip_id'] ?? '';
if (!$trip_id) die("ไม่พบ trip_id");

// 🗑️ ลบข้อมูลจากฐาน
$stmt = $conn->prepare("DELETE FROM trip_requests WHERE trip_id = ?");
$stmt->bind_param("s", $trip_id);
if ($stmt->execute()) {
  echo "✅ ลบข้อมูลเรียบร้อยแล้ว";
} else {
  echo "❌ เกิดข้อผิดพลาด: " . $stmt->error;
}

// 🔁 กลับไปหน้ารายการย้อนหลัง
header("Location: list_trip.php");
exit;
