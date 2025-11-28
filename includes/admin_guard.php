<?php
// Bảo vệ trang quản trị: chỉ cho phép role = admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? 'user') !== 'admin') {
    // Có thể chuyển hướng về trang login hoặc báo lỗi 403
    header('HTTP/1.1 403 Forbidden');
    echo '<div style="font-family:Arial;padding:40px;text-align:center;">'
       .'<h2>403 - Không có quyền truy cập</h2>'
       .'<p>Tài khoản của bạn không phải quản trị. <a href="/DoAnThuNa/public/login.php">Đăng nhập tài khoản admin</a></p>'
       .'</div>';
    exit;
}
?>