<?php
require_once "../config/db.php";
require_once "../includes/admin_guard.php"; // Chỉ admin mới vào

// CSRF token
if(session_status()===PHP_SESSION_NONE){session_start();}
if(empty($_SESSION['csrf_admin'])){ $_SESSION['csrf_admin'] = bin2hex(random_bytes(16)); }

// Lấy danh sách danh mục từ bảng categories (chuẩn) thay vì DISTINCT products
$categories = [];
$catQuery = $conn->query("SELECT name FROM categories ORDER BY name ASC");
if($catQuery){
    while($row = $catQuery->fetch_assoc()){
        $c = trim($row['name']);
        if($c !== ''){ $categories[] = $c; }
    }
}

$errors = [];
$successMsg = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!hash_equals($_SESSION['csrf_admin'], $_POST['csrf_token'] ?? '')){
        $errors[] = 'Token không hợp lệ, vui lòng tải lại trang.';
    } else {
        $name  = trim($_POST['name'] ?? '');
        $priceRaw = trim($_POST['price'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');

        if($name===''){ $errors[] = 'Tên sản phẩm bắt buộc.'; }
        if($priceRaw===''){ $errors[] = 'Giá bắt buộc.'; }
        if($priceRaw!=='' && !ctype_digit($priceRaw)){ $errors[] = 'Giá phải là số nguyên dương.'; }
        $price = (int)$priceRaw;
        if($price < 0){ $errors[] = 'Giá không được âm.'; }
        if(empty($categories)) {
            $errors[] = 'Chưa có danh mục nào trong bảng categories. Vui lòng tạo trước.';
        } else {
            if($category===''){ $errors[] = 'Danh mục bắt buộc.'; }
            elseif(!in_array($category, $categories)) { $errors[] = 'Danh mục không hợp lệ.'; }
        }

        $imgName='';
        if(isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
            $uploadErr = $_FILES['image']['error'];
            if($uploadErr !== UPLOAD_ERR_OK){
                $errors[] = 'Lỗi upload (mã '.$uploadErr.')';
            } else {
                $orig = $_FILES['image']['name'] ?? '';
                $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','webp','gif','svg'];
                if(!in_array($ext,$allowed)){
                    $errors[] = 'Định dạng ảnh không hợp lệ.';
                } else {
                    $imgName = 'p_'.time().'_'.preg_replace('/[^a-z0-9]+/i','-', pathinfo($orig, PATHINFO_FILENAME)).'.'.$ext;
                    $target = "../uploads/".$imgName;
                    if(!is_uploaded_file($_FILES['image']['tmp_name'])){
                        $errors[] = 'Không nhận được file hợp lệ.';
                    } elseif(!move_uploaded_file($_FILES['image']['tmp_name'], $target)){
                        $errors[] = 'Tải lên ảnh thất bại (không thể ghi vào uploads).';
                    }
                }
            }
        }

        if(empty($errors)){
            $stmt = $conn->prepare("INSERT INTO products(name, price, image, description, category) VALUES(?,?,?,?,?)");
            $stmt->bind_param('sisss', $name, $price, $imgName, $desc, $category);
            if($stmt->execute()){
                $successMsg = 'Thêm sản phẩm thành công!';
                // Reset form data sau khi thêm để tránh repost
                $_POST = [];
            } else {
                $errors[] = 'Lỗi CSDL: '.htmlspecialchars($conn->error);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm - Admin</title>
    <style>
        :root {
        --bg: #f4f5f7;
        --card-bg: #ffffff;
        --border: #e1e1e1;
        --radius: 16px;
        --accent: #111;
        --accent-hover: #333;
        --shadow: 0 8px 24px -6px rgba(0,0,0,0.08);
        --field-bg: #fafafa;
        --focus: #222;
    }

    * {
        box-sizing: border-box;
        font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    }

    body {
        margin: 0;
        background: var(--bg);
        color: #1d1d1d;
    }

    .container {
        max-width: 780px; /* smaller container */
        margin: 26px auto;
        padding: 0 18px;
    }

    h1 {
        font-size: 20px;
        margin: 0 0 18px;
        font-weight: 700;
        letter-spacing: -.2px;
    }

    .card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 18px 18px; /* tighter padding */
        box-shadow: 0 6px 16px -6px rgba(0,0,0,0.06);
        border: 1px solid var(--border);

        display: grid;
        grid-template-columns: 1fr; /* form full width */
        gap: 18px;
    }

    @media(max-width: 900px) {
        .card {
            grid-template-columns: 1fr;
            padding: 24px;
        }
    }

    /* FORM LAYOUT */
    .form-fields {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
    }

    .field label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #444;
    }

    input[type=text],
    input[type=number],
    select,
    textarea {
        width: 100%;
        padding: 10px 12px; /* smaller inputs */
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--field-bg);
        font-size: 13px;
        transition: .12s;
    }

    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: var(--focus);
        box-shadow: 0 0 0 3px rgba(0,0,0,0.15);
    }

    textarea {
        min-height: 110px;
        resize: vertical;
    }

    /* IMAGE PANEL */
    .image-panel {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .drop-area {
        padding: 14px;
        border-radius: 10px;
        border: 2px dashed #d0d0d0;
        background: #fafafa;
        text-align: center;
        min-height: 180px; /* smaller preview area */
        cursor: pointer;
        transition: .15s;
    }

    .drop-area:hover {
        background: #f0efef;
    }

    .drop-area.drag {
        background: #f5f2ef;
        border-color: #aaa;
    }

    .drop-area strong {
        font-size: 14px;
        color: #222;
    }

    .preview-wrapper {
        width: 100%;
        max-width: 320px; /* limit preview width */
        aspect-ratio: 1/1;
        border-radius: 10px;
        border: 1px solid #ddd;
        background: #f3f3f3;

        display: flex;
        align-items: center;
        justify-content: center;

        overflow: hidden;
        font-size: 12px;
        color: #666;
        margin: 0 auto; /* center smaller preview */
    }

    .preview-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* ACTIONS */
    .actions {
        margin-top: 18px;
        display: flex;
        gap: 10px;
    }

    button.submit-btn,
    button.secondary-btn {
        padding: 10px 14px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .6px;
        text-transform: uppercase;
    }

    button.submit-btn {
        flex: 1;
        background: var(--accent);
        color: #fff;
    }

    button.submit-btn:hover {
        background: var(--accent-hover);
    }

    button.secondary-btn {
        background: #ececec;
    }

    button.secondary-btn:hover {
        background: #e0e0e0;
    }

    /* MESSAGES */
    .messages .msg {
        margin-bottom: 12px;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .msg.error {
        background: #ffe4e4;
        color: #a10000;
    }

    .msg.success {
        background: #eaf9ec;
        color: #1c6c27;
    }

    /* Back link */
    a.back-link {
        font-size: 14px;
        color: #333;
        text-decoration: none;
        margin-bottom: 20px;
        display: inline-flex;
        gap: 8px;
    }

    a.back-link:hover {
        text-decoration: underline;
    }

    </style>
</head>
<body>
<div class="container">
    <a class="back-link" href="products.php">← Danh sách sản phẩm</a>
    <h1>Thêm sản phẩm mới</h1>
    <div class="messages">
        <?php foreach($errors as $e): ?>
            <div class="msg error"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
        <?php if($successMsg): ?><div class="msg success"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
    </div>
    <div class="card">
        <form method="POST" enctype="multipart/form-data" novalidate id="productForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_admin']) ?>">
            <div class="form-fields">
                <div class="field" style="grid-column:1/-1;">
                    <label for="name">Tên sản phẩm</label>
                    <input id="name" name="name" type="text" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="Nhập tên sản phẩm">
                </div>
                <div class="field">
                    <label for="price">Giá (VND)</label>
                    <input id="price" name="price" type="number" min="0" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" placeholder="0">
                    <div class="price-display" id="priceDisplay"></div>
                </div>
                <div class="field">
                    <label for="category">Danh mục</label>
                    <?php if(!empty($categories)): ?>
                        <select id="category" name="category" required style="appearance:none;">
                            <option value="">— Chọn danh mục —</option>
                            <?php foreach($categories as $c): ?>
                                <option value="<?= htmlspecialchars($c) ?>" <?= (($_POST['category'] ?? '') === $c ? 'selected' : '') ?>><?= htmlspecialchars($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <select id="category" name="category" disabled>
                            <option>Chưa có danh mục</option>
                        </select>
                        <small class="helper">Chưa có dữ liệu danh mục trong bảng sản phẩm. Hãy thêm một sản phẩm tạm bằng cách bật nhập danh mục hoặc tạo bảng categories riêng.</small>
                    <?php endif; ?>
                </div>
                <div class="field" style="grid-column:1/-1;">
                    <label for="description">Mô tả</label>
                    <textarea id="description" name="description" placeholder="Mô tả ngắn gọn, chất liệu, kích thước..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    <small class="helper">Có thể để trống, sau này bổ sung thêm.</small>
                </div>
            </div>

            <div class="image-panel">
                <div class="drop-area" id="dropArea">
                    <strong>Kéo & thả ảnh</strong>
                    <span style="font-size:12px;color:#666;">hoặc nhấp để chọn</span>
                    <input type="file" name="image" accept="image/*" id="imageInput">
                    <div class="preview-wrapper" id="imgPreview">Chưa chọn ảnh</div>
                </div>
                <small style="font-size:11px;color:#777;">Dung lượng khuyến nghị &lt; 2MB. Định dạng: jpg, png, webp, gif, svg.</small>
            </div>                 

            <div class="actions">
                <button class="submit-btn" type="submit" <?= empty($categories)?'disabled style="background:#999;cursor:not-allowed;"':'' ?>>Lưu sản phẩm</button>
                <button class="secondary-btn" type="reset" id="resetBtn">Xóa dữ liệu</button>
            </div>
        </form>
        
    </div>
    <?php if($successMsg): ?>
        <div style="margin-top:20px; text-align:center;">
            <a href="products.php" style="display:inline-block;padding:10px 18px;background:#222;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;">Xem danh sách sản phẩm</a>
        </div>
    <?php endif; ?>
</div>
<script>
// Image preview + drag-drop
const fileInput = document.getElementById('imageInput');
const preview = document.getElementById('imgPreview');
const dropArea = document.getElementById('dropArea');
function updatePreview(f){
  if(!f){preview.innerHTML='Chưa chọn ảnh';return;}
  const url = URL.createObjectURL(f);
  preview.innerHTML='<img src="'+url+'" alt="preview">';
}
fileInput?.addEventListener('change',()=>{ updatePreview(fileInput.files?.[0]); });
['dragenter','dragover'].forEach(ev=>dropArea.addEventListener(ev,e=>{e.preventDefault();dropArea.classList.add('drag');}));
['dragleave','drop'].forEach(ev=>dropArea.addEventListener(ev,e=>{e.preventDefault();if(ev==='drop'){const dt=e.dataTransfer; if(dt?.files?.[0]){fileInput.files=dt.files; updatePreview(dt.files[0]);}}dropArea.classList.remove('drag');}));
dropArea.addEventListener('click',()=>fileInput.click());

// Price formatting display
const priceInput = document.getElementById('price');
const priceDisplay = document.getElementById('priceDisplay');
function formatVN(num){return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.');}
function updatePrice(){const v=priceInput.value; if(!v){priceDisplay.textContent='';return;} priceDisplay.textContent='Hiển thị: '+formatVN(v)+' ₫';}
priceInput?.addEventListener('input',updatePrice); updatePrice();

// Reset preview on form reset
document.getElementById('resetBtn')?.addEventListener('click',()=>{preview.innerHTML='Chưa chọn ảnh'; priceDisplay.textContent='';});
</script>
</body>
</html>
