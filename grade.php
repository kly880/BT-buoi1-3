
<?php
// Kiem tra diem co hop le hay khong
function isValidScore($score): bool
{
    if ($score === '' || !is_numeric($score)) {
        return false;
    }

    return $score >= 0 && $score <= 10;
}

// Tinh diem hoc phan
function finalScore(
    float $chuyenCan,
    float $giuaKy,
    float $cuoiKy
): float {
    return $chuyenCan * 0.1
         + $giuaKy * 0.3
         + $cuoiKy * 0.6;
}

// Xep loai ket qua
function classifyScore(float $diem): string
{
    if ($diem >= 8.5) {
        return 'Gioi';
    } elseif ($diem >= 7) {
        return 'Kha';
    } elseif ($diem >= 5) {
        return 'Trung binh';
    } else {
        return 'Chua dat';
    }
}

// Chuyen du lieu thanh chuoi an toan khi hien thi HTML
function e($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Khoi tao gia tri mac dinh
$hoTen = '';
$chuyenCan = '';
$giuaKy = '';
$cuoiKy = '';

$errors = [];
$ketQua = null;

// Xu ly khi nguoi dung bam nut tinh diem
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoTen = trim($_POST['hoTen'] ?? '');
    $chuyenCan = trim($_POST['chuyenCan'] ?? '');
    $giuaKy = trim($_POST['giuaKy'] ?? '');
    $cuoiKy = trim($_POST['cuoiKy'] ?? '');

    // Kiem tra ho ten
    if ($hoTen === '') {
        $errors[] = 'Vui long nhap ho va ten sinh vien.';
    }

    // Kiem tra diem chuyen can
    if (!isValidScore($chuyenCan)) {
        $errors[] = 'Diem chuyen can phai tu 0 den 10.';
    }

    // Kiem tra diem giua ky
    if (!isValidScore($giuaKy)) {
        $errors[] = 'Diem giua ky phai tu 0 den 10.';
    }

    // Kiem tra diem cuoi ky
    if (!isValidScore($cuoiKy)) {
        $errors[] = 'Diem cuoi ky phai tu 0 den 10.';
    }

    // Neu khong co loi thi tinh diem
    if (empty($errors)) {
        $diemTongKet = finalScore(
            (float)$chuyenCan,
            (float)$giuaKy,
            (float)$cuoiKy
        );

        $ketQua = [
            'hoTen' => $hoTen,
            'chuyenCan' => (float)$chuyenCan,
            'giuaKy' => (float)$giuaKy,
            'cuoiKy' => (float)$cuoiKy,
            'diemTongKet' => $diemTongKet,
            'xepLoai' => classifyScore($diemTongKet)
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Máy tính điểm học phần</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 35px 16px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f0f7f2;
            color: #263b30;
        }

        .container {
            max-width: 850px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 28px;
        }

        .header h1 {
            margin: 0 0 12px;
            color: #247348;
            font-size: 30px;
        }

        .header p {
            margin: 0;
            color: #65776b;
            font-size: 15px;
            line-height: 1.6;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 8px 28px rgba(32, 91, 57, 0.09);
            border: 1px solid #e0eee4;
        }

        .section-title {
            margin: 0 0 22px;
            color: #247348;
            font-size: 21px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            font-weight: bold;
            font-size: 14px;
            color: #344b3c;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d4e3d8;
            border-radius: 9px;
            background: #fbfdfb;
            font-size: 15px;
            outline: none;
            transition: 0.2s;
        }

        input:focus {
            border-color: #32965c;
            box-shadow: 0 0 0 3px rgba(50, 150, 92, 0.12);
            background: #ffffff;
        }

        .score-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .hint {
            display: block;
            margin-top: 7px;
            font-size: 12px;
            color: #7a8b80;
        }

        .formula {
            padding: 15px;
            margin: 8px 0 23px;
            background: #f0f8f2;
            border: 1px solid #dcefe1;
            border-radius: 9px;
            color: #286d43;
            font-size: 14px;
            line-height: 1.7;
        }

        .formula strong {
            display: block;
            margin-bottom: 3px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        button {
            padding: 13px 22px;
            border: none;
            border-radius: 9px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-primary {
            flex: 1;
            background: #27834b;
            color: white;
        }

        .btn-primary:hover {
            background: #1d6a3b;
        }

        .btn-reset {
            background: #edf3ee;
            color: #486451;
        }

        .btn-reset:hover {
            background: #dfeae1;
        }

        .error-box {
            margin-bottom: 22px;
            padding: 15px 18px;
            border-radius: 9px;
            background: #fff1f0;
            border: 1px solid #f3c5c1;
            color: #a72e26;
        }

        .error-box h3 {
            margin: 0 0 10px;
            font-size: 16px;
        }

        .error-box ul {
            margin: 0;
            padding-left: 22px;
            line-height: 1.8;
        }

        .result {
            margin-top: 25px;
            padding: 23px;
            border-radius: 12px;
            background: #f0f8f2;
            border: 1px solid #d5eadb;
        }

        .result h2 {
            margin: 0 0 18px;
            color: #247348;
            font-size: 21px;
        }

        .result-name {
            margin-bottom: 18px;
            font-size: 16px;
        }

        .result-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .result-item {
            padding: 14px 10px;
            background: #ffffff;
            border-radius: 9px;
            text-align: center;
            border: 1px solid #e0eee4;
        }

        .result-item span {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            color: #718176;
        }

        .result-item strong {
            color: #2b6640;
            font-size: 19px;
        }

        .total-score {
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #dcefe1;
        }

        .total-score .label {
            margin-bottom: 8px;
            color: #627469;
            font-size: 14px;
        }

        .total-score .number {
            color: #218148;
            font-size: 38px;
            font-weight: bold;
        }

        .classification {
            margin-top: 12px;
            font-size: 16px;
            font-weight: bold;
            color: #247348;
        }

        .footer {
            margin-top: 22px;
            text-align: center;
            color: #809086;
            font-size: 12px;
        }

        @media (max-width: 600px) {
            body {
                padding: 22px 12px;
            }

            .card {
                padding: 21px 16px;
            }

            .header h1 {
                font-size: 25px;
            }

            .score-grid,
            .result-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .result-item {
                margin-bottom: 9px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn-primary,
            .btn-reset {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h1>Máy tính điểm học phần</h1>
            <p>Nhập điểm thành phần để tính điểm tổng kết và xếp loại học tập.</p>
        </div>

        <div class="card">
            <h2 class="section-title">Thông tin sinh viên</h2>

            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <h3>Vui lòng kiểm tra lại thông tin</h3>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="hoTen">Họ và tên sinh viên</label>
                    <input
                        type="text"
                        id="hoTen"
                        name="hoTen"
                        placeholder="Nhập họ và tên sinh viên"
                        value="<?= e($hoTen) ?>"
                    >
                </div>

                <h2 class="section-title">Điểm thành phần</h2>

                <div class="score-grid">
                    <div class="form-group">
                        <label for="chuyenCan">Điểm chuyên cần</label>
                        <input
                            type="number"
                            id="chuyenCan"
                            name="chuyenCan"
                            min="0"
                            max="10"
                            step="0.1"
                            placeholder="0 - 10"
                            value="<?= e($chuyenCan) ?>"
                        >
                        <span class="hint">Trọng số: 10%</span>
                    </div>

                    <div class="form-group">
                        <label for="giuaKy">Điểm giữa kỳ</label>
                        <input
                            type="number"
                            id="giuaKy"
                            name="giuaKy"
                            min="0"
                            max="10"
                            step="0.1"
                            placeholder="0 - 10"
                            value="<?= e($giuaKy) ?>"
                        >
                        <span class="hint">Trọng số: 30%</span>
                    </div>

                    <div class="form-group">
                        <label for="cuoiKy">Điểm cuối kỳ</label>
                        <input
                            type="number"
                            id="cuoiKy"
                            name="cuoiKy"
                            min="0"
                            max="10"
                            step="0.1"
                            placeholder="0 - 10"
                            value="<?= e($cuoiKy) ?>"
                        >
                        <span class="hint">Trọng số: 60%</span>
                    </div>
                </div>

                <div class="formula">
                    <strong>Công thức tính điểm tổng kết</strong>
                    Điểm tổng kết = Điểm chuyên cần × 10%
                    + Điểm giữa kỳ × 30%
                    + Điểm cuối kỳ × 60%
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary">
                        Tính điểm học phần
                    </button>

                    <button
                        type="button"
                        class="btn-reset"
                        onclick="window.location.href = window.location.pathname;"
                    >
                        Nhập lại
                    </button>
                </div>
            </form>

            <?php if ($ketQua !== null): ?>
                <div class="result">
                    <h2>Kết quả học phần</h2>

                    <div class="result-name">
                        <strong>Sinh viên:</strong>
                        <?= e($ketQua['hoTen']) ?>
                    </div>

                    <div class="result-grid">
                        <div class="result-item">
                            <span>Điểm chuyên cần</span>
                            <strong>
                                <?= number_format($ketQua['chuyenCan'], 1) ?>
                            </strong>
                        </div>

                        <div class="result-item">
                            <span>Điểm giữa kỳ</span>
                            <strong>
                                <?= number_format($ketQua['giuaKy'], 1) ?>
                            </strong>
                        </div>

                        <div class="result-item">
                            <span>Điểm cuối kỳ</span>
                            <strong>
                                <?= number_format($ketQua['cuoiKy'], 1) ?>
                            </strong>
                        </div>
                    </div>

                    <div class="total-score">
                        <div class="label">Điểm tổng kết học phần</div>

                        <div class="number">
                            <?= number_format($ketQua['diemTongKet'], 2) ?>
                        </div>

                        <div class="classification">
                            Xếp loại: <?= e($ketQua['xepLoai']) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            Bài tập PHP 2.9 - Máy tính điểm học phần
        </div>

    </div>
</body>
</html>
