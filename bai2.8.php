
<?php
// Bài 2.8: Bảng điểm 3 sinh viên

// 1. Tạo mảng gồm 3 sinh viên
$students = [
    [
        "name" => "Nguyen Van An",
        "midterm" => 7.5,
        "final" => 8.0
    ],
    [
        "name" => "Tran Thi Binh",
        "midterm" => 6.0,
        "final" => 5.5
    ],
    [
        "name" => "Le Van Cuong",
        "midterm" => 4.0,
        "final" => 4.5
    ]
];

// 2. Hàm tính điểm trung bình
function calculateAverage(float $midterm, float $final): float
{
    return ($midterm + $final) / 2;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài 2.8 - Bảng điểm sinh viên</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 35px 16px;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f1f8f3;
            color: #26352b;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(20, 80, 40, 0.10);
        }

        .header {
            padding: 28px 32px;
            color: white;
            background: linear-gradient(135deg, #07863e, #16b364);
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 27px;
        }

        .header p {
            margin: 0;
            color: #e4ffeb;
            font-size: 14px;
        }

        .content {
            padding: 28px 32px;
        }

        .summary {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .summary-card {
            flex: 1;
            min-width: 150px;
            padding: 18px;
            border: 1px solid #dceee1;
            background: #f5fbf6;
            border-radius: 12px;
        }

        .summary-card span {
            display: block;
            color: #718078;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .summary-card strong {
            font-size: 24px;
            color: #118344;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e1ebe4;
            border-radius: 12px;
        }

        table {
            width: 100%;
            min-width: 650px;
            border-collapse: collapse;
        }

        th {
            background: #e8f6ec;
            color: #176b3a;
            padding: 16px 14px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 16px 14px;
            border-top: 1px solid #edf2ee;
            font-size: 14px;
        }

        tbody tr:hover {
            background: #f8fcf9;
        }

        .score {
            font-weight: 600;
            color: #334b3b;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .pass {
            background: #dcfce7;
            color: #166534;
        }

        .fail {
            background: #fee2e2;
            color: #b91c1c;
        }

        .note {
            margin-top: 20px;
            padding: 15px 18px;
            background: #f5fbf6;
            border-left: 4px solid #16a34a;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.6;
        }

        .footer {
            padding: 17px 32px;
            border-top: 1px solid #edf2ee;
            background: #fafcfb;
            color: #829087;
            text-align: center;
            font-size: 12px;
        }

        @media (max-width: 600px) {
            body {
                padding: 14px 8px;
            }

            .header, .content {
                padding: 20px 16px;
            }

            .header h1 {
                font-size: 22px;
            }

            .summary-card {
                min-width: 100%;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Bảng điểm 3 sinh viên</h1>
        <p>Bài 2.8 · Thực hành mảng, hàm và vòng lặp trong PHP</p>
    </div>

    <div class="content">

        <?php
        $passedCount = 0;

        foreach ($students as $student) {
            $average = calculateAverage(
                $student["midterm"],
                $student["final"]
            );

            if ($average >= 5) {
                $passedCount++;
            }
        }
        ?>

        <div class="summary">
            <div class="summary-card">
                <span>Tổng số sinh viên</span>
                <strong><?= count($students) ?></strong>
            </div>

            <div class="summary-card">
                <span>Số sinh viên đạt</span>
                <strong><?= $passedCount ?></strong>
            </div>

            <div class="summary-card">
                <span>Số sinh viên chưa đạt</span>
                <strong><?= count($students) - $passedCount ?></strong>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Họ và tên</th>
                        <th>Giữa kỳ</th>
                        <th>Cuối kỳ</th>
                        <th>Điểm trung bình</th>
                        <th>Kết quả</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($students as $index => $student): ?>
                        <?php
                        $average = calculateAverage(
                            $student["midterm"],
                            $student["final"]
                        );

                        $passed = $average >= 5;
                        ?>

                        <tr>
                            <td><?= $index + 1 ?></td>

                            <td>
                                <?= htmlspecialchars($student["name"], ENT_QUOTES, "UTF-8") ?>
                            </td>

                            <td class="score">
                                <?= number_format($student["midterm"], 1) ?>
                            </td>

                            <td class="score">
                                <?= number_format($student["final"], 1) ?>
                            </td>

                            <td class="score">
                                <?= number_format($average, 2) ?>
                            </td>

                            <td>
                                <?php if ($passed): ?>
                                    <span class="badge pass">Đạt</span>
                                <?php else: ?>
                                    <span class="badge fail">Chưa đạt</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="note">
            <strong>Quy tắc đánh giá:</strong>
            Điểm trung bình từ 5.00 trở lên là <strong>Đạt</strong>;
            dưới 5.00 là <strong>Chưa đạt</strong>.
            <br>
            <strong>Công thức:</strong>
            Điểm trung bình = (Điểm giữa kỳ + Điểm cuối kỳ) / 2.
        </div>

    </div>

    <div class="footer">
        Bài thực hành PHP 2.8 · Bảng điểm sinh viên
    </div>

</div>
</body>
</html>