<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

// CSRF
if (empty($_SESSION['csrf_edit'])) {
    $_SESSION['csrf_edit'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_edit'];

// Lấy ID sản phẩm
$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("ID sản phẩm không hợp lệ.");
}

// Lấy dữ liệu sản phẩm
$stmt = $conn->prepare("SELECT id, name, price, description, category, image FROM products WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    die("Không tìm thấy sản phẩm.");
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_edit'], $token)) {
        die("CSRF token không hợp lệ.");
    }

    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $oldImage = $product['image'];

    $newImage = $oldImage;

    // Nếu người dùng chọn ảnh mới
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];

            if (in_array($ext, $allowed)) {
                $newName = 'img_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $uploadPath = __DIR__ . '/../uploads/' . $newName;

                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Xóa ảnh cũ nếu tồn tại
                    if (!empty($oldImage)) {
                        $oldFile = __DIR__ . '/../uploads/' . $oldImage;
                        if (is_file($oldFile)) {
                            @unlink($oldFile);
                        }
                    }
                    $newImage = $newName;
                }
            }
        }
    }

    // Update DB
    $stmt2 = $conn->prepare("UPDATE products SET name=?, price=?, description=?, category=?, image=? WHERE id=?");
    $stmt2->bind_param("sdsssi", $name, $price, $description, $category, $newImage, $id);

    if ($stmt2->execute()) {
        $_SESSION['flash_products'] = "Cập nhật sản phẩm thành công.";
        header("Location: products.php"); exit;
    } else {
        $error = "Lỗi cập nhật: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa sản phẩm</title>

<style>
    body { font-family: system-ui, Arial; background:#f4f4f4; margin:0; padding:0; }
    .container { max-width:900px; margin:40px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.06); }
    h1 { margin:0 0 20px; font-size:22px; }

    label { font-size:14px; font-weight:600; display:block; margin-bottom:6px; }

    input, textarea, select {
        width:100%; padding:12px; border-radius:8px;
        border:1px solid #ccc; margin-bottom:16px; font-size:14px;
        background:#fafafa;
    }

    button {
        padding:14px 24px; border:none; border-radius:8px;
        background:#222; color:#fff; font-size:14px; cursor:pointer;
    }
    button:hover { background:#444; }

    .image-preview img {
        width:150px; height:150px;
        object-fit:cover; border-radius:8px;
        border:1px solid #ccc;
    }

    .back { text-decoration:none; display:inline-block; margin-bottom:16px; color:#333; }
</style>

</head>
<body>

<div class="container">
    <a href="products.php" class="back">← Quay lại danh sách</a>
    <h1>Sửa sản phẩm #<?= $product['id'] ?></h1>

    <?php if(isset($error)): ?>
        <div style="padding:12px; background:#ffecec; color:#a10000; border-radius:6px; margin-bottom:15px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <label>Tên sản phẩm</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label>Giá</label>
        <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>

        <label>Mô tả</label>
        <textarea name="description" rows="5"><?= htmlspecialchars($product['description']) ?></textarea>

        <?php
            // Lấy danh sách danh mục
            $catRes = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
            $categories = [];
            if ($catRes) {
                while ($row = $catRes->fetch_assoc()) {
                    $categories[] = $row;
                }
            }
        ?>
            <label>Danh mục</label>
            <select name="category" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['id']) ?>"
                        <?= ($product['category'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

        <label>Ảnh hiện tại</label>
        <div class="image-preview">
            <?php if($product['image'] && is_file(__DIR__ . '/../uploads/' . $product['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($product['image']) ?>">
            <?php else: ?>
                <img src="../assets/images/placeholder-bag.svg">
            <?php endif; ?>
        </div>

        <label>Ảnh mới (nếu muốn thay):</label>
        <input type="file" name="image" accept="image/*">

        <br><br>
        <button type="submit">Lưu thay đổi</button>
    </form>
</div>

</body>
</html>
