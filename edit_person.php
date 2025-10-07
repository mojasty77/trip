<?php
require 'config.php';
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die("ไม่พบข้อมูล");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['full_name'] ?? '';
  $position = $_POST['position'] ?? '';
  $stmt = $conn->prepare("UPDATE people SET full_name = ?, position = ? WHERE id = ?");
  $stmt->bind_param("ssi", $name, $position, $id);
  $stmt->execute();
  header("Location: manage_people.php");
  exit;
}

$result = $conn->query("SELECT * FROM people WHERE id = $id");
$data = $result->fetch_assoc();
?>

<form method="post">
  <input type="text" name="full_name" value="<?= htmlspecialchars($data['full_name']) ?>" required>
  <input type="text" name="position" value="<?= htmlspecialchars($data['position']) ?>" required>
  <button type="submit">บันทึกการแก้ไข</button>
</form>
