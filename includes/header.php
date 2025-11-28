<?php
// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOUVER</title>
    <style>
        :root{--header-height:70px;--accent:#111;--bg:#ffffff;--text:#111;--muted:#6d6d6d;--gap:42px;--brand-letter-spacing:6px;--page-bg:#f9f7f4;}
        *{box-sizing:border-box;}
        body{margin:0;font-family: 'Times New Roman', serif;background:var(--page-bg);color:var(--text);}        
        a{text-decoration:none;color:var(--text);}
        header.louver-header{position:sticky;top:0;z-index:50;display:flex;align-items:center;justify-content:space-between;padding:0 46px;height:var(--header-height);background:var(--bg);border-bottom:1px solid #e6dfd7;font-size:13px;letter-spacing:.5px;font-weight:500;}
        .nav-left,.nav-right{display:flex;align-items:center;gap:28px;}
        .nav-left a{text-transform:uppercase;font-size:11px;letter-spacing:1px;position:relative;padding:4px 0;}
        .nav-left a::after{content:"";position:absolute;left:0;bottom:-6px;height:1px;width:0;background:#222;transition:.3s;}
        .nav-left a:hover::after{width:100%;}
        .brand{font-size:20px;font-weight:600;letter-spacing:var(--brand-letter-spacing);margin:0 auto;display:flex;align-items:center;justify-content:center;flex:1;}
        .icons a,.icons button{background:none;border:none;cursor:pointer;padding:4px;display:flex;align-items:center;justify-content:center;color:var(--text);}
        .icons svg{width:18px;height:18px;stroke-width:1.4;}
        .icons a:hover svg path,.icons button:hover svg path{stroke:#000;}
        .user-dropdown{position:relative;}
        .user-dropdown-menu{position:absolute;top:42px;right:0;background:#fff;border:1px solid #e5ddd4;border-radius:8px;min-width:160px;padding:10px 0;box-shadow:0 10px 24px rgba(0,0,0,.08);display:none;}
        .user-dropdown-menu a{display:block;padding:8px 18px;font-size:13px;}
        .user-dropdown-menu a:hover{background:#f7f2ed;}
        .user-dropdown.open .user-dropdown-menu{display:block;}
        @media(max-width:860px){header.louver-header{padding:0 20px;}.nav-left{display:none;}.brand{flex:none;margin:0;} .nav-right{gap:14px}}
    </style>
</head>
<body>
<header class="louver-header">
    <nav class="nav-left">
        <a href="/DoAnThuNa/public/index.php">HANDBAGS</a>
        <a href="/DoAnThuNa/public/index.php#accessories">ACCESSORIES</a>
        <a href="/DoAnThuNa/public/index.php#sale">SALE</a>
        <a href="/DoAnThuNa/public/index.php#contact">CONTACT</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
            <a href="/DoAnThuNa/admin/dashboard.php" style="color:#b22;font-weight:600;">ADMIN</a>
        <?php endif; ?>
    </nav>
    <h1 class="brand">L O U V E R</h1>
    <div class="nav-right icons">
        <a href="/DoAnThuNa/public/search.php" aria-label="Tìm kiếm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
        </a>
        <a href="/DoAnThuNa/public/wishlist.php" aria-label="Yêu thích">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 21s-6.5-4.35-9-9a5.5 5.5 0 0 1 9-6 5.5 5.5 0 0 1 9 6c-2.5 4.65-9 9-9 9z"/></svg>
        </a>
        <a href="/DoAnThuNa/public/cart.php" aria-label="Giỏ hàng">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4h2l3 12h10l3-8H6"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
        </a>
        <?php if(isset($_SESSION['user'])): ?>
            <div class="user-dropdown" id="userDropdown">
                <button type="button" aria-label="Tài khoản" onclick="document.getElementById('userDropdown').classList.toggle('open')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 12a5 5 0 1 0-0.001-10.001A5 5 0 0 0 12 12z"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></svg>
                </button>
                <div class="user-dropdown-menu">
                    <span style="display:block;padding:6px 18px;font-size:12px;color:var(--muted);">Xin chào <?= htmlspecialchars($_SESSION['user']) ?></span>
                    <a href="/DoAnThuNa/public/profile.php">Hồ sơ</a>
                    <a href="/DoAnThuNa/public/logout.php">Đăng xuất</a>
                </div>
            </div>
        <?php else: ?>
            <a href="/DoAnThuNa/public/login.php" style="font-size:12px;text-transform:uppercase;letter-spacing:1px;">Login</a>
        <?php endif; ?>
    </div>
</header>

<!-- BẮT ĐẦU NỘI DUNG TRANG -->
<main style="padding: 20px;">
