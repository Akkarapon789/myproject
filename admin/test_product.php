<?php
if(!is_dir('uploads')){
    mkdir('uploads', 0755);
    echo "สร้างโฟลเดอร์ uploads สำเร็จ";
} else {
    echo "โฟลเดอร์ uploads มีอยู่แล้ว";
}
