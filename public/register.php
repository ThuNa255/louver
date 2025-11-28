<?php
session_start();
require_once "../config/db.php"; // Kết nối database

$msg = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu & làm sạch
    $username = trim($_POST["username"] ?? "");
    $fullname = trim($_POST["fullname"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirm  = trim($_POST["confirm"] ?? "");

    // Validate cơ bản
    if ($username === "" || $fullname === "" || $email === "" || $password === "" || $confirm === "") {
        $msg = "Vui lòng nhập đầy đủ thông tin!";
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {
        $msg = "Username chỉ gồm chữ, số, '_' (3-20 ký tự)!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Email không hợp lệ!";
    } elseif (strlen($password) < 6) {
        $msg = "Mật khẩu tối thiểu 6 ký tự!";
    } elseif ($password !== $confirm) {
        $msg = "Mật khẩu nhập lại không trùng!";
    } else {
        // Kiểm tra trùng username hoặc email
        $check = $conn->prepare("SELECT username,email FROM users WHERE username = ? OR email = ? LIMIT 1");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['username'] === $username) {
                $msg = "Username đã tồn tại!";
            } elseif ($row['email'] === $email) {
                $msg = "Email đã tồn tại!";
            }
        } else {
            // Mã hoá mật khẩu
            $hash = password_hash($password, PASSWORD_BCRYPT);
            // Mặc định role = user
            $role = 'user';
            $stmt = $conn->prepare("INSERT INTO users(username, fullname, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $fullname, $email, $hash, $role);
            if ($stmt->execute()) {
                $msg = "Đăng ký thành công! Đang chuyển hướng...";
                $success = true;
            } else {
                $msg = "Lỗi hệ thống: " . htmlspecialchars($conn->error);
            }
        }
    }
}
?>

<style>
    html,body{height:100%;margin:0;background:#f5efe8;}
    .auth-container{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:40px;background:transparent}
    .auth-card{width:960px;max-width:95%;display:flex;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.08);background:#fff}
    .auth-left{flex:0 0 46%;background:linear-gradient(180deg,#f3e9df,#f7efe6);padding:46px 54px 50px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center}
    .brand-title{font-family:serif;font-size:50px;letter-spacing:2px;margin:0 0 10px 0;color:#12120f}
    .brand-sub{margin-top:8px;color:#7b6e63;font-style:italic}
    .brand-illustration{display:block;margin-top:28px;width:360px;max-width:82%;opacity:.95;filter:drop-shadow(0 8px 16px rgba(0,0,0,0.06));}
    .auth-right{flex:1;padding:50px 60px 50px 54px;display:flex;flex-direction:column;justify-content:center;background:#fff}
    .auth-heading{font-family:serif;font-size:24px;margin:0 0 18px;color:#111}
    .auth-heading strong{display:block;font-size:38px;margin-top:6px}
    form.auth-form{margin-top:4px}
    .form-group{margin-bottom:14px}
    .form-input{width:100%;padding:14px 16px;border:1px solid #e3dcd6;border-radius:10px;background:#f5f7fb;font-size:15px}
    .form-input:focus{outline:none;border-color:#c7bfb7;box-shadow:0 0 0 3px rgba(199,191,183,.25)}
    .btn-primary{background:#0a0a0a;color:#fff;border:none;padding:14px 20px;border-radius:10px;font-weight:600;cursor:pointer;width:100%;font-size:15px}
    .btn-primary:hover{background:#141414}
    .message{margin-bottom:14px;padding:12px 14px;border-radius:10px;font-size:13px}
    .message.error{background:#ffecec;color:#b94a48}
    .message.success{background:#e8f7ea;color:#256029}
    .helper{text-align:center;color:#8a8379;font-size:13px;margin-top:18px}
    .helper a{color:#111;text-decoration:underline}
    @media(max-width:960px){.auth-card{width:100%;flex-direction:column}.auth-left{flex:unset;padding:40px 38px}.auth-right{padding:44px 38px}.brand-title{font-size:44px}.auth-heading strong{font-size:34px}}
    @media(max-width:560px){.auth-left{padding:38px 30px}.auth-right{padding:38px 30px}.brand-title{font-size:40px}.auth-heading strong{font-size:30px}}
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-left">
            <div class="brand-block">
                <h1 class="brand-title">LOUVER</h1>
                <div class="brand-sub">Elegance in Every Detail</div>
            </div>
            <img src="../assets/Testlogin.png" alt="Túi thời trang" class="brand-illustration" loading="lazy">
        </div>

        <div class="auth-right">
            <div class="auth-heading">Tạo tài khoản mới <strong>LOUVER</strong></div>

            <?php if ($msg != ""): ?>
                <div class="message <?= $success?'success':'error' ?>"><?= $msg ?></div>
                <?php if ($success): ?>
                    <script>
                        // Chuyển qua trang đăng nhập sau 2 giây với query status
                        setTimeout(function(){
                            window.location.href = 'login.php?status=register_success';
                        },2000);
                    </script>
                <?php endif; ?>
            <?php endif; ?>

            <form method="POST" class="auth-form" novalidate>
                <div class="form-group">
                    <input type="text" name="username" class="form-input" placeholder="Username" value="<?= isset($_POST['username'])?htmlspecialchars($_POST['username']):'' ?>" required>
                </div>
                <div class="form-group">
                    <input type="text" name="fullname" class="form-input" placeholder="Họ tên" value="<?= isset($_POST['fullname'])?htmlspecialchars($_POST['fullname']):'' ?>" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Email" value="<?= isset($_POST['email'])?htmlspecialchars($_POST['email']):'' ?>" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Mật khẩu" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm" class="form-input" placeholder="Nhập lại mật khẩu" required>
                </div>
                <button type="submit" class="btn-primary">Đăng ký</button>
                <div class="helper">Đã có tài khoản? <a href="login.php">Đăng nhập</a></div>
                <div class="helper" style="margin-top:8px;font-size:12px;color:#b3aba3">Khi đăng ký, bạn đồng ý với Điều khoản &amp; Chính sách của LOUVER.</div>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
