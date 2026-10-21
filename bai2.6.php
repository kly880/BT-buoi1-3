
<?php
// Bài 2.6: GET và POST trong PHP

$students = [
    ["name" => "An", "score" => 8.0],
    ["name" => "Binh", "score" => 6.5],
    ["name" => "Chi", "score" => 4.5]
];

$keyword = trim($_GET["keyword"] ?? "");
$message = "";

// Xử lý thêm sinh viên bằng POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $score = $_POST["score"] ?? "";

    if ($name === "" || $score === "" ||
        !is_numeric($score) || $score < 0 || $score > 10) {
        $message = "Vui lòng nhập tên và điểm hợp lệ từ 0 đến 10.";
    } else {
        // Minh họa thêm sinh viên trong lần chạy hiện tại
        $students[] = [
            "name" => $name,
            "score" => (float)$score
        ];

        $message = "Đã thêm sinh viên thành công!";
    }
}

// Tìm kiếm sinh viên bằng GET
$results = [];

foreach ($students as $student) {
    if (
        $keyword === "" ||
        stripos($student["name"], $keyword) !== false
    ) {
        $results[] = $student;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bài 2.6 - GET và POST</title>
</head>
<body>
    <h2>Bài 2.6: GET và POST trong PHP</h2>

    <h3>1. Tìm kiếm sinh viên bằng GET</h3>

    <form method="GET">
        <label>Nhập tên sinh viên:</label>
        <input
            type="text"
            name="keyword"
            value="<?= htmlspecialchars($keyword) ?>"
            placeholder="Ví dụ: An"
        >
        <button type="submit">Tìm kiếm</button>
    </form>

    <h3>Kết quả tìm kiếm</h3>

    <?php if (count($results) > 0): ?>
        <?php foreach ($results as $student): ?>
            <p>
                Tên: <?= htmlspecialchars($student["name"]) ?>
                - Điểm: <?= $student["score"] ?>
            </p>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không tìm thấy sinh viên phù hợp.</p>
    <?php endif; ?>

    <hr>

    <h3>2. Thêm sinh viên bằng POST</h3>

    <form method="POST">
        <p>
            <label>Tên sinh viên:</label>
            <input type="text" name="name" required>
        </p>

        <p>
            <label>Điểm:</label>
            <input
                type="number"
                name="score"
                min="0"
                max="10"
                step="any"
                required
            >
        </p>

        <button type="submit">Thêm sinh viên</button>
    </form>

    <p><?= htmlspecialchars($message) ?></p>

    <p><b>Lưu ý:</b> Dữ liệu thêm mới chỉ tồn tại trong lần gửi form
    hiện tại, chưa được lưu vào cơ sở dữ liệu.</p>
</body>
</html>