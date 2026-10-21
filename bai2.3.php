
<?php
// Bài 2.3: Điều kiện và vòng lặp

$diem = "";
$ketQua = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diem = $_POST["diem"] ?? "";

    // Kiểm tra dữ liệu đầu vào
    if ($diem === "" || !is_numeric($diem)
        || $diem < 0 || $diem > 10) {
        $ketQua = "Điểm không hợp lệ! Vui lòng nhập điểm từ 0 đến 10.";
    } else {
        // Xếp loại từ cao xuống thấp
        if ($diem >= 8) {
            $xepLoai = "Giỏi";
        } elseif ($diem >= 6.5) {
            $xepLoai = "Khá";
        } elseif ($diem >= 5) {
            $xepLoai = "Trung bình";
        } else {
            $xepLoai = "Chưa đạt";
        }

        $ketQua = "Điểm của bạn: $diem - Xếp loại: $xepLoai";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bài 2.3 - Điều kiện và vòng lặp</title>
</head>
<body>
    <h2>Bài 2.3: Điều kiện và vòng lặp trong PHP</h2>

    <form method="POST">
        <label>Nhập điểm (0 - 10): </label>
        <input
            type="number"
            name="diem"
            min="0"
            max="10"
            step="any"
            value="<?= htmlspecialchars((string)$diem) ?>"
            required
        >
        <button type="submit">Xếp loại</button>
    </form>

    <p>
        <strong>Kết quả:</strong>
        <?= htmlspecialchars($ketQua) ?>
    </p>
</body>
</html>