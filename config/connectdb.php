<?php
// config/connectdb.php (Corrected & Final Version)

// ⭐️ สำคัญ: ต้องไม่มีช่องว่าง, บรรทัดว่าง, หรือข้อความใดๆ อยู่ก่อนบรรทัดนี้

$servername = "localhost"; // หรือ IP ของ Server ฐานข้อมูล
$username   = "root";      // ชื่อผู้ใช้ฐานข้อมูล
$password   = "Msu123mbs";          // รหัสผ่านฐานข้อมูล
$dbname     = "myproject"; // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตั้งค่า character set เป็น utf8mb4 เพื่อรองรับภาษาไทยสมบูรณ์แบบ
$conn->set_charset("utf8mb4");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
  // หากเชื่อมต่อไม่สำเร็จ ให้หยุดการทำงานและแสดงข้อผิดพลาด
  die("Connection failed: " . $conn->connect_error);
}
