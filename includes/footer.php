</main> <!-- KẾT THÚC NỘI DUNG TRANG -->

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-col">
            <h4>CẦN GIÚP ĐỠ?</h4>
            <ul>
                <li><a href="#">Kiểm tra trạng thái đơn hàng</a></li>
                <li><a href="#">Chính sách vận chuyển</a></li>
                <li><a href="#">Đổi / Trả hàng</a></li>
                <li><a href="#">Liên hệ hỗ trợ</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>VỀ CHÚNG TÔI</h4>
            <ul>
                <li><a href="#">Về thương hiệu</a></li>
                <li><a href="#">Câu chuyện Louver</a></li>
                <li><a href="#">Tuyển dụng</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>MUA SẮM NÀO</h4>
            <ul>
                <li><a href="#">Định vị cửa hàng</a></li>
                <li><a href="#">Quà tặng</a></li>
                <li><a href="#">Ưu đãi</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>PHÁP LÝ</h4>
            <ul>
                <li><a href="#">Điều khoản sử dụng</a></li>
                <li><a href="#">Chính sách bảo mật</a></li>
            </ul>
        </div>
        <div class="footer-col newsletter">
            <h4>ĐĂNG KÝ NHẬN TIN</h4>
            <p>Nhận các thông tin thời trang mới nhất.</p>
            <form method="post" action="#" onsubmit="event.preventDefault();alert('Đã đăng ký!');">
                <input type="email" placeholder="Email của bạn" required>
                <button>Đăng ký</button>
            </form>
        </div>
    </div>
    <div class="footer-bottom">© <?php echo date('Y'); ?> - Louver. All rights reserved.</div>
</footer>
<style>
.site-footer{background:#f5f3f0;margin-top:40px;font-family:'Times New Roman',serif;color:#111;}
.footer-inner{max-width:1320px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:40px;padding:50px 40px 30px;}
.footer-col h4{margin:0 0 16px;font-size:14px;letter-spacing:1px;font-weight:600;text-transform:uppercase;}
.footer-col ul{list-style:none;padding:0;margin:0;}
.footer-col ul li{margin-bottom:8px;}
.footer-col a{text-decoration:none;color:#444;font-size:13px;letter-spacing:.5px;}
.footer-col a:hover{text-decoration:underline;color:#000;}
.newsletter p{font-size:13px;margin:0 0 12px;color:#555;}
.newsletter form{display:flex;flex-direction:column;gap:10px;}
.newsletter input{padding:10px 14px;border:1px solid #ccc;background:#fff;font-size:13px;border-radius:4px;}
.newsletter button{padding:10px 16px;border:none;background:#111;color:#fff;font-size:12px;letter-spacing:1px;text-transform:uppercase;border-radius:4px;cursor:pointer;}
.newsletter button:hover{background:#333;}
.footer-bottom{text-align:center;padding:16px 0;font-size:12px;border-top:1px solid #e2ded9;letter-spacing:.5px;}
@media(max-width:600px){.footer-inner{padding:40px 24px 24px;gap:34px;} .footer-col h4{font-size:13px;} }
</style>

</body>
</html>
