<?php
// 🔗 เชื่อมกับฐานข้อมูล
require 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 📆 ฟังก์ชันแปลงวันที่เป็นแบบไทย
function formatThaiDate($datetime) {
  $months = [
    "", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
    "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
  ];
  $timestamp = strtotime($datetime);
  $day = date("j", $timestamp);
  $month = $months[(int)date("n", $timestamp)];
  $year = date("Y", $timestamp) + 543;
  return "$day $month $year";
}

// ✅ แก้ฟังก์ชัน bahtText ให้ไม่ซ้อนกัน
function convert($num, $txtnum1, $txtnum2) {
  $len = strlen($num);
  $res = "";
  for ($i = 0; $i < $len; $i++) {
    $n = (int)$num[$i];
    if ($n != 0) {
      if ($i == $len - 1 && $n == 1 && $len > 1) {
        $res .= "เอ็ด";
      } elseif ($i == $len - 2 && $n == 2) {
        $res .= "ยี่";
      } elseif ($i == $len - 2 && $n == 1) {
        $res .= "";
      } else {
        $res .= $txtnum1[$n];
      }
      $res .= $txtnum2[$len - $i - 1];
    }
  }
  return $res;
}

function bahtText($number) {
  $number = number_format($number, 2, ".", "");
  list($integer, $fraction) = explode(".", $number);

  $txtnum1 = ["", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า"];
  $txtnum2 = ["", "สิบ", "ร้อย", "พัน", "หมื่น", "แสน", "ล้าน"];
  $result = "";

  $result .= convert($integer, $txtnum1, $txtnum2);
  $result .= "บาท";

  if ((int)$fraction > 0) {
    $result .= convert($fraction, $txtnum1, $txtnum2) . "สตางค์";
  } else {
    $result .= "ถ้วน";
  }

  return $result;
}

// 📥 รับ trip_id ที่เลือกจาก dropdown หรือจากลิงก์
$selected_trip_id = $_GET['trip_id'] ?? '';

// 🔍 ดึงรายการ trip_id ทั้งหมดสำหรับ dropdown
$trip_list = $conn->query("SELECT trip_id FROM trip_requests ORDER BY trip_id DESC");

// 🔍 ถ้ามี trip_id ที่เลือก → ดึงข้อมูลเต็ม
$data = null;
$full_names = [];
$positions = [];
if ($selected_trip_id) {
  $stmt = $conn->prepare("SELECT * FROM trip_requests WHERE trip_id = ?");
  $stmt->bind_param("s", $selected_trip_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $data = $result->fetch_assoc();

  if ($data) {
    $full_names = json_decode($data['full_names'], true);
    $positions = json_decode($data['positions'], true);
    $formatted_date = formatThaiDate($data['start_datetime']);
  }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>บันทึกข้อความ</title>
  <style>
    /* 📄 กำหนดขนาดหน้ากระดาษเป็น A4 */
    body {
      width: 210mm;
      height: 297mm;
      margin-left: 2.0cm;
      padding-right: 1cm; /* ✅ เว้นระยะด้านขวา */
      margin-top: 0cm;
      font-family: "TH SarabunPSK", sans-serif;
      font-size: 18pt;
      line-height: 1.4;
      position: relative;
      /* border: 1px solid #000; /* ✅ เส้นขอบสีดำรอบกระดาษ */
    }

    /* 🖼️ โลโก้ราชการ */
    .logo {
      position: absolute;
      top: 0;
      left: 0;
      width: 2.0cm;
      height: 2.0cm;
    }

    /* 📝 หัวฟอร์ม */
    .header {
      text-align: center;
      font-size: 24pt;
      margin-top: 0.5cm;
      font-weight: bold;
      margin-bottom: -0.2cm;
    }

    /* 🏛️ ส่วนราชการ */
    .agency-block {
      font-size: 18pt;
     }
     
    /* ตัวบรรทัดภายในทั้งหมด */
    .text {
      font-size: 18pt;
    }

    /* 📋 กล่องข้อมูล */
    .section {
      margin-top: 1cm;
    }

    .section h3 {
      font-size: 18pt;
      margin-bottom: 0.5cm;
    }

    .person {
      margin-bottom: 0.3cm;
    }

    /* 🔍 dropdown เลือก trip_id */
    .lookup {
      margin-bottom: 1cm;
    }

    select {
      font-size: 18pt;
      padding: 1px;
      font-family: "TH SarabunPSK", sans-serif;
    }

    /* 📦 กล่องข้อมูลแบบห้ามแก้ไข */
    .readonly-box {
      background-color: #f9f9f9;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

      /* 🧾 ตารางรายชื่อบุคคล */
      .person-table {
        border-collapse: collapse;
        margin-top: -0.2cm;
        width: 100%;
      }

      .person-table td {
        border: none; /* ✅ ไม่มีขอบ ใส่ nonoe อยากได้ขอบใส่ 1px solid red*/ 
        padding: -1px 0px;
        font-size: 18pt;
      }

    /* 🖨️ ซ่อนบางส่วนตอนพิมพ์ */
    @media print {
      .no-print {
        display: none !important;
      }
    }
  </style>
</head>
<body>

  <!-- 🖼️ โลโก้ราชการ -->
  <img src="logo.png" alt="โลโก้" class="logo">

  <!-- 📝 หัวฟอร์ม -->
   <br>
  <div class="header">บันทึกข้อความ</div>
  
  <?php if ($data): ?>
    <!-- 🏛️ ส่วนราชการ -->
      <div class="agency-block">
      <span style="font-size: 20pt; font-weight: bold;">ส่วนราชการ:</span> สำนักงานส่งเสริมการเรียนรู้ประจำจังหวัดแม่ฮ่องสอน<br>
      <span style="font-size: 18pt; font-weight: bold;">ที่ ศธ.</span> 07074: <?= htmlspecialchars($selected_trip_id) ?>
      <span style="display:inline-block; width:9cm;"></span> <!-- ความยาวตัวสือ -->
      <span style="font-size: 18pt; font-weight: bold;">วันที่  </span><?= $formatted_date ?><br>
      <span style="font-size: 18pt; font-weight: bold;">เรื่อง :</span> ขออนุมัติไปราชการ
      <div style="height: 1px; background-color: #000; margin-top: -0.1cm; margin-bottom: 0.1cm;"></div> <!-- เส้นขีดยาวๆ -->
     </div>
    <?php endif; ?>
   <div class="select">
       <span style="font-size: 18pt; font-weight: bold;">เรียน:</span>  ผู้อำนวยการสำนักงานส่งเสริมการเรียนรู้ประจำจังหวัดแม่ฮ่องสอน<br>
  </div>

    <!-- 🧾 รายละะเอียดภายใน -->
    <div class="text">
      <span style="display:inline-block; width:1cm;"></span>
      ตามที่ข้าพเจ้า <?= htmlspecialchars($full_names[0]) ?> 
      ตำแหน่ง <?= htmlspecialchars($positions[0]) ?>
      ได้รับมอบหมายให้ไปราชการเพื่อปฏิบัติราชการ <?= nl2br(htmlspecialchars($data['purpose'])) ?> 
      ระหว่างวันที่ <?= formatThaiDate($data['start_datetime']) ?> ถึงวันที่ <?= formatThaiDate($data['end_datetime']) ?>
  

      <!--  เรื่องของการรวมเวลาเฉยๆ -->
     <?php
      $days = $data['duration_days'] ?? 0;
      $hours = $data['duration_hours'] ?? 0;
      if ($days > 0 || $hours > 0):
    ?>
      <strong>รวมแล้วเป็นเวลา:</strong> <?= $days ?> วัน <?= $hours ?> ชั่วโมง<br>
    <?php else: ?>
      <strong>รวมแล้วเป็นเวลา:</strong> ไม่สามารถคำนวณได้<br>
    <?php endif; ?>
         
    </div>
    
              <!-- ✅ Checkbox: มีผู้รักษาการแทนหรือไม่ -->
          <div class="no-print">
            <label>
              <input type="checkbox" id="has_acting" onchange="toggleActing()"> มีผู้รักษาการแทน (กรณีผู้อำนวยการ)
            </label>

            <!-- ✅ Dropdown: เลือกชื่อ -->
            <select id="acting_person" onchange="updateActingDisplay()" style="display:none;">
              <option value="">-- กรุณาเลือก --</option>
              <?php foreach ($full_names as $index => $name): ?>
                <option value="<?= htmlspecialchars($name) ?>">
                  <?= htmlspecialchars($name) ?> (<?= htmlspecialchars($positions[$index] ?? '') ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- ✅ ข้อความแสดงผล -->
          <div id="acting_text"></div>

          <script>
          function toggleActing() {
            const checkbox = document.getElementById('has_acting');
            const dropdown = document.getElementById('acting_person');
            const text = document.getElementById('acting_text');

            if (checkbox.checked) {
              dropdown.style.display = 'inline';
            } else {
              dropdown.style.display = 'none';
              text.innerHTML = ''; // ✅ ลบข้อความถ้าไม่เลือก
            }
          }
            // ตัวหนังสือรัวๆ
          function updateActingDisplay() {
            const name = document.getElementById('acting_person').value;
            const text = document.getElementById('acting_text');
            if (name) {
              text.innerHTML = `ระหว่างการไปราชการครั้งนี้ ขอมอบหมายให้ <strong>${name}</strong> รักษาการแทน (กรณีผู้อำนวยการ)`;
            } else {
              text.innerHTML = '';
            }
          }
          </script>
    
    <!--  อ้างระเบียบทั่วไป  -->
    <div class="text">
      <span style="display:inline-block; width:1cm;"></span> การอนุมัติให้ไปราชการครั้งนี้เป็นอำนาจของผู้อำนวยการสำนักงานส่งเสริมการเรียนรู้จังหวัด  ตามคำสั่งกรมส่งเสริมการเรียนรู้ ที่ 3/2566  เรื่อง  มอบอำนาจให้ผู้อำนวยการสำนักงานส่งเสริมการเรียนรู้จังหวัด และผู้อำนวยการสำนักงานส่งเสริมการเรียนรู้กรุงเทพมหานคร ปฏิบัติราชการแทน ลงวันที่ 29 พฤษภาคม 2566 และคำสั่งกรมส่งเสริมการเรียนรู้ ที่ 87/2566  เรื่อง  แก้ไขเปลี่ยนแปลงคำสั่ง  ลงวันที่ 13 มิถุนายน 2566    

      
    <!--  ต่อบรรทัดต่อไปเพิ่มคน และลำดับ ตำแหน่ง ไอ่สัสยากอีกละ  -->
          <div class="text">
            <span style="display:inline-block; width:1cm;"></span> 
            <span style="line-height: 1;">
              ในการเดินทางไปราชการครั้งนี้มีเจ้าหน้าที่ที่ไปปฏิบัติงาน จำนวน <?= $data['person_count'] ?> คนดังนี้
            </span>
            <table class="person-table">
              <?php foreach ($full_names as $index => $name): ?> 
                <tr>
                  <td style="width:1cm;"></td> <!-- ✅ ย่อหน้า -->
                  <td style="width:0.3cm;"><?= $index + 1 ?>.</td>
                  <td style="width:6cm;"><?= htmlspecialchars($name) ?></td> 
                  <td>ตำแหน่ง <?= htmlspecialchars($positions[$index] ?? '') ?></td>
                </tr>
              <?php endforeach; ?>
            </table>
          </div>


  <!--  เรียกใช้พาหนะจากในระบบ  -->  
      <span style="display:inline-block; width:1cm;"></span>   
      ขออนุมัติเดินทางโดยพาหนะ <?= htmlspecialchars($data['transport']) ?>
      <?php if ($data['budget_type']): ?>
        งบประมาณหมวด <?= htmlspecialchars($data['budget_type']) ?>
      <?php endif; ?>
      <?php if ($data['project']): ?>
        แผนกิจกรรม/โครงการ <?= htmlspecialchars($data['project']) ?> 
      <?php endif; ?> 
      <br>
        <?php if ($data['estimated_cost']): ?>
        ในการไปราชการในครั้งนี้ ขอประมาณการค่าใช้จ่ายเงิน จำนวน <?= number_format($data['estimated_cost'], 2) ?> บาท
        (<?= bahtText($data['estimated_cost']) ?>)<br>
        ขอยืมเงินทดรองราชการ / เงินยืมราชการ จำนวน <?= number_format($data['estimated_cost'], 2) ?> บาท
        (<?= bahtText($data['estimated_cost']) ?>)
      <?php endif; ?>

      <br>

          

      <!--  มาอีกแล้วครับท่านนนนนนนนนนน ลงชื่อท้าย  -->
  <span style="display:inline-block; width:1cm;"></span>   
      จึงเรียนมาเพื่อโปรดพิจารณาอนุมัติ
          <br><br><br>

          <div style="text-align: right; margin-right: 8cm;">ลงชื่อ<br>
          </div>

          <div style="text-align: right; margin-right: 2.5cm;">
            (<?= htmlspecialchars($full_names[0]) ?>)
          </div>

          <div style="text-align: right; margin-right: 2cm;">
            ตำแหน่ง <?= htmlspecialchars($positions[0]) ?>
          </div>


  <!-- 🔍 dropdown เลือก trip_id (ซ่อนตอนพิมพ์) -->
  <div class="no-print lookup"> <!-- (ซ่อนตอนพิมพ์) -->
    <form method="get">
      <label for="trip_id">เลือกเลขบันทึกข้อความ:</label>
      <select name="trip_id" id="trip_id" onchange="this.form.submit()">
        <option value="">-- กรุณาเลือก --</option>
        <?php while ($row = $trip_list->fetch_assoc()): ?>
          <option value="<?= $row['trip_id'] ?>" <?= ($row['trip_id'] === $selected_trip_id) ? 'selected' : '' ?>>
            <?= $row['trip_id'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </form>
  </div>

  <!-- 🔙 ปุ่มกลับหน้าแรก (ซ่อนตอนพิมพ์) -->
  <div class="no-print section" style="text-align:center;">
    <a href="index.php" style="padding: 8px 16px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 4px;">
      กลับไปหน้าแรก
    </a>
  </div>

</body>
</html>
    