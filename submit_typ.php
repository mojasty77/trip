<?php
require 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ดึงรายชื่อจากฐานข้อมูล
$people_result = $conn->query("SELECT full_name, position FROM people ORDER BY full_name ASC");
$people_data = [];
while ($row = $people_result->fetch_assoc()) {
  $people_data[$row['full_name']] = $row['position'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ฟอร์มบันทึกไปราชการ</title>
  <!-- Bootstrap 5 -->
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
      text-align: center;
      margin-bottom: 30px;
      font-size: 22pt;
    }
    label {
      font-weight: bold;
    }
    #duration_result {
      margin-top: 10px;
      font-weight: bold;
      color: #333;
    }
    #trip_id_warning {
      font-weight: bold;
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
        <a href="manage_people.php">✒ จัดการรายชื่อบุคคล</a>
        <a href="list_trip.php">📑 แก้ไข/ปริ้น</a>
      </nav>

      <!-- ฟอร์มด้านขวา -->
      <main class="col-md-9 col-lg-10 form-area">
        <h2>แบบฟอร์มบันทึกไปราชการ</h2>
        <form method="post" action="submit_trip.php">
          <div class="mb-3">
            <label for="trip_id" class="form-label">เลขบันทึกข้อความ (Primary Key)</label>
            <input type="text" class="form-control" id="trip_id" name="trip_id" required>
            <div id="trip_id_warning" class="form-text mt-1"></div>
          </div>

          <div class="mb-3">
            <label for="person_count" class="form-label">จำนวนผู้เดินทาง (รวมตัวเองด้วย)</label>
            <input type="number" class="form-control" id="person_count" name="person_count" min="1" max="10" value="1" onchange="renderTravelers()">
          </div>

          <div id="travelers"></div>

          <div class="mb-3">
            <label for="purpose" class="form-label">ความประสงค์ที่จะขออนุมัติไปราชการเกี่ยวกับ</label>
            <textarea class="form-control" id="purpose" name="purpose" rows="3" required></textarea>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">ตั้งแต่วันที่</label>
              <input type="date" class="form-control" id="start_date" name="start_date" onchange="calculateDuration()">
            </div>
            <div class="col-md-6">
              <label class="form-label">เวลา</label>
              <input type="time" class="form-control" id="start_time" name="start_time" onchange="calculateDuration()">
            </div>
            <div class="col-md-6">
              <label class="form-label">ถึงวันที่</label>
              <input type="date" class="form-control" id="end_date" name="end_date" onchange="calculateDuration()">
            </div>
            <div class="col-md-6">
              <label class="form-label">เวลา</label>
              <input type="time" class="form-control" id="end_time" name="end_time" onchange="calculateDuration()">
            </div>
            <div id="duration_result" class="form-text mt-2 ms-2"></div>
          </div>

          <div class="mb-3">
            <label for="transport" class="form-label">ขออนุมัติเดินทางโดยพาหนะ</label>
            <select class="form-select" id="transport" name="transport" required>
              <option value="">-- เลือกพาหนะ --</option>
              <option value="รถยนต์ราชการ">รถยนต์ราชการ</option>
              <option value="รถยนต์ส่วนตัว">รถยนต์ส่วนตัว</option>
              <option value="รถโดยสารประจำทาง">รถโดยสารประจำทาง</option>
              <option value="รถรับจ้าง">รถรับจ้าง</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="budget_type" class="form-label">งบประมาณหมวด</label>
            <input type="text" class="form-control" id="budget_type" name="budget_type">
          </div>

          <div class="mb-3">
            <label for="project" class="form-label">แผน/โครงการ</label>
            <input type="text" class="form-control" id="project" name="project">
          </div>

          <div class="mb-3">
            <label for="estimated_cost" class="form-label">ขอประมาณการค่าใช้จ่ายเป็นเงิน จำนวน</label>
            <input type="text" class="form-control" id="estimated_cost" name="estimated_cost">
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">💾 บันทึกข้อมูล</button>
          </div>
        </form>
      </main>
    </div>
  </div>

  <script>
    const peopleData = <?= json_encode($people_data, JSON_UNESCAPED_UNICODE) ?>;

    function renderTravelers() {
      const count = parseInt(document.getElementById("person_count").value);
      const container = document.getElementById("travelers");
      container.innerHTML = "";

      for (let i = 1; i <= count; i++) {
        const div = document.createElement("div");
        div.className = "mb-3";
        div.innerHTML = `
          <label class="form-label">ชื่อ-นามสกุลผู้เดินทาง ${i}</label>
          <select class="form-select" onchange="updatePosition(this, ${i})" name="full_name_${i}" required>
            <option value="">-- เลือกชื่อจากระบบ --</option>
            ${Object.keys(peopleData).map(name => `<option value="${name}">${name}</option>`).join("")}
          </select>
          <label class="form-label mt-2">ตำแหน่ง</label>
          <input type="text" class="form-control" id="position_${i}" name="position_${i}" readonly>
        `;
        container.appendChild(div);
      }
    }

    function updatePosition(select, index) {
      const position = peopleData[select.value] || "";
      document.getElementById(`position_${index}`).value = position;
    }

    function calculateDuration() {
      const sd = new Date(document.getElementById("start_date").value + "T" + document.getElementById("start_time").value);
      const ed = new Date(document.getElementById("end_date").value + "T" + document.getElementById("end_time").value);
      if (isNaN(sd) || isNaN(ed)) return;

      const diffMs = ed - sd;
      const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
      const days = Math.floor(diffHrs / 24);
      const hours = diffHrs % 24;
      const hours = diffHrs % 24;
      document.getElementById("duration_result").innerText = `รวม ${days} วัน ${hours} ชั่วโมง`;
    }

    renderTravelers(); // initial render

    // ตรวจสอบความซ้ำซ้อนของ trip_id
    document.getElementById("trip_id").addEventListener("blur", function() {
      const tripId = this.value.trim();
      if (!tripId) return;

      fetch("check_trip_id.php?trip_id=" + encodeURIComponent(tripId))
        .then(response => response.json())
        .then(data => {
          const warning = document.getElementById("trip_id_warning");
          if (data.status === "exists") {
            warning.textContent = "❌ trip_id นี้มีอยู่แล้วในระบบ!";
            warning.style.color = "red";
            document.querySelector("button[type='submit']").disabled = true;
          } else if (data.status === "ok") {
            warning.textContent = "✅ trip_id นี้สามารถใช้ได้";
            warning.style.color = "green";
            document.querySelector("button[type='submit']").disabled = false;
          } else {
            warning.textContent = "⚠️ ไม่สามารถตรวจสอบ trip_id ได้";
            warning.style.color = "orange";
          }
        })
        .catch(err => {
          console.error("เกิดข้อผิดพลาดในการตรวจสอบ trip_id", err);
        });
    });
  </script>
</body>
</html>

