<?php
include("../config/db.php");
session_start();

$message = "";

if ($_POST) {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $stmt = $conn->prepare("SELECT username,password,role FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row["password"])) {
            $_SESSION["user"] = $row["username"];            
            $_SESSION["role"] = $row["role"] ?? 'user';
            // Chuyển admin vào dashboard
            if(($_SESSION['role'] ?? 'user') === 'admin'){
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $message = "Sai mật khẩu!";
        }
    } else {
        $message = "Tài khoản không tồn tại!";
    }
}
?>

<style>
    html,body{height:100%;margin:0;background:#f5efe8;}
    .login-container{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:40px;background:transparent}
    .login-card{width:960px;max-width:95%;display:flex;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.08);background:#fff}
    .login-left{flex:0 0 46%;background:linear-gradient(180deg,#f3e9df,#f7efe6);padding:46px 54px 50px;position:relative;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center}
    .brand-title{font-family:serif;font-size:50px;letter-spacing:2px;margin:0 0 10px 0;color:#12120f}
    .brand-sub{margin-top:8px;color:#7b6e63;font-style:italic}
    .bag-illustration{display:block;margin-top:28px;width:360px;max-width:82%;opacity:0.95;filter:drop-shadow(0 8px 16px rgba(0,0,0,0.06));}
    .login-right{flex:1;padding:50px 60px 50px 54px;display:flex;flex-direction:column;justify-content:center;background:#fff}
    .welcome{font-family:serif;font-size:24px;margin:0 0 18px 0;color:#111}
    .welcome strong{display:block;font-size:38px;margin-top:8px}
    .form-group{margin-bottom:14px}
    .form-input{width:100%;padding:14px 16px;border:1px solid #e3dcd6;border-radius:10px;background:#f5f7fb;font-size:15px}
    .checkbox-row{display:flex;align-items:center;gap:8px;color:#6b6158;margin-bottom:18px}
    .btn-primary{background:#0a0a0a;color:#fff;border:none;padding:14px 20px;border-radius:10px;font-weight:600;cursor:pointer;width:100%;font-size:15px}
    .helper{color:#8a8379;font-size:13px;margin-top:16px}
    .helper a{color:#111;text-decoration:underline}
    .message{margin-bottom:12px;padding:10px;border-radius:8px}
    .message.error{background:#ffecec;color:#b94a48}
    .message.success{background:#e8f7ea;color:#256029}
    @media(max-width:960px){.login-card{width:100%;flex-direction:column}.login-left{flex:unset;padding:40px 38px}.login-right{padding:44px 38px}.welcome strong{font-size:34px}.brand-title{font-size:44px}}
    @media(max-width:560px){.login-left{padding:42px 30px}.login-right{padding:42px 30px}.brand-title{font-size:40px}.welcome strong{font-size:30px}}
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-left">
            <div class="brand-block">
                <h1 class="brand-title">LOUVER</h1>
                <div class="brand-sub">Elegance in Every Detail</div>
            </div>
            <img src="../assets/Testlogin.png" alt="Túi thời trang" class="bag-illustration" loading="lazy">
        </div>

        <div class="login-right">
            <div class="welcome">Welcome Back to <strong>LOUVER</strong></div>

            <?php if($message != ""): ?>
                <div class="message error"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if(isset($_GET["status"]) && $_GET["status"] == "register_success"): ?>
                <div class="message success">Đăng ký thành công! Mời đăng nhập.</div>
            <?php endif; ?>

            <form method="POST" class="login-form" novalidate>
                <div class="form-group">
                    <input class="form-input" type="text" name="username" placeholder="Email Address" value="<?= isset($_POST['username'])?htmlspecialchars($_POST['username']):'' ?>" required>
                </div>

                <div class="form-group">
                    <input class="form-input" type="password" name="password" placeholder="Password" required>
                </div>

                <label class="checkbox-row"><input type="checkbox" name="remember"> Remember me</label>

                <button type="submit" class="btn-primary">Đăng nhập</button>

                <div class="helper">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></div>
                <div class="helper" style="margin-top:8px;font-size:12px;color:#b3aba3">Khi đăng ký, bạn đồng ý với Điều khoản &amp; Chính sách của LOUVER.</div>
            </form>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>

