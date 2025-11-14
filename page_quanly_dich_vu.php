<?php
// 1. KẾT NỐI DATABASE
if (!isset($conn)) {
    require_once 'connect_db.php';
}

// KHỞI TẠO BIẾN
$ten_dich_vu = "";
$gia = "";
$don_vi = "";
$dich_vu_id_update = 0;
$is_editing = false;
$message = "";

// URL cơ sở cho trang này
$base_url = "admin_index.php?page=services";

// 2. XỬ LÝ FORM (THÊM / CẬP NHẬT)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_dich_vu'])) {

    $ten_dich_vu = $_POST['ten_dich_vu'];
    $gia = $_POST['gia'];
    $don_vi = $_POST['don_vi'];
    
    if (isset($_POST['dich_vu_id_update']) && $_POST['dich_vu_id_update'] > 0) {
        // ----- CẬP NHẬT (UPDATE) -----
        $dich_vu_id = $_POST['dich_vu_id_update'];
        $sql = "UPDATE dich_vu SET ten_dich_vu = ?, gia = ?, don_vi = ? WHERE dich_vu_id = ?";
        $stmt = $conn->prepare($sql);
        // s: string, d: double/decimal, s: string, i: integer
        $stmt->bind_param("sdsi", $ten_dich_vu, $gia, $don_vi, $dich_vu_id);
        
        if ($stmt->execute()) {
            $message = "Cập nhật dịch vụ thành công!";
        } else {
            $message = "Lỗi khi cập nhật: " . $stmt->error;
        }
        $stmt->close();
        
        // Reset biến
        $is_editing = false;
        $dich_vu_id_update = 0;
        $ten_dich_vu = ""; $gia = ""; $don_vi = "";
        
    } else {
        // ----- THÊM MỚI (CREATE) -----
        $sql = "INSERT INTO dich_vu (ten_dich_vu, gia, don_vi) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        // s: string, d: double/decimal, s: string
        $stmt->bind_param("sds", $ten_dich_vu, $gia, $don_vi);
        
        if ($stmt->execute()) {
            $message = "Thêm dịch vụ mới thành công!";
            $ten_dich_vu = ""; $gia = ""; $don_vi = ""; // Reset form
        } else {
            $message = "Lỗi khi thêm mới: " . $stmt->error;
        }
        $stmt->close();
    }
}

// 3. XỬ LÝ XÓA (DELETE)
if (isset($_GET['action']) && $_GET['action'] == 'delete_dv' && isset($_GET['id'])) {
    $dich_vu_id = $_GET['id'];
    $sql = "DELETE FROM dich_vu WHERE dich_vu_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dich_vu_id);
    
    if ($stmt->execute()) {
        $message = "Xóa dịch vụ thành công!";
    } else {
        $message = "Lỗi khi xóa: " . $stmt->error . ". (Dịch vụ này có thể đã được sử dụng).";
    }
    $stmt->close();
}

// 4. XỬ LÝ SỬA (LẤY THÔNG TIN)
if (isset($_GET['action']) && $_GET['action'] == 'edit_dv' && isset($_GET['id'])) {
    $dich_vu_id_update = $_GET['id'];
    $is_editing = true;
    
    $sql = "SELECT * FROM dich_vu WHERE dich_vu_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dich_vu_id_update);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ten_dich_vu = $row['ten_dich_vu'];
        $gia = $row['gia'];
        $don_vi = $row['don_vi'];
    }
    $stmt->close();
}

// 5. LẤY DỮ LIỆU ĐỂ HIỂN THỊ (READ)
$sql_select = "SELECT * FROM dich_vu ORDER BY dich_vu_id DESC";
$result_select = $conn->query($sql_select);

?>

<div class="container-content">

    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'Lỗi') !== false ? 'error' : ''; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <h3><?php echo $is_editing ? 'Chỉnh sửa Dịch vụ' : 'Thêm Dịch vụ Mới'; ?></h3>
        <form action="<?php echo $base_url; ?>" method="POST">
            
            <input type="hidden" name="form_dich_vu" value="1">
            <input type="hidden" name="dich_vu_id_update" value="<?php echo $dich_vu_id_update; ?>">

            <div>
                <label for="ten_dich_vu">Tên dịch vụ:</label>
                <input type="text" id="ten_dich_vu" name="ten_dich_vu" value="<?php echo htmlspecialchars($ten_dich_vu); ?>" required>
            </div>
            <div>
                <label for="gia">Giá:</label>
                <input type="number" id="gia" name="gia" step="0.01" value="<?php echo htmlspecialchars($gia); ?>" required>
            </div>
            <div>
                <label for="don_vi">Đơn vị (ví dụ: lần, chai, kg...):</label>
                <input type="text" id="don_vi" name="don_vi" value="<?php echo htmlspecialchars($don_vi); ?>" required>
            </div>
            
            <button type="submit"><?php echo $is_editing ? 'Cập nhật' : 'Thêm mới'; ?></button>
            <?php if ($is_editing): ?>
                <a href="<?php echo $base_url; ?>" class="form-container button cancel">Hủy</a>
            <?php endif; ?>
        </form>
    </div>

    <h3>Danh sách Dịch vụ</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên dịch vụ</th>
                <th>Giá</th>
                <th>Đơn vị</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_select && $result_select->num_rows > 0): ?>
                <?php while($row = $result_select->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['dich_vu_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['ten_dich_vu']); ?></td>
                        <td><?php echo number_format($row['gia']); ?> VNĐ</td>
                        <td><?php echo htmlspecialchars($row['don_vi']); ?></td>
                        <td>
                            <a href="<?php echo $base_url; ?>&action=edit_dv&id=<?php echo $row['dich_vu_id']; ?>" class="btn btn-success">Sửa</a>
                            <a href="<?php echo $base_url; ?>&action=delete_dv&id=<?php echo $row['dich_vu_id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này?');">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Chưa có dịch vụ nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>