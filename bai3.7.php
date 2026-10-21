
<?php
session_start();

// Khoi tao du lieu va loi
$errors = [];
$data = [
    'name' => '',
    'email' => '',
    'birthday' => ''
];

// Ham ma hoa du lieu khi hien thi HTML
function e($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// Tao CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Xu ly thong bao sau khi chuyen huong
$successMessage = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

// Doc du lieu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name'] = trim((string)($_POST['name'] ?? ''));
    $data['email'] = trim((string)($_POST['email'] ?? ''));
    $data['birthday'] = trim((string)($_POST['birthday'] ?? ''));

    $token = (string)($_POST['csrf_token'] ?? '');

    // Kiem tra CSRF
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors['csrf'] = 'Yeu cau khong hop le. Vui long tai lai trang.';
    }

    // Kiem tra ho ten
    if ($data['name'] === '') {
        $errors['name'] = 'Vui long nhap ho va ten.';
    } elseif (mb_strlen($data['name'], 'UTF-8') > 100) {
        $errors['name'] = 'Ho va ten khong duoc qua 100 ky tu.';
    }

    // Kiem tra email
    if ($data['email'] === '') {
        $errors['email'] = 'Vui long nhap email.';
    } elseif (
        filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false
    ) {
        $errors['email'] = 'Email khong dung dinh dang.';
    } elseif (strlen($data['email']) > 254) {
        $errors['email'] = 'Email qua dai.';
    }

    // Kiem tra ngay sinh
    if ($data['birthday'] === '') {
        $errors['birthday'] = 'Vui long nhap ngay sinh.';
    } else {
        $date = DateTime::createFromFormat(
            '!Y-m-d',
            $data['birthday']
        );

        $dateErrors = DateTime::getLastErrors();

        $validDate = $date !== false
            && $date->format('Y-m-d') === $data['birthday']
            && (
                $dateErrors === false
                || (
                    $dateErrors['warning_count'] === 0
                    && $dateErrors['error_count'] === 0
                )
            );

        if (!$validDate) {
            $errors['birthday'] = 'Ngay sinh khong hop le.';
        } elseif ($data['birthday'] > date('Y-m-d')) {
            $errors['birthday'] = 'Ngay sinh khong duoc o tuong lai.';
        }
    }

    // Neu hop le thi luu thong tin va redirect
    if (empty($errors)) {
        $record = [
            'name' => $data['name'],
            'email' => $data['email'],
            'birthday' => $data['birthday'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $file = __DIR__ . '/students.json';
        $students = [];

        if (is_file($file)) {
            $content = file_get_contents($file);

            if ($content !== false && trim($content) !== '') {
                $decoded = json_decode($content, true);

                if (is_array($decoded)) {
                    $students = $decoded;
                }
            }
        }

        $students[] = $record;

        $json = json_encode(
            $students,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        if (
            $json === false
            || file_put_contents($file, $json, LOCK_EX) === false
        ) {
            $errors['save'] =
                'Khong the luu du lieu. Hay kiem tra quyen ghi thu muc.';
        } else {
            $_SESSION['success_message'] =
                'Dang ky sinh vien thanh cong!';

            // Tao token moi sau khi gui thanh cong
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Chuyen huong sau khi xu ly thanh cong
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.7 Demo - Form đăng ký sinh viên</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px 15px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f7f3;
            color: #263b30;
        }

        .container {
            max-width: 760px;
            margin: 0 auto;
        }

        .header {
            padding: 23px 28px;
            color: #fff;
            background: #00ad55;
            border-radius: 12px 12px 0 0;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
        }

        .card {
            padding: 28px;
            background: #fff;
            border: 1px solid #e0ebe3;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.05);
        }

        .intro {
            margin-top: 0;
            color: #65766b;
            line-height: 1.7;
        }

        .info {
            margin: 20px 0 25px;
            padding: 16px 18px;
            background: #eaf7ee;
            border-left: 4px solid #00ad55;
            border-radius: 7px;
            line-height: 1.8;
        }

        h2 {
            margin: 25px 0 20px;
            color: #247348;
            font-size: 21px;
        }

        .field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ceded2;
            border-radius: 7px;
            font-size: 15px;
            background: #fff;
        }

        input:focus {
            outline: none;
            border-color: #00ad55;
            box-shadow: 0 0 0 3px rgba(0, 173, 85, 0.12);
        }

        .hint {
            display: block;
            margin-top: 7px;
            color: #7b8a80;
            font-size: 12px;
        }

        .field-error {
            display: block;
            margin-top: 7px;
            color: #b42318;
            font-size: 13px;
        }

        .input-error {
            border-color: #d92d20;
            background: #fffafa;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 7px;
            background: #00ad55;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #008e45;
        }

        .error-box {
            margin-bottom: 22px;
            padding: 15px 18px;
            color: #a52b24;
            background: #fff0ef;
            border: 1px solid #f0c7c3;
            border-radius: 7px;
            line-height: 1.7;
        }

        .success-box {
            margin-bottom: 22px;
            padding: 16px;
            color: #236b3d;
            background: #eaf8ef;
            border: 1px solid #c9e9d3;
            border-radius: 8px;
            line-height: 1.7;
        }

        .footer {
            margin-top: 22px;
            text-align: center;
            color: #7b8a80;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            body {
                padding: 18px 10px;
            }

            .card {
                padding: 20px;
            }

            .header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>3.7 Demo: Form đăng ký sinh viên</h1>
    </div>

    <div class="card">

        <p class="intro">
            Nhập họ tên, email và ngày sinh để đăng ký.
            Hệ thống sẽ kiểm tra dữ liệu trước khi lưu.
        </p>

        <div class="info">
            <strong>Quy trình xử lý</strong><br>
            1. Nhận dữ liệu bằng POST.<br>
            2. Kiểm tra các trường bắt buộc.<br>
            3. Kiểm tra định dạng email và ngày sinh.<br>
            4. Nếu có lỗi, hiển thị lỗi và giữ dữ liệu.<br>
            5. Nếu hợp lệ, lưu thông tin và chuyển hướng.
        </div>

        <?php if ($successMessage !== ''): ?>
            <div class="success-box" role="status">
                <strong>
                    <?= e($successMessage) ?>
                </strong>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-box" role="alert">
                <strong>Vui lòng kiểm tra lại thông tin:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <h2>Thông tin đăng ký</h2>

        <form method="POST" action="">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['csrf_token']) ?>"
            >

            <div class="field">
                <label for="name">Họ và tên *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                    maxlength="100"
                    autocomplete="name"
                    placeholder="Nhập họ và tên"
                    class="<?= isset($errors['name']) ? 'input-error' : '' ?>"
                    value="<?= e($data['name']) ?>"
                    aria-invalid="<?= isset($errors['name']) ? 'true' : 'false' ?>"
                >

                <?php if (isset($errors['name'])): ?>
                    <span class="field-error">
                        <?= e($errors['name']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="email">Email *</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    maxlength="254"
                    autocomplete="email"
                    placeholder="example@gmail.com"
                    class="<?= isset($errors['email']) ? 'input-error' : '' ?>"
                    value="<?= e($data['email']) ?>"
                    aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>"
                >

                <?php if (isset($errors['email'])): ?>
                    <span class="field-error">
                        <?= e($errors['email']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="birthday">Ngày sinh *</label>
                <input
                    type="date"
                    id="birthday"
                    name="birthday"
                    required
                    max="<?= date('Y-m-d') ?>"
                    class="<?= isset($errors['birthday']) ? 'input-error' : '' ?>"
                    value="<?= e($data['birthday']) ?>"
                    aria-invalid="<?= isset($errors['birthday']) ? 'true' : 'false' ?>"
                >

                <?php if (isset($errors['birthday'])): ?>
                    <span class="field-error">
                        <?= e($errors['birthday']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <button type="submit">
                Đăng ký sinh viên
            </button>
        </form>

        <div class="footer">
            Bài thực hành PHP 3.7 - Form đăng ký sinh viên
        </div>

    </div>
</div>
</body>
</html>
