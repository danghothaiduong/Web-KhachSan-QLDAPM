<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('connect_db.php'); // kết nối CSDL 1 lần duy nhất
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Hotel | Trang chủ</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images/logo.png">
</head>
<body>
    <!-- ===== HEADER ===== -->
    <header>
        <div class="container">
            <h1>Luxury Hotel</h1>
            <nav>
                <ul>
                    <li><a href="index.php?page=home">Trang chủ</a></li>
                    <li><a href="index.php?page=booking">Đặt phòng</a></li>
                    <li><a href="index.php?page=login" class="btn-login">Đăng nhập</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main>
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if ($page == 'login') {
                include('login.php');
            } elseif ($page == 'booking') {
                include('booking.php');
            } else {
                include('home.php'); // trang mặc định
            }
        } else {
            include('home.php');
        }
        ?>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer id="contact">
        <p>&copy; 2025 Luxury Hotel. Mọi quyền được bảo lưu.</p>
        <p>Địa chỉ: Phường Long Xuyên, An Giang | Điện thoại: <a href="tel:0123456789">0123 456 789</a></p>
        <p>Email: <a href="mailto:gmail@luxuryhotel.vn">gmail@luxuryhotel.vn</a></p>
    </footer>
</body>
</html>
