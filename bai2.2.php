
<?php
// Bài 2.2: Phân biệt == và ===

// Khai báo các biến
$a = "0";
$b = 0;
$c = false;

// Thực hiện so sánh
$kq1 = ($a == $b);
$kq2 = ($a === $b);
$kq3 = ($c == $b);

echo "<h2>2.2 - So sánh == và === trong PHP</h2>";

echo "<h3>1. Giá trị và kiểu dữ liệu</h3>";

echo "\$a = ";
var_dump($a);

echo "\$b = ";
var_dump($b);

echo "\$c = ";
var_dump($c);

echo "<hr>";

echo "<h3>2. Kết quả so sánh</h3>";

echo "'0' == 0: ";
var_dump($kq1);

echo "<br>'0' === 0: ";
var_dump($kq2);

echo "<br>false == 0: ";
var_dump($kq3);

echo "<hr>";

echo "<h3>3. Giải thích</h3>";

echo "<p><b>==</b>: So sánh giá trị, PHP có thể chuyển đổi kiểu dữ liệu.</p>";

echo "<p><b>===</b>: So sánh cả giá trị và kiểu dữ liệu, không tự chuyển kiểu.</p>";
?>
