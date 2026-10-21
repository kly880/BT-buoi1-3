
<?php
// Bài 2.5: Hàm - Chia bài toán thành bước nhỏ

// Hàm tính điểm tổng kết
function finalScore(float $cc, float $mid, float $final): float
{
    return $cc * 0.10 + $mid * 0.30 + $final * 0.60;
}

$ketQua = "";
$cc = "";
$mid = "";
$final = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cc = $_POST["cc"] ?? "";
    $mid = $_POST["mid"] ?? "";
    $final = $_POST["final"] ?? "";

    if (
        $cc === "" || $mid === "" || $final === "" ||
        !is_numeric($cc) || !is_numeric($mid) || !is_numeric($final) ||
        $cc < 0 || $cc > 10 ||
        $mid < 0 || $mid > 10 ||
        $final < 0 || $final > 10
    ) {
        $ketQua = "Vui lòng nhập đủ 3 điểm hợp lệ từ 0 đến 10.";
    } else {
        $tongKet = finalScore((float)$cc, (float)$mid, (float)$final);
        $ketQua = "Điểm tổng kết: " . number_format($tongKet, 2);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bài 2.5 - Hàm tính điểm tổng kết</title>
</head>
<body>
    <h2>Bài 2.5: Hàm tính điểm tổng kết</h2>

    <form method="POST">
        <p>
            <label>Điểm chuyên cần (10%):</label>
            <input type="number" name="cc" min="0" max="10"
                   step="any" required
                   value="<?= htmlspecialchars((string)$cc) ?>">
        </p>

        <p>
            <label>Điểm giữa kỳ (30%):</label>
            <input type="number" name="mid" min="0" max="10"
                   step="any" required
                   value="<?= htmlspecialchars((string)$mid) ?>">
        </p>

        <p>
            <label>Điểm cuối kỳ (60%):</label>
            <input type="number" name="final" min="0" max="10"
                   step="any" required
                   value="<?= htmlspecialchars((string)$final) ?>">
        </p>

        <button type="submit">Tính điểm tổng kết</button>
    </form>

    <h3><?= htmlspecialchars($ketQua) ?></h3>
</body>
</html>