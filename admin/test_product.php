<?php
$file = 'uploads/test.txt';
if(file_put_contents($file, "ทดสอบเขียนไฟล์") !== false){
    echo "เขียนไฟล์สำเร็จ";
    unlink($file); // ลบไฟล์ทดสอบ
} else {
    echo "เขียนไฟล์ไม่สำเร็จ";
}
