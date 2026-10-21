
<?php
session_start();

$errors = [];
$success = false;

$data = [
    'name' => '',
    'email' => '',
    'subject' => 'technical',
    'content' => ''
];

$subjects = [
    'general' => 'Hỏi đáp chung',
    'technical' => 'Hỗ trợ kỹ thuật',
    'feedback' => 'Góp ý',
    'other' => 'Khác'
];

$uploadedImage = null;

// Ma hoa du lieu truoc khi hien thi HTML
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

// Xu ly thong bao sau khi gui thanh cong
$successMessage = $_SESSION['contact_success'] ?? '';
unset($_SESSION['contact_success']);

// Xu ly form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Doc du lieu POST
    $data['name'] = trim((string)($_POST['name'] ?? ''));
    $data['email'] = trim((string)($_POST['email'] ?? ''));
    $data['subject'] = (string)($_POST['subject'] ?? '');
    $data['content'] = trim((string)($_POST['content'] ?? ''));

    $token = (string)($_POST['csrf_token'] ?? '');

    // Kiem tra CSRF
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors['csrf'] = 'Yeu cau khong hop le. Vui long tai lai trang.';
    }

    // Kiem tra ho ten
    if ($data['name'] === '') {
        $errors['name'] = 'Vui long nhap ho ten.';
    } elseif (mb_strlen($data['name'], 'UTF-8') > 100) {
        $errors['name'] = 'Ho ten khong duoc qua 100 ky tu.';
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

    // Kiem tra chu de
    if (!array_key_exists($data['subject'], $subjects)) {
        $errors['subject'] = 'Vui long chon chu de hop le.';
    }

    // Kiem tra noi dung
    $contentLength = mb_strlen($data['content'], 'UTF-8');

    if ($data['content'] === '') {
        $errors['content'] = 'Vui long nhap noi dung lien he.';
    } elseif ($contentLength < 10 || $contentLength > 500) {
        $errors['content'] =
            'Noi dung phai tu 10 den 500 ky tu.';
    }

    // Kiem tra upload anh neu nguoi dung chon file
    if (
        isset($_FILES['avatar'])
        && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $file = $_FILES['avatar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['avatar'] = 'Upload anh that bai.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors['avatar'] = 'Anh khong duoc vuot qua 2 MB.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($allowedTypes[$mime])) {
                $errors['avatar'] =
                    'Chi chap nhan anh JPG, PNG hoac WEBP.';
            } else {
                $imageInfo = @getimagesize($file['tmp_name']);

                if ($imageInfo === false) {
                    $errors['avatar'] = 'File khong phai anh hop le.';
                } else {
                    $uploadedImage = [
                        'tmp_name' => $file['tmp_name'],
                        'extension' => $allowedTypes[$mime]
                    ];
                }
            }
        }
    }

    // Neu khong co loi thi xu ly thanh cong
    if (empty($errors)) {
        $imageName = '';

        // Luu anh vao thu muc uploads
        if ($uploadedImage !== null) {
            $uploadDir = __DIR__ . '/uploads';

            if (
                !is_dir($uploadDir)
                && !mkdir($uploadDir, 0755, true)
                && !is_dir($uploadDir)
            ) {
                $errors['avatar'] =
                    'Khong the tao thu muc luu anh.';
            } else {
                // Ten file ngau nhien, khong dung ten goc
                $imageName = bin2hex(random_bytes(16))
                    . '.'
                    . $uploadedImage['extension'];

                $destination = $uploadDir . '/' . $imageName;

                if (
                    !move_uploaded_file(
                        $uploadedImage['tmp_name'],
                        $destination
                    )
                ) {
                    $errors['avatar'] = 'Khong the luu anh.';
                }
            }
        }

        // Luu thong tin lien he vao JSON
        if (empty($errors)) {
            $record = [
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $subjects[$data['subject']],
                'content' => $data['content'],
                'avatar' => $imageName,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $dataFile = __DIR__ . '/contacts.json';
            $records = [];

            if (is_file($dataFile)) {
                $jsonContent = file_get_contents($dataFile);

                if (
                    $jsonContent !== false
                    && trim($jsonContent) !== ''
                ) {
                    $decoded = json_decode($jsonContent, true);

                    if (is_array($decoded)) {
                        $records = $decoded;
                    }
                }
            }

            $records[] = $record;

            $json = json_encode(
                $records,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

            if (
                $json === false
                || file_put_contents(
                    $dataFile,
                    $json,
                    LOCK_EX
                ) === false
            ) {
                $errors['save'] =
                    'Khong the luu thong tin. Hay kiem tra quyen ghi file.';
            } else {
                $_SESSION['contact_success'] =
                    'Gui lien he thanh cong! Cam on ban.';

                $_SESSION['csrf_token'] =
                    bin2hex(random_bytes(32));

                // Redirect sau khi gui thanh cong
                header(
                    'Location: ' . strtok($_SERVER['REQUEST_URI'], '?')
                );
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.8 - Form liên hệ</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f2f6fa;
            color: #263b30;
        }

        .container {
            max-width: 760px;
            margin: auto;
        }

        .header {
            padding: 22px 28px;
            background: #00ad55;
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
        }

        .card {
            padding: 28px;
            background: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.07);
        }

        .subtitle {
            margin-top: 0;
            margin-bottom: 25px;
            color: #738076;
            font-size: 14px;
            line-height: 1.7;
        }

        .field {
            margin-bottom: 19px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #344b3c;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d6dfe6;
            border-radius: 7px;
            background: white;
            font-family: inherit;
            font-size: 14px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2780bd;
            box-shadow: 0 0 0 3px rgba(39, 128, 189, 0.12);
        }

        textarea {
            min-height: 125px;
            resize: vertical;
        }

        .hint {
            display: block;
            margin-top: 7px;
            color: #7b8790;
            font-size: 12px;
            line-height: 1.5;
        }

        .field-error {
            display: block;
            margin-top: 7px;
            color: #c62828;
            font-size: 13px;
        }

        .input-error {
            border-color: #e53935;
            background: #fff8f8;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 7px;
            background: #287db5;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn:hover {
            background: #206a9c;
        }

        .error-box {
            margin-bottom: 22px;
            padding: 15px 18px;
            color: #a52b24;
            background: #fff0ef;
            border: 1px solid #f0c7c3;
            border-radius: 8px;
            line-height: 1.7;
        }

        .success-box {
            margin-bottom: 22px;
            padding: 15px 18px;
            color: #236b3d;
            background: #eaf8ef;
            border: 1px solid #c9e9d3;
            border-radius: 8px;
            line-height: 1.7;
        }

        .note {
            margin-top: 23px;
            padding: 15px 17px;
            background: #eaf7ee;
            border-radius: 8px;
            color: #286d43;
            font-size: 13px;
            line-height: 1.8;
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
        <h1>3.8 Thực hành: Form liên hệ</h1>
    </div>

    <div class="card">

        <p class="subtitle">
            Vui lòng nhập đầy đủ thông tin bên dưới.
            Các trường có dấu * là bắt buộc.
        </p>

        <?php if ($successMessage !== ''): ?>
            <div class="success-box" role="status">
                <strong><?= e($successMessage) ?></strong>
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

        <form method="POST" action="" enctype="multipart/form-data">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['csrf_token']) ?>"
            >

            <div class="field">
                <label for="name">Họ tên *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                    maxlength="100"
                    placeholder="Nhập họ và tên"
                    value="<?= e($data['name']) ?>"
                    class="<?= isset($errors['name']) ? 'input-error' : '' ?>"
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
                    placeholder="example@gmail.com"
                    value="<?= e($data['email']) ?>"
                    class="<?= isset($errors['email']) ? 'input-error' : '' ?>"
                >

                <?php if (isset($errors['email'])): ?>
                    <span class="field-error">
                        <?= e($errors['email']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="subject">Chủ đề *</label>
                <select
                    id="subject"
                    name="subject"
                    required
                    class="<?= isset($errors['subject']) ? 'input-error' : '' ?>"
                >
                    <?php foreach ($subjects as $key => $label): ?>
                        <option
                            value="<?= e($key) ?>"
                            <?= $data['subject'] === $key ? 'selected' : '' ?>
                        >
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if (isset($errors['subject'])): ?>
                    <span class="field-error">
                        <?= e($errors['subject']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="content">Nội dung *</label>
                <textarea
                    id="content"
                    name="content"
                    required
                    minlength="10"
                    maxlength="500"
                    placeholder="Nhập nội dung liên hệ..."
                    class="<?= isset($errors['content']) ? 'input-error' : '' ?>"
                ><?= e($data['content']) ?></textarea>

                <span class="hint">
                    Nội dung phải từ 10 đến 500 ký tự.
                </span>

                <?php if (isset($errors['content'])): ?>
                    <span class="field-error">
                        <?= e($errors['content']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="avatar">Ảnh đại diện (không bắt buộc)</label>
                <input
                    type="file"
                    id="avatar"
                    name="avatar"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <span class="hint">
                    Chỉ nhận JPG, PNG hoặc WEBP, dung lượng tối đa 2 MB.
                </span>

                <?php if (isset($errors['avatar'])): ?>
                    <span class="field-error">
                        <?= e($errors['avatar']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <button class="btn" type="submit">
                Gửi liên hệ
            </button>

        </form>

        <div class="note">
            <strong>Quy trình xử lý:</strong><br>
            1. Nhận dữ liệu bằng POST.<br>
            2. Kiểm tra họ tên, email, chủ đề và nội dung.<br>
            3. Kiểm tra loại và dung lượng ảnh upload.<br>
            4. Nếu lỗi, hiển thị thông báo và giữ dữ liệu đã nhập.<br>
            5. Nếu hợp lệ, lưu thông tin vào file JSON và chuyển hướng.
        </div>

    </div>
</div>
</body>
</html>
