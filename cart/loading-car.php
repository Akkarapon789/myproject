<?php
// loading-car.php
session_start();
$order_id = $_GET['order_id'] ?? 0; // รับค่า order_id มาจาก place_order.php
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>กำลังดำเนินการสั่งซื้อ...</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f0f2f5;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
    }
    .loading-text {
      font-size: 1.5rem;
      color: #2155CD;
      margin-bottom: 30px;
      animation: blink 1s infinite;
    }
    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.3; }
    }

    /* ถนน */
    .road {
      position: relative;
      width: 80%;
      height: 100px;
      background: #333;
      border-radius: 10px;
      overflow: hidden;
    }
    .line {
      position: absolute;
      top: 50%;
      width: 100%;
      height: 6px;
      background: repeating-linear-gradient(
        to right,
        #fff 0,
        #fff 60px,
        transparent 60px,
        transparent 120px
      );
      transform: translateY(-50%);
    }

    /* รถ */
    .car {
      position: absolute;
      bottom: 10px;
      left: 0;
      width: 80px;
      transition: left 0.1s linear;
    }

    /* Progress Bar */
    .progress-container {
      margin-top: 30px;
      width: 80%;
      background: #e9ecef;
      border-radius: 20px;
      overflow: hidden;
      height: 25px;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
    }
    .progress-bar {
      height: 100%;
      width: 0;
      background: linear-gradient(90deg, #2155CD, #4da3ff);
      color: #fff;
      text-align: center;
      line-height: 25px;
      font-weight: bold;
      transition: width 0.1s linear;
    }
  </style>
</head>
<body>

  <div class="loading-text">
    กำลังดำเนินการสั่งซื้อ... เลขคำสั่งซื้อ #<?= htmlspecialchars($order_id) ?>
  </div>
  
  <!-- ถนน -->
  <div class="road">
    <div class="line"></div>
    <img src="../assets/car/car5.svg" alt="Car" class="car" id="car">
  </div>

  <!-- Progress -->
  <div class="progress-container">
    <div class="progress-bar" id="progressBar">0%</div>
  </div>

  <script>
    let progress = 0;
    const car = document.getElementById("car");
    const progressBar = document.getElementById("progressBar");
    const road = document.querySelector(".road");

    const roadWidth = road.offsetWidth - car.offsetWidth;

    const interval = setInterval(() => {
      progress += 2; // เพิ่มทีละ 2%
      if (progress > 100) progress = 100;

      // ขยับรถ
      const carPosition = (roadWidth * progress) / 100;
      car.style.left = carPosition + "px";

      // อัพเดท progress bar
      progressBar.style.width = progress + "%";
      progressBar.textContent = progress + "%";

      if (progress >= 100) {
        clearInterval(interval);
        setTimeout(() => {
          window.location.href = "success.php?order_id=<?= $order_id ?>";
        }, 500); // รออีกนิดก่อน redirect
      }
    }, 80); // 80ms ต่อรอบ ~8s ถึง 100%
  </script>

</body>
</html>