
<?php
// Bài 2.1: Biến và kiểu dữ liệu trong PHP

$x = "5";
$y = 5;

// Hiển thị giá trị và kiểu dữ liệu
echo "<h3>2.1 Biến và kiểu dữ liệu trong PHP</h3>";

echo "<b>Giá trị của \$x:</b> ";
var_dump($x);

echo "<br><b>Giá trị của \$y:</b> ";
var_dump($y);

// Phép cộng
$tong = $x + $y;

echo "<br><br><b>Kết quả \$x + \$y:</b> ";
echo $tong;

// Kiểm tra kiểu dữ liệu
echo "<br><br><b>Kiểu dữ liệu của \$x:</b> ";
echo gettype($x);

echo "<br><b>Kiểu dữ liệu của \$y:</b> ";
echo gettype($y);

echo "<br><b>Kiểu dữ liệu của tổng:</b> ";
echo gettype($tong);
?>