
<?php
$ketQua = "";
$diem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diem = $_POST["diem"] ?? "";

    if ($diem !== "" && is_numeric($diem)
        && $diem >= 0 && $diem <= 10) {
        $ketQua = "Điểm hợp lệ!";
    } else {
        $ketQua = "Điểm không hợp lệ. Vui lòng nhập số từ 0 đến 10.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kiểm tra điểm hợp lệ</title>
</head>
<body>
    <h2>Kiểm tra điểm hợp lệ</h2>

    <form method="POST">
        <label>Nhập điểm từ 0 đến 10:</label>
        <input type="number" name="diem"
               min="0" max="10" step="any"
               value="<?= htmlspecialchars((string)$diem) ?>"
               required>
        <button type="submit">Kiểm tra</button>
    </form>

    <p><?= htmlspecialchars($ketQua) ?></p>
</body>
</html>
