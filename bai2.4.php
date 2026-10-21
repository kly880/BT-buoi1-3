
<?php
// Bài 2.4: Mảng chỉ số và mảng kết hợp

$students = [
    [
        'name' => 'An',
        'score' => 8.0
    ],
    [
        'name' => 'Binh',
        'score' => 6.5
    ],
    [
        'name' => 'Chi',
        'score' => 4.5
    ]
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bài 2.4 - Mảng trong PHP</title>
</head>
<body>
    <h2>Bài 2.4: Mảng chỉ số và mảng kết hợp</h2>

    <h3>Danh sách sinh viên</h3>

    <?php
    foreach ($students as $student) {
        echo "Tên: " . htmlspecialchars($student['name']);
        echo " - Điểm: " . $student['score'];
        echo "<br>";
    }
    ?>
</body>
</html>