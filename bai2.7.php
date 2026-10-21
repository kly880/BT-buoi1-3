
<?php
function money(float $value): string {
    return number_format($value, 0, ',', '.') . ' đ';
}

$name = "";
$qty = "";
$price = "";
$total = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $qtyInput = $_POST["qty"] ?? "";
    $priceInput = $_POST["price"] ?? "";

    if (
        $name === "" ||
        $qtyInput === "" ||
        $priceInput === "" ||
        filter_var($qtyInput, FILTER_VALIDATE_INT) === false ||
        !is_numeric($priceInput) ||
        (int)$qtyInput < 1 ||
        (float)$priceInput < 0
    ) {
        $error = "Vui lòng nhập thông tin hợp lệ!";
    } else {
        $qty = (int)$qtyInput;
        $price = (float)$priceInput;
        $total = $qty * $price;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài 2.7 - Hóa đơn học liệu</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px 16px;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #e8f5e9, #f4f7fb);
            color: #263238;
        }

        .container {
            max-width: 760px;
            margin: 0 auto;
        }

        .invoice {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 35px rgba(30, 70, 45, 0.12);
            border: 1px solid #e1eee4;
        }

        .header {
            background: linear-gradient(135deg, #079447, #16b364);
            color: white;
            padding: 30px;
        }

        .header .tag {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(255,255,255,0.18);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .header p {
            margin: 0;
            color: #e5ffed;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .section-title {
            font-size: 18px;
            margin: 0 0 8px;
            color: #176b3a;
        }

        .description {
            color: #718096;
            font-size: 14px;
            margin: 0 0 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d9e3dc;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
            transition: 0.2s;
            background: #fcfefd;
        }

        input:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12);
            background: #fff;
        }

        .hint {
            margin-top: 6px;
            font-size: 12px;
            color: #87948b;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #128a45;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            background: #087438;
            transform: translateY(-1px);
        }

        .error {
            margin-top: 20px;
            padding: 13px 15px;
            background: #fff0f0;
            border: 1px solid #ffd3d3;
            color: #c62828;
            border-radius: 10px;
        }

        .result {
            margin-top: 28px;
            padding: 22px;
            background: #f3fbf5;
            border: 1px solid #d5efdc;
            border-radius: 14px;
        }

        .result h2 {
            margin: 0 0 18px;
            font-size: 19px;
            color: #176b3a;
        }

        .result-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px dashed #d5e5d9;
            font-size: 14px;
        }

        .result-row span:first-child {
            color: #66756a;
        }

        .result-row strong {
            text-align: right;
            overflow-wrap: anywhere;
        }

        .total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 18px;
            padding: 17px;
            border-radius: 12px;
            background: #128a45;
            color: white;
        }

        .total span {
            font-weight: 600;
        }

        .total strong {
            font-size: 23px;
        }

        .footer {
            padding: 18px 30px;
            background: #fafcfb;
            border-top: 1px solid #edf2ee;
            color: #8a968d;
            text-align: center;
            font-size: 12px;
        }

        @media (max-width: 520px) {
            body {
                padding: 20px 10px;
            }

            .header, .content {
                padding: 22px;
            }

            .header h1 {
                font-size: 23px;
            }

            .result-row {
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
<div class="container">
    <div class="invoice">

        <div class="header">
            <div class="tag">BÀI THỰC HÀNH PHP 2.7</div>
            <h1>Hóa đơn học liệu</h1>
            <p>Nhập thông tin tài liệu để tính thành tiền nhanh chóng.</p>
        </div>

        <div class="content">
            <h2 class="section-title">Thông tin tài liệu</h2>
            <p class="description">
                Vui lòng điền đầy đủ thông tin bên dưới.
            </p>

            <form method="POST">
                <div class="form-group">
                    <label for="name">Tên tài liệu</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        placeholder="Ví dụ: Giáo trình PHP"
                        value="<?= htmlspecialchars($name) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="qty">Số lượng</label>
                    <input
                        id="qty"
                        type="number"
                        name="qty"
                        min="1"
                        step="1"
                        placeholder="Nhập số lượng"
                        value="<?= htmlspecialchars((string)$qty) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="price">Đơn giá (VNĐ)</label>
                    <input
                        id="price"
                        type="number"
                        name="price"
                        min="0"
                        step="any"
                        placeholder="Ví dụ: 50000"
                        value="<?= htmlspecialchars((string)$price) ?>"
                        required
                    >
                    <div class="hint">Nhập đơn giá bằng số, không cần dấu chấm.</div>
                </div>

                <button class="btn" type="submit">
                    Tính thành tiền
                </button>
            </form>

            <?php if ($error !== ""): ?>
                <div class="error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($total !== null): ?>
                <div class="result">
                    <h2>✓ Kết quả hóa đơn</h2>

                    <div class="result-row">
                        <span>Tên tài liệu</span>
                        <strong><?= htmlspecialchars($name) ?></strong>
                    </div>

                    <div class="result-row">
                        <span>Số lượng</span>
                        <strong><?= $qty ?></strong>
                    </div>

                    <div class="result-row">
                        <span>Đơn giá</span>
                        <strong><?= money($price) ?></strong>
                    </div>

                    <div class="total">
                        <span>THÀNH TIỀN</span>
                        <strong><?= money($total) ?></strong>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            Bài thực hành PHP · Xử lý biểu mẫu POST và tính toán hóa đơn
        </div>

    </div>
</div>
</body>
</html>
