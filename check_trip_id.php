<?php
require 'config.php';
$trip_id = $_GET['trip_id'] ?? '';
if (!$trip_id) {
  echo json_encode(['status' => 'error', 'message' => 'ไม่พบ trip_id']);
  exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM trip_requests WHERE trip_id = ?");
$stmt->bind_param("s", $trip_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
  echo json_encode(['status' => 'exists']);
} else {
  echo json_encode(['status' => 'ok']);
}
?>
