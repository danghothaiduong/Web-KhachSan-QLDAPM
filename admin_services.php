<?php
//session_start();
include('connect_db.php'); // Kết nối CSDL

// Khai báo mảng chứa dữ liệu dịch vụ
$dichvu = [];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;
$error_message = '';
$success_message = '';
$service_to_edit = null; // Biến lưu dữ liệu dịch vụ đang sửa

// Kiểm tra nếu chưa đăng nhập thì quay lại trang đăng nhập
if (!isset($_SESSION['ten_dang_nhap'])) {
    // header("Location: index.php?page=login");
    // exit();
}


// I. XỬ LÝ SỬA DỊCH VỤ (POST FORM)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_service'])) {
    $edit_id = mysqli_real_escape_string($conn, $_POST['dich_vu_id']);
    $ten_dich_vu = mysqli_real_escape_string($conn, $_POST['ten_dich_vu']);
    $gia = mysqli_real_escape_string($conn, $_POST['gia']);
    $don_vi = mysqli_real_escape_string($conn, $_POST['don_vi']);

    $sql_update = "UPDATE dich_vu SET 
                   ten_dich_vu = '$ten_dich_vu', 
                   gia = '$gia', 
                   don_vi = '$don_vi' 
                   WHERE dich_vu_id = '$edit_id'";
    
    if (mysqli_query($conn, $sql_update)) {
        $success_message = "Cập nhật dịch vụ thành công!";
        // Sau khi sửa thành công, chuyển hướng để loại bỏ tham số action và form
        header("Location: admin_services.php?msg=" . urlencode($success_message));
        exit();
    } else {
        $error_message = "Lỗi khi cập nhật dịch vụ: " . mysqli_error($conn);
    }
}

// II. XỬ LÝ XÓA DỊCH VỤ (GET ACTION)
// -------------------------------------------------------------
if ($action === 'xoa' && $id) {
    // Thêm cảnh báo xác nhận trước khi xóa (Optional - nên dùng JavaScript)
    $sql_delete = "DELETE FROM dich_vu WHERE dich_vu_id = " . mysqli_real_escape_string($conn, $id);

    if (mysqli_query($conn, $sql_delete)) {
        $success_message = "Xóa dịch vụ ID: {$id} thành công!";
    } else {
        $error_message = "Lỗi khi xóa dịch vụ: " . mysqli_error($conn);
    }
    // Chuyển hướng để xóa tham số action khỏi URL
    header("Location: admin_services.php?msg=" . urlencode($success_message));
    exit();
}


// III. CHUẨN BỊ DỮ LIỆU CHO FORM SỬA (GET ACTION)
// -------------------------------------------------------------
if ($action === 'sua' && $id) {
    $sql_select_one = "SELECT * FROM dich_vu WHERE dich_vu_id = " . mysqli_real_escape_string($conn, $id);
    $result_one = mysqli_query($conn, $sql_select_one);
    
    if ($result_one && mysqli_num_rows($result_one) === 1) {
        $service_to_edit = mysqli_fetch_assoc($result_one);
    } else {
        $error_message = "Không tìm thấy dịch vụ cần sửa.";
        // Tắt chế độ sửa nếu không tìm thấy ID hợp lệ
        $action = ''; 
        $id = null;
    }
}

// V. XỬ LÝ THÊM DỊCH VỤ (POST FORM)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    // Lấy và làm sạch dữ liệu
    $ten_dich_vu_moi = mysqli_real_escape_string($conn, $_POST['ten_dich_vu_moi']);
    $gia_moi = mysqli_real_escape_string($conn, $_POST['gia_moi']);
    $don_vi_moi = mysqli_real_escape_string($conn, $_POST['don_vi_moi']);

    // Kiểm tra dữ liệu bắt buộc (ví dụ: Tên dịch vụ không rỗng)
    if (empty($ten_dich_vu_moi)) {
        $error_message = "Tên dịch vụ không được để trống!";
    } else {
        // Thực hiện truy vấn INSERT
        $sql_insert = "INSERT INTO dich_vu (ten_dich_vu, gia, don_vi) 
                       VALUES ('$ten_dich_vu_moi', '$gia_moi', '$don_vi_moi')";

        if (mysqli_query($conn, $sql_insert)) {
            $success_message = "Thêm dịch vụ **{$ten_dich_vu_moi}** thành công!";
            // Chuyển hướng để xóa dữ liệu POST và hiển thị thông báo
            header("Location: admin_services.php?msg=" . urlencode($success_message));
            exit();
        } else {
            $error_message = "Lỗi khi thêm dịch vụ: " . mysqli_error($conn);
        }
    }
}

// IV. TRUY VẤN DANH SÁCH DỊCH VỤ HIỂN THỊ TRONG BẢNG
// -------------------------------------------------------------
$sql = "SELECT * FROM dich_vu ORDER BY dich_vu_id ASC";
$result = mysqli_query($conn, $sql);

// Đưa dữ liệu vào mảng
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $dichvu[] = $row;
    }
}

// Lấy thông báo từ URL sau khi chuyển hướng
if (isset($_GET['msg'])) {
    $success_message = htmlspecialchars($_GET['msg']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Hotel | Quản trị dịch vụ</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .sidebar {
            width: 250px;
            background-color: #1e3d59;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
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
        .sidebar a:hover {
            background-color: #16344a;
        }
        .logout-btn {
            background-color: #c62828;
            margin-top: auto;
            text-align: center;
            padding: 12px;
        }
        .main-content {
            flex: 1;
            padding: 30px;
        }
        h2 {
            color: #1e3d59;
            margin-bottom: 20px;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #1e3d59;
            color: white;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .action {
            padding: 6px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            margin: 2px;
            display: inline-block;
        }
        .edit { background-color: orange; }
        .delete { background-color: red; }
        .action:hover { opacity: 0.8; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>🛎️ Admin Panel</h2>
    <a href="index_admin.php?page=dashboard">📊 Bảng điều khiển</a>
    <a href="index_admin.php?page=users">👤 Người dùng</a>
    <a href="index_admin.php?page=rooms">🏨 Phòng</a>
    <a href="index_admin.php?page=bookings">🗓️ Đặt phòng</a>
    <a href="admin_services.php">🛎️ Dịch vụ</a>
    <a href="index_admin.php?page=bills">💵 Hóa đơn</a>
    <a href="logout.php" class="logout-btn">🚪 Đăng xuất</a>
</div>

<div class="main-content">
    <?php if ($error_message): ?>
    <div style="padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 20px;">
        ⚠️ <?= $error_message; ?>
    </div>
<?php endif; ?>

<?php if ($success_message): ?>
    <div style="padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 20px;">
        ✅ <?= $success_message; ?>
    </div>
<?php endif; ?>

<h3 style="color: #1e3d59; margin-bottom: 15px;">➕ Thêm Dịch Vụ Mới</h3>
<form method="POST" action="admin_services.php" style="background: white; padding: 25px; margin-bottom: 30px; border-radius: 8px; box-shadow: 0 1px 5px rgba(0,0,0,0.1);">
    <input type="hidden" name="add_service" value="1">
    
    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
        <div style="flex: 3;">
            <label for="ten_dich_vu_moi" style="display: block; font-weight: bold; margin-bottom: 5px;">Tên Dịch Vụ:</label>
            <input type="text" name="ten_dich_vu_moi" required 
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="flex: 1.5;">
            <label for="gia_moi" style="display: block; font-weight: bold; margin-bottom: 5px;">Giá (VNĐ):</label>
            <input type="number" name="gia_moi" value="0" required 
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="flex: 1.5;">
            <label for="don_vi_moi" style="display: block; font-weight: bold; margin-bottom: 5px;">Đơn Vị:</label>
            <input type="text" name="don_vi_moi" required placeholder="/lượt, /ngày, /kg..."
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>
    </div>

    <button type="submit" style="background-color: #00b894; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
        Thêm Dịch Vụ
    </button>
</form>

<?php if ($action === 'sua' && $service_to_edit): ?>
    <h3 style="color: #007bff; margin-bottom: 15px;">Chỉnh Sửa Dịch Vụ ID: <?= $service_to_edit['dich_vu_id']; ?></h3>
    <form method="POST" action="admin_services.php" style="background: white; padding: 20px; margin-bottom: 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <input type="hidden" name="dich_vu_id" value="<?= $service_to_edit['dich_vu_id']; ?>">
        <input type="hidden" name="update_service" value="1">
        
        <div style="margin-bottom: 15px;">
            <label for="ten_dich_vu">Tên Dịch Vụ:</label>
            <input type="text" name="ten_dich_vu" value="<?= htmlspecialchars($service_to_edit['ten_dich_vu']); ?>" required 
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="gia">Giá (VNĐ):</label>
            <input type="number" name="gia" value="<?= htmlspecialchars($service_to_edit['gia']); ?>" required 
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="don_vi">Đơn Vị:</label>
            <input type="text" name="don_vi" value="<?= htmlspecialchars($service_to_edit['don_vi']); ?>" required 
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>

        <button type="submit" style="background-color: #1e3d59; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            Cập Nhật Dịch Vụ
        </button>
        <a href="admin_services.php" style="color: #555; margin-left: 10px; text-decoration: none;">Hủy</a>
    </form>
<?php endif; ?>
    <h2>Danh sách dịch vụ khách sạn</h2>

    <table>
        <thead>
            <tr>
                <th>Mã Dịch Vụ</th>
                <th>Tên Dịch Vụ</th>
                <th>Giá (VNĐ)</th>
                <th>Đơn Vị</th>
                <th>Chức Năng</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($dichvu)): ?>
            <?php foreach ($dichvu as $dv): ?>
            <tr>
                <td><?= htmlspecialchars($dv['dich_vu_id']); ?></td>
                <td><?= htmlspecialchars($dv['ten_dich_vu']); ?></td>
                <td><?= number_format($dv['gia'], 0, ',', '.'); ?></td>
                <td><?= htmlspecialchars($dv['don_vi']); ?></td>
                <td>
                    <a href="?action=sua&id=<?= $dv['dich_vu_id']; ?>" class="action edit">Sửa</a>
                    <a href="?action=xoa&id=<?= $dv['dich_vu_id']; ?>" class="action delete" onclick="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này không?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">Không có dịch vụ nào.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    
</div>

</body>
</html>
