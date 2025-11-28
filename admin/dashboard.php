<?php
require_once "../includes/admin_guard.php";
require_once "../config/db.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - LOUVER</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:0;background:#f5f5f4;color:#222;}
        header{background:#111;color:#fff;padding:14px 26px;display:flex;justify-content:space-between;align-items:center;}
        header a{color:#fff;text-decoration:none;font-size:13px;margin-left:18px;}
        .wrap{max-width:1200px;margin:30px auto;padding:0 30px;}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:22px;margin-top:30px;}
        .card{background:#fff;border:1px solid #e4e1dc;border-radius:10px;padding:20px;box-shadow:0 4px 14px rgba(0,0,0,.05);}
        .card h3{margin:0 0 12px;font-size:16px;letter-spacing:.5px;}
        .card a.btn{display:inline-block;margin-top:6px;background:#111;color:#fff;padding:8px 14px;font-size:12px;text-transform:uppercase;text-decoration:none;border-radius:4px;letter-spacing:1px;}
        .card a.btn:hover{background:#333;}
    </style>
</head>
<body>
<header>
    <div><strong>LOUVER Admin</strong></div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="themsanpham.php">Thêm sản phẩm</a>
        <a href="../public/index.php">Trang chủ</a>
        <a href="../public/logout.php">Đăng xuất</a>
    </nav>
</header>
<div class="wrap">
    <h1 style="margin:0;font-size:26px;">Chào <?= htmlspecialchars($_SESSION['user']) ?> (Admin)</h1>
    <p style="margin-top:8px;color:#555">Quản lý nội dung và sản phẩm của cửa hàng.</p>
    <div class="grid">
        <div class="card">
            <h3>Sản phẩm</h3>
            <p>Thêm, sửa, xóa sản phẩm.</p>
            <a class="btn" href="themsanpham.php">Quản lý</a>
        </div>
        <div class="card">
            <h3>Đơn hàng</h3>
            <p>Xem và xử lý đơn hàng (placeholder).</p>
            <a class="btn" href="#">Xem</a>
        </div>
        <div class="card">
            <h3>Người dùng</h3>
            <p>Quản lý tài khoản khách hàng (placeholder).</p>
            <a class="btn" href="#">Quản lý</a>
        </div>
        <div class="card">
            <h3>Báo cáo</h3>
            <p>Doanh thu / lượt xem (placeholder).</p>
            <a class="btn" href="#">Xem báo cáo</a>
        </div>
    </div>
</div>
</body>
</html>