<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('connect_db.php'); // Kết nối CSDL

// Kiểm tra nếu chưa đăng nhập thì quay lại trang đăng nhập
if (!isset($_SESSION['ten_dang_nhap'])) {
    header("Location: index.php?page=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Hotel | Quản trị</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images/logo.png">
    
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5; /* Thêm màu nền cho body */
        }
        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 250px;
            background-color: #1e3d59;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            flex-shrink: 0; /* Không co lại */
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            font-size: 15px;
            transition: background 0.3s;
        }
        .sidebar a:hover,
        .sidebar a.active { /* Thêm class active cho link được chọn */
            background-color: #16344a;
        }
        .logout-btn {
            background-color: #c62828;
            margin-top: auto;
            text-align: center;
        }
        .logout-btn:hover {
            background-color: #a62323 !important; /* Quan trọng */
        }

        /* ===== MAIN ===== */
        .main-content {
            flex: 1;
            padding: 0; /* Xóa padding cũ */
            display: flex;
            flex-direction: column;
        }
        /* Header của main content */
        .main-content .header {
            background-color: #002b5b; /* Lấy màu từ style.css */
            color: white;
            padding: 20px 40px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .main-content .header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        /* Vùng nội dung chính */
        .main-content .content {
            padding: 20px 40px; /* Di chuyển padding vào đây */
            flex: 1;
        }
        
        /* Footer (Nếu bạn muốn có) */
        footer {
            text-align: center;
            padding: 15px;
            color: #555;
            border-top: 1px solid #ddd;
            background-color: #fff;
        }
        hr { margin: 20px 0; }

        /* ===== CSS CHO TRANG CON (ĐÃ THÊM) ===== */
        .main-content .content table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .main-content .content table th,
        .main-content .content table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .main-content .content table th {
            background-color: #f4f4f4;
        }
        .main-content .content table td img.thumbnail { 
            width: 100px; height: auto; border-radius: 4px; 
        }
        .main-content .content table .btn-success,
        .main-content .content table .btn-danger {
            display: inline-block;
            margin-right: 5px;
            padding: 5px 10px;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
        }
        .main-content .content table .btn-success { background-color: #28a745; }
        .main-content .content table .btn-danger { background-color: #dc3545; }

        /* CSS cho Form */
        .form-container { 
            margin-bottom: 20px; border: 1px solid #ccc;
            background-color: #fff; padding: 20px; border-radius: 5px;
        }
        .form-container h3 {
            margin-top: 0; margin-bottom: 15px;
            border-bottom: 1px solid #eee; padding-bottom: 10px;
        }
        .form-container label { 
            display: block; margin-top: 10px; margin-bottom: 5px;
            font-weight: bold; 
        }
        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container input[type="file"],
        .form-container textarea { 
            width: 100%; padding: 10px; box-sizing: border-box; 
            border: 1px solid #ccc; border-radius: 4px; font-size: 14px;
        }
        .form-container textarea { min-height: 80px; }
        .form-container button { 
            background-color: #007bff; color: white; padding: 10px 15px; 
            border: none; border-radius: 4px; cursor: pointer; 
            margin-top: 15px; font-size: 16px;
        }
        .form-container a.button.cancel {
            display: inline-block; background-color: #6c757d; color: white;
            padding: 10px 15px; border: none; border-radius: 4px; 
            cursor: pointer; margin-top: 15px; text-decoration: none;
            font-size: 16px;
        }

        /* CSS cho Thông báo */
        .message { 
            padding: 15px; background-color: #d4edda; color: #155724; 
            border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 15px;
        }
        .message.error { 
            background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; 
        }

    </style>
</head>
<body>

    <?php
    // Xác định trang hiện tại để active link sidebar
    $currentPage = $_GET['page'] ?? 'dashboard'; // Mặc định là dashboard
    ?>

    <div class="sidebar">
        <h2>🛎️ Admin Panel</h2>
        <a href="admin_index.php" class="<?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>">📊 Bảng điều khiển</a>
        <a href="admin_index.php?page=users" class="<?php echo ($currentPage == 'users') ? 'active' : ''; ?>">👤 Người dùng</a>
        <a href="admin_index.php?page=loai_phong" class="<?php echo ($currentPage == 'loai_phong') ? 'active' : ''; ?>">🏨 Loại Phòng</a>
        <a href="admin_index.php?page=phong_chitiet" class="<?php echo ($currentPage == 'phong_chitiet') ? 'active' : ''; ?>">🏨 Phòng Chi Tiết</a>
        <a href="admin_index.php?page=bookings" class="<?php echo ($currentPage == 'bookings') ? 'active' : ''; ?>">🗓️ Đặt phòng</a>
        <a href="admin_index.php?page=services" class="<?php echo ($currentPage == 'services') ? 'active' : ''; ?>">🛎️ Dịch vụ</a>
        <a href="admin_index.php?page=bills" class="<?php echo ($currentPage == 'bills') ? 'active' : ''; ?>">💵 Hóa đơn</a>
        <a href="logout.php" class="logout-btn">🚪 Đăng xuất</a>
    </div>

    <div class="main-content">
        <header>
             <h1>
                <?php
                if ($currentPage == 'dashboard') { echo "📊 Bảng điều khiển"; }
                elseif ($currentPage == 'users') { echo "👤 Quản lý Người dùng"; }
                elseif ($currentPage == 'loai_phong') { echo "🏨 Quản lý Loại Phòng"; }
                elseif ($currentPage == 'phong_chitiet') { echo "🏨 Quản lý Phòng Chi Tiết"; }
                elseif ($currentPage == 'bookings') { echo "🗓️ Quản lý Đặt phòng"; }
                elseif ($currentPage == 'services') { echo "🛎️ Quản lý Dịch vụ"; }
                elseif ($currentPage == 'bills') { echo "💵 Quản lý Hóa đơn"; }
                else { echo "🛡️ Luxury Hotel - Quản trị"; }
                ?>
            </h1>
        </header>

        <div class="content">
            <?php
            // LOGIC TẢI TRANG CON
            if ($currentPage == 'loai_phong') {
                // Đổi 'phong' thành 'loai_phong'
                include('page_quanly_loai_phong.php'); 
            }
            elseif ($currentPage == 'phong_chitiet') {
                // Thêm trang mới
                include('page_quanly_phong_chitiet.php');
            }
            elseif ($currentPage == 'services') {
                // Tải file quản lý dịch vụ
                include('page_quanly_dich_vu.php');
            }
            elseif ($currentPage == 'users') {
                echo "<h2>Quản lý Người dùng</h2><p>Nội dung trang quản lý người dùng sẽ ở đây.</p>";
            }
            elseif ($currentPage == 'bookings') {
                include('booking.php');
                //echo "<h2>Quản lý Đặt phòng</h2><p>Nội dung trang quản lý tất cả đặt phòng sẽ ở đây.</p>";
            }
             elseif ($currentPage == 'bills') {
                include('admin_bills.php');
                //echo "<h2>Quản lý Hóa đơn</h2><p>Nội dung trang quản lý hóa đơn sẽ ở đây.</p>";
            }
            else {
                // Trang mặc định (dashboard)
                // Dán code "Xin chào" và "Bảng đặt phòng" vào đây
            ?>
                
                <section>
                    <h2>👋 Xin chào, <?php echo htmlspecialchars($_SESSION['ten_dang_nhap']); ?>!</h2>
                    <p>Chào mừng bạn đến với khu vực quản trị hệ thống Luxury Hotel.</p>
                    <hr>
                </section>
            
                <div class="booking-management">
                    <h2>Danh sách đặt phòng mới (Chờ xác nhận)</h2>
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên khách hàng</th>
                                <th>Phòng</th>
                                <th>Ngày nhận</th>
                                <th>Ngày trả</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // ĐÃ SỬA SQL QUERY ĐỂ CHẠY ĐÚNG VỚI qldapm.sql
                            $sql = "SELECT dp.dat_phong_id, nd.ten_nguoi_dung, p.so_phong, 
                                           dp.ngay_nhan_phong, dp.ngay_tra_phong, dp.trang_thai_dat_phong 
                                    FROM dat_phong dp
                                    JOIN nguoi_dung nd ON dp.nguoi_dung_id = nd.nguoi_dung_id
                                    JOIN phong p ON dp.phong_id = p.phong_id
                                    WHERE dp.trang_thai_dat_phong = 'ChoXacNhan'
                                    ORDER BY dp.dat_phong_id DESC
                                    LIMIT 10";
                                    
                            $result = $conn->query($sql);
                            
                            if ($result && $result->num_rows > 0) {
                                while($booking = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>".$booking['dat_phong_id']."</td>";
                                    echo "<td>".htmlspecialchars($booking['ten_nguoi_dung'])."</td>";
                                    echo "<td>".htmlspecialchars($booking['so_phong'])."</td>";
                                    echo "<td>".$booking['ngay_nhan_phong']."</td>";
                                    echo "<td>".$booking['ngay_tra_phong']."</td>";
                                    echo "<td>".htmlspecialchars($booking['trang_thai_dat_phong'])."</td>";
                                    echo "<td>
                                        <a href='admin_index.php?page=bookings&action=approve&id=".$booking['dat_phong_id']."' class='btn btn-success'>Duyệt</a>
                                        <a href='admin_index.php?page=bookings&action=reject&id=".$booking['dat_phong_id']."' class='btn btn-danger'>Từ chối</a>
                                    </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' style='text-align: center;'>Không có đặt phòng mới nào.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
            } // Kết thúc else (trang mặc định)
            ?>
        </div> </div> </body>
</html>
<?php
$conn->close(); // Đóng kết nối ở cuối file
?>