<?php
// Kiểm tra xem biến $conn đã được tạo bởi file index.php chưa
if (!isset($conn)) {
    // Nếu $conn CHƯA TỒN TẠI (do bị chạy trực tiếp), 
    // thì file này sẽ tự mình gọi connect_db.php để tạo ra $conn
    include("connect_db.php");
}


// --- BẮT ĐẦU LOGIC PHÂN TRANG VÀ TRUY VẤN ---

// 1. Cài đặt phân trang
$limit = 6; // Số lượng phòng hiển thị mỗi trang
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1; // Trang hiện tại, mặc định là 1
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit; // Vị trí bắt đầu lấy

// 2. Lấy tổng số loại phòng
$count_result = $conn->query("SELECT COUNT(*) as total FROM loai_phong");
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// 3. Lấy dữ liệu phòng cho trang hiện tại
// SỬA LỖI Ở ĐÂY: Đổi 'ha.ten_file_anh' thành 'ha.url_hinh_anh'
$sql = "SELECT lp.*, 
               (SELECT ha.url_hinh_anh FROM hinh_anh_phong ha 
                WHERE ha.loai_phong_id = lp.loai_phong_id 
                LIMIT 1) as hinh_anh_dai_dien
        FROM loai_phong lp
        ORDER BY lp.loai_phong_id DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql); // Dòng 33 sẽ hết lỗi tại đây
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// --- KẾT THÚC LOGIC ---
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt phòng | Khách sạn ABC</title>
    <style>
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
        }

        
        .room-section {
            width: 90%;
            margin: 40px auto;
            max-width: 1200px;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 40px;
            color: #2c3e50;
        }

        .rooms {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .room-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .room-card:hover {
            transform: translateY(-5px);
        }

        .room-card img {
            width: 100%;
            height: 200px; /* Chiều cao cố định cho ảnh */
            object-fit: cover; /* Đảm bảo ảnh vừa vặn */
        }

        .room-info {
            padding: 20px;
        }

        .room-info h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 10px;
        }

        .room-info p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .book-btn {
            display: inline-block;
            background-color: #e67e22;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .book-btn:hover {
            background-color: #d35400;
        }

        /* DỊCH VỤ */
        .service-section {
            width: 90%;
            margin: 60px auto;
            max-width: 1200px;
        }

        .services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .service-card {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .service-card h4 {
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        /* === CSS MỚI CHO PHÂN TRANG === */
        .pagination {
            text-align: center;
            margin-top: 40px;
        }
        .pagination a {
            color: #2c3e50;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .pagination a.active {
            background-color: #2c3e50;
            color: white;
            border-color: #2c3e50;
        }
        .pagination a:hover:not(.active) {
            background-color: #f4f4f4;
        }

    </style>
</head>
<body>
    
    <div class="room-section">
        <h2 class="section-title">Các loại phòng của chúng tôi</h2>
        <div class="rooms">
            
            <?php
            if ($result->num_rows > 0) {
                // Bắt đầu vòng lặp để hiển thị các phòng
                while ($row = $result->fetch_assoc()) {
                    
                    // ===== PHẦN SỬA LỖI HIỂN THỊ ẢNH =====
                    // Tên cột 'url_hinh_anh' có thể đã chứa 'images/phong/don1.jpg'
                    // Chúng ta không cần thêm 'images/phong/' nữa.
                    $image_url = $row['hinh_anh_dai_dien'] ? htmlspecialchars($row['hinh_anh_dai_dien']) : 'images/phong/default.jpg';
                    // ======================================
                    
                    echo '<div class="room-card">';
                    echo '    <img src="' . $image_url . '" alt="' . htmlspecialchars($row['ten_loai']) . '">';
                    echo '    <div class="room-info">';
                    echo '        <h3>' . htmlspecialchars($row['ten_loai']) . '</h3>';
                    echo '        <p><strong>Giá:</strong> ' . number_format($row['gia_co_ban']) . ' VNĐ / đêm</p>';
                    echo '        <p><strong>Sức chứa:</strong> ' . $row['suc_chua_toi_da'] . ' người</p>';
                    // Cắt bớt mô tả nếu quá dài
                    $mo_ta_ngan = strlen($row['mo_ta']) > 100 ? substr($row['mo_ta'], 0, 100) . '...' : $row['mo_ta'];
                    echo '        <p>' . htmlspecialchars($mo_ta_ngan) . '</p>';
                    // Nút "Đặt ngay"
                    echo '        <a href="index.php?page=dat_phong_chi_tiet&loai_phong_id=' . $row['loai_phong_id'] . '" class="book-btn">Đặt ngay</a>';
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                echo '<p style="text-align: center; grid-column: 1 / -1;">Hiện tại chưa có loại phòng nào trong CSDL.</p>';
            }
            $stmt->close();
            ?>
            
        </div> <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="index.php?page=booking&p=<?php echo $page - 1; ?>">&laquo; Trang trước</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?page=booking&p=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="index.php?page=booking&p=<?php echo $page + 1; ?>">Trang tiếp &raquo;</a>
            <?php endif; ?>
        </div>
        
    </div> <div class="service-section">
        <h2 class="section-title">Dịch vụ kèm theo</h2>
        <div class="services">
            <div class="service-card">
                <h4>Hồ bơi</h4>
                <p>Miễn phí cho tất cả khách đặt phòng.</p>
            </div>
            <div class="service-card">
                <h4>Ăn sáng Buffet</h4>
                <p>Thưởng thức bữa sáng phong phú, đa dạng.</p>
            </div>
            <div class="service-card">
                <h4>Xe đưa đón</h4>
                <p>Đưa đón sân bay miễn phí cho khách VIP.</p>
            </div>
            <div class="service-card">
                <h4>Gym & Spa</h4>
                <p>Thư giãn với dịch vụ massage và phòng tập hiện đại.</p>
            </div>
        </div>
    </div>
</body>
</html>