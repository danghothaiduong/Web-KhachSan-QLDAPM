<?php
// 1. KẾT NỐI DATABASE
if (!isset($conn)) {
    require_once 'connect_db.php';
}

// KHỞI TẠO BIẾN
$loai_phong_id = "";
$so_phong = "";
$tang = "";
$trang_thai = "Available"; // Mặc định
$phong_id_update = 0;
$is_editing = false;
$message = "";

// URL cơ sở cho trang này
$base_url = "admin_index.php?page=phong_chitiet";

// 2. XỬ LÝ FORM (THÊM / CẬP NHẬT)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_phong_chitiet'])) {
    $loai_phong_id = $_POST['loai_phong_id'];
    $so_phong = $_POST['so_phong'];
    $tang = $_POST['tang'];
    $trang_thai = $_POST['trang_thai'];
    
    if (isset($_POST['phong_id_update']) && $_POST['phong_id_update'] > 0) {
        // ----- CẬP NHẬT (UPDATE) -----
        $phong_id = $_POST['phong_id_update'];
        $sql = "UPDATE phong SET loai_phong_id = ?, so_phong = ?, tang = ?, trang_thai = ? WHERE phong_id = ?";
        $stmt = $conn->prepare($sql);
        // i: integer, s: string, s: string, s: string, i: integer
        $stmt->bind_param("isssi", $loai_phong_id, $so_phong, $tang, $trang_thai, $phong_id);
        
        if ($stmt->execute()) {
            $message = "Cập nhật phòng thành công!";
        } else {
            $message = "Lỗi khi cập nhật: " . $stmt->error;
        }
        $stmt->close();
        
        // Reset biến
        $is_editing = false; $phong_id_update = 0;
        $loai_phong_id = ""; $so_phong = ""; $tang = ""; $trang_thai = "Available";
        
    } else {
        // ----- THÊM MỚI (CREATE) -----
        $sql = "INSERT INTO phong (loai_phong_id, so_phong, tang, trang_thai) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $loai_phong_id, $so_phong, $tang, $trang_thai);
        
        if ($stmt->execute()) {
            $message = "Thêm phòng mới thành công!";
            $loai_phong_id = ""; $so_phong = ""; $tang = ""; $trang_thai = "Available"; // Reset form
        } else {
            $message = "Lỗi khi thêm mới: " . $stmt->error . " (Có thể bị trùng 'Số phòng')";
        }
        $stmt->close();
    }
}

// 3. XỬ LÝ XÓA (DELETE)
if (isset($_GET['action']) && $_GET['action'] == 'delete_p' && isset($_GET['id'])) {
    $phong_id = $_GET['id'];
    $sql = "DELETE FROM phong WHERE phong_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $phong_id);
    
    if ($stmt->execute()) {
        $message = "Xóa phòng thành công!";
    } else {
        $message = "Lỗi khi xóa: " . $stmt->error . ". (Phòng này có thể đã có người đặt).";
    }
    $stmt->close();
}

// 4. XỬ LÝ SỬA (LẤY THÔNG TIN)
if (isset($_GET['action']) && $_GET['action'] == 'edit_p' && isset($_GET['id'])) {
    $phong_id_update = $_GET['id'];
    $is_editing = true;
    
    $sql = "SELECT * FROM phong WHERE phong_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $phong_id_update);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $loai_phong_id = $row['loai_phong_id'];
        $so_phong = $row['so_phong'];
        $tang = $row['tang'];
        $trang_thai = $row['trang_thai'];
    }
    $stmt->close();
}

// 5. LẤY DỮ LIỆU ĐỂ HIỂN THỊ (READ)
// Lấy danh sách phòng (JOIN để lấy tên loại phòng)
$sql_select = "SELECT p.*, lp.ten_loai 
               FROM phong p
               JOIN loai_phong lp ON p.loai_phong_id = lp.loai_phong_id
               ORDER BY p.so_phong ASC";
$result_select = $conn->query($sql_select);

// Lấy danh sách loại phòng cho dropdown (QUAN TRỌNG)
$result_loai_phong = $conn->query("SELECT loai_phong_id, ten_loai FROM loai_phong");

?>

<div class="container-content">

    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'Lỗi') !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <h3><?php echo $is_editing ? 'Chỉnh sửa Phòng' : 'Thêm Phòng Mới'; ?> (101, 102...)</h3>
        <form action="<?php echo $base_url; ?>" method="POST">
            
            <input type="hidden" name="form_phong_chitiet" value="1">
            <input type="hidden" name="phong_id_update" value="<?php echo $phong_id_update; ?>">

            <div>
                <label for="loai_phong_id">Loại phòng:</label>
                <select id="loai_phong_id" name="loai_phong_id" required>
                    <option value="">-- Chọn loại phòng --</option>
                    <?php
                    if ($result_loai_phong->num_rows > 0) {
                        while ($lp_row = $result_loai_phong->fetch_assoc()) {
                            // So sánh để chọn 'selected'
                            $selected = ($lp_row['loai_phong_id'] == $loai_phong_id) ? 'selected' : '';
                            echo "<option value='{$lp_row['loai_phong_id']}' $selected>" . htmlspecialchars($lp_row['ten_loai']) . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>Vui lòng thêm Loại Phòng trước</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="so_phong">Số phòng (ví dụ: 101, 203, VIP01):</label>
                <input type="text" id="so_phong" name="so_phong" value="<?php echo htmlspecialchars($so_phong); ?>" required>
            </div>
            <div>
                <label for="tang">Tầng:</label>
                <input type="number" id="tang" name="tang" value="<?php echo htmlspecialchars($tang); ?>" required>
            </div>
            <div>
                <label for="trang_thai">Trạng thái:</label>
                <select id="trang_thai" name="trang_thai" required>
                    <option value="Available" <?php echo ($trang_thai == 'Available') ? 'selected' : ''; ?>>Sẵn sàng (Available)</option>
                    <option value="Occupied" <?php echo ($trang_thai == 'Occupied') ? 'selected' : ''; ?>>Có khách (Occupied)</option>
                    <option value="Cleaning" <?php echo ($trang_thai == 'Cleaning') ? 'selected' : ''; ?>>Đang dọn (Cleaning)</option>
                    <option value="Maintenance" <?php echo ($trang_thai == 'Maintenance') ? 'selected' : ''; ?>>Bảo trì (Maintenance)</option>
                </select>
            </div>
            
            <button type="submit"><?php echo $is_editing ? 'Cập nhật' : 'Thêm mới'; ?></button>
            <?php if ($is_editing): ?>
                <a href="<?php echo $base_url; ?>" class="form-container button cancel">Hủy</a>
            <?php endif; ?>
        </form>
    </div>

    <h3>Danh sách Phòng Cụ Thể</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Số phòng</th>
                <th>Loại phòng</th>
                <th>Tầng</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_select && $result_select->num_rows > 0): ?>
                <?php while($row = $result_select->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['phong_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['so_phong']); ?></td>
                        <td><?php echo htmlspecialchars($row['ten_loai']); ?></td>
                        <td><?php echo $row['tang']; ?></td>
                        <td><?php echo htmlspecialchars($row['trang_thai']); ?></td>
                        <td>
                            <a href="<?php echo $base_url; ?>&action=edit_p&id=<?php echo $row['phong_id']; ?>" class="btn btn-success">Sửa</a>
                            <a href="<?php echo $base_url; ?>&action=delete_p&id=<?php echo $row['phong_id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa phòng này?');">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Chưa có phòng cụ thể nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>