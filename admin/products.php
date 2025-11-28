<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

// CSRF token for future actions (delete/edit) - prepared now
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Flash message handling
$flash = $_SESSION['flash_products'] ?? '';
unset($_SESSION['flash_products']);

// Handle delete action securely
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '') === 'delete') {
    $delToken = $_POST['csrf_token'] ?? '';
    $delIdRaw = $_POST['delete_id'] ?? '';
    if(!hash_equals($_SESSION['csrf_token'], $delToken)) {
        $_SESSION['flash_products'] = 'Token không hợp lệ.';
        header('Location: products.php'); exit;
    }
    if(!ctype_digit($delIdRaw)) {
        $_SESSION['flash_products'] = 'ID không hợp lệ.';
        header('Location: products.php'); exit;
    }
    $delId = (int)$delIdRaw;
    // Lấy tên ảnh trước khi xóa
    if($stmt = $conn->prepare('SELECT image FROM products WHERE id=?')) {
        $stmt->bind_param('i',$delId);
        $stmt->execute();
        $stmt->bind_result($imgName);
        $found = $stmt->fetch();
        $stmt->close();
        if(!$found) {
            $_SESSION['flash_products'] = 'Sản phẩm không tồn tại.';
            header('Location: products.php'); exit;
        }
        // Xóa hàng
        if($stmt2 = $conn->prepare('DELETE FROM products WHERE id=?')) {
            $stmt2->bind_param('i',$delId);
            if($stmt2->execute()) {
                // Xóa file ảnh nếu có trong uploads hoặc assets/images
                if(!empty($imgName)) {
                    $uploadPath = __DIR__ . '/../uploads/' . $imgName;
                    $legacyPath = __DIR__ . '/../assets/images/' . $imgName;
                    if(is_file($uploadPath)) { @unlink($uploadPath); }
                    elseif(is_file($legacyPath)) { /* tùy chọn không xóa assets tĩnh */ }
                }
                $_SESSION['flash_products'] = 'Đã xóa sản phẩm #'.$delId.' thành công.';
            } else {
                $_SESSION['flash_products'] = 'Lỗi khi xóa: '.htmlspecialchars($conn->error);
            }
            $stmt2->close();
        }
    }
    header('Location: products.php'); exit;
}

// Handle search
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) && ctype_digit($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Count total
$countSql = 'SELECT COUNT(*) FROM products';
$params = [];
$types = '';
if ($q !== '') {
    $countSql .= ' WHERE name LIKE ? OR category LIKE ?';
    $like = '%' . $q . '%';
    $params = [$like, $like];
    $types = 'ss';
}
$total = 0;
if ($stmt = $conn->prepare($countSql)) {
    if ($types) { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();
}
$totalPages = $total ? (int)ceil($total / $perPage) : 1;
if ($page > $totalPages) { $page = $totalPages; $offset = ($page - 1) * $perPage; }

// Fetch page
$sql = "SELECT p.id, p.name, p.price, p.image, p.description, p.category, c.name AS category_name, p.created_at
        FROM products p
        LEFT JOIN categories c ON p.category = c.id";
if ($q !== '') {
    $sql .= ' WHERE name LIKE ? OR category LIKE ?';
}
$sql .= ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
$products = [];
if ($stmt = $conn->prepare($sql)) {
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stmt->bind_param('ssii', $like, $like, $perPage, $offset);
    } else {
        $stmt->bind_param('ii', $perPage, $offset);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) { $products[] = $row; }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý sản phẩm</title>
    <style>
        body { font-family: system-ui, Arial, sans-serif; margin:0; background:#f8f8f6; }
        header { padding:16px 24px; background:#222; color:#fff; display:flex; align-items:center; justify-content:space-between; }
        h1 { margin:0; font-size:20px; }
        a.button, button.button { background:#111; color:#fff; padding:8px 14px; border-radius:6px; text-decoration:none; font-size:14px; display:inline-block; }
        a.button:hover, button.button:hover { background:#333; }
        .toolbar { display:flex; gap:12px; align-items:center; margin:20px 24px 0; }
        .toolbar form { display:flex; gap:8px; }
        .toolbar input[type=text] { padding:8px 10px; border:1px solid #ccc; border-radius:6px; min-width:220px; }
        table { width:calc(100% - 48px); margin:16px 24px 40px; border-collapse:collapse; background:#fff; border:1px solid #e2e2e2; }        
        th, td { padding:10px 12px; border-bottom:1px solid #eee; text-align:left; font-size:13px; }
        th { background:#fafafa; font-weight:600; }
        tr:last-child td { border-bottom:none; }
        .image-cell img { width:60px; height:60px; object-fit:cover; border-radius:4px; border:1px solid #ddd; background:#f0f0f0; }
        .actions { display:flex; gap:8px; }
        .pill { display:inline-block; padding:4px 8px; font-size:12px; background:#eef; color:#225; border-radius:12px; }
        .pagination { display:flex; gap:6px; flex-wrap:wrap; padding:0 24px 30px; }
        .pagination a { padding:6px 10px; border:1px solid #ccc; border-radius:4px; text-decoration:none; color:#333; font-size:12px; }
        .pagination a.active { background:#222; color:#fff; border-color:#222; }
        .empty { margin:40px 24px; padding:50px 30px; text-align:center; background:#fff; border:1px dashed #ccc; border-radius:8px; color:#666; }
        @media (max-width:900px) {
            table, thead { font-size:12px; }
            th:nth-child(4), td:nth-child(4), th:nth-child(5), td:nth-child(5) { display:none; }
        }
    </style>
</head>
<body>
<header>
    <h1>Quản lý sản phẩm</h1>
    <nav>
        <a class="button" href="dashboard.php">Dashboard</a>
        <a class="button" href="themsanpham.php">+ Thêm sản phẩm</a>
    </nav>
</header>
<div class="toolbar">
    <form method="get" action="products.php">
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Tìm tên hoặc danh mục..." />
        <button class="button" type="submit">Tìm kiếm</button>
    </form>
</div>
<?php if($flash): ?>
    <div style="margin:10px 24px 0; padding:10px 14px; background:#e8f7ea; color:#256029; border:1px solid #b7e2bb; border-radius:6px; font-size:13px;">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>
<?php if (!$products): ?>
    <div class="empty">Chưa có sản phẩm nào (hoặc không tìm thấy). Hãy thêm sản phẩm mới.</div>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Tên</th>
            <th>Giá</th>
            <th>Danh mục</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= (int)$p['id'] ?></td>
                <td class="image-cell">
                    <?php
                        $imgTag = '<img src="../assets/images/placeholder-bag.svg" alt="placeholder" />';
                        if(!empty($p['image'])){
                            $name = $p['image'];
                            $uploadPath = __DIR__ . '/../uploads/' . $name;
                            $legacyPath = __DIR__ . '/../assets/images/' . $name;
                            if(is_file($uploadPath)){
                                $imgTag = '<img src="../uploads/'.htmlspecialchars($name).'" alt="'.htmlspecialchars($p['name']).'" />';
                            } elseif(is_file($legacyPath)) {
                                $imgTag = '<img src="../assets/images/'.htmlspecialchars($name).'" alt="'.htmlspecialchars($p['name']).'" />';
                            }
                        }
                        echo $imgTag;
                    ?>
                </td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= number_format((float)$p['price'], 0, ',', '.') ?>₫</td>
                <td><span class="pill"><?= htmlspecialchars($p['category_name'] ?? 'Không có') ?></span></td>
                <td><?= htmlspecialchars($p['created_at']) ?></td>
                <td>
                    <div class="actions">
                        <a class="button" href="editsanpham.php?id=<?= (int)$p['id'] ?>">Sửa</a>
                        <form method="POST" style="margin:0;" onsubmit="return confirm('Xóa sản phẩm này?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="delete_id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="button" style="background:#8a0000">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="<?= $i === $page ? 'active' : '' ?>" href="products.php?page=<?= $i ?>&q=<?= urlencode($q) ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
</body>
</html>
